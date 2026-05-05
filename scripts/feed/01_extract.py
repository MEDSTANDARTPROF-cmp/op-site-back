"""Извлекает все курсы из БД obrprofi и собирает метаданные в courses.json.

Что собираем на курс:
- id, pagetitle, uri (полный путь), root_id, ancestors
- price, old_price (из TV priceAll/priceOld или modx_ms2_products)
- image (TV offerImg или ms2 image, с фолбэком)
- cont2Prog (HTML программы обучения, если есть)
- parsed_hours (часы, парсим из pagetitle/content)
- category_id (Yandex rubricator)
- offer_type, result_type
- set_id (id подкатегории первого уровня, для <set-ids>)
"""
import json
import re
import sys
from pathlib import Path

import pymysql

sys.path.insert(0, str(Path(__file__).parent))
from config import DB, ROOT_CATEGORIES, COURSE_TEMPLATES, EXCLUDE_TITLE_KEYWORDS, EXCLUDE_IDS, COURSES_JSON, SITE_URL
from category_mapping import get_category_id, get_offer_type_and_result


def connect():
    return pymysql.connect(**DB, cursorclass=pymysql.cursors.DictCursor)


def fetch_courses(conn) -> list[dict]:
    """Рекурсивный CTE — все курсы, чей какой-либо предок есть в ROOT_CATEGORIES."""
    roots_placeholder = ",".join(str(r) for r in ROOT_CATEGORIES)
    tpl_placeholder = ",".join(str(t) for t in COURSE_TEMPLATES)

    sql = f"""
    WITH RECURSIVE up AS (
        SELECT id, pagetitle, parent, id as course_id, 0 as lvl,
               CAST(id AS CHAR(500)) as chain
        FROM modx_site_content
        WHERE class_key='msProduct'
          AND template IN ({tpl_placeholder})
          AND deleted=0 AND published=1
        UNION ALL
        SELECT c.id, c.pagetitle, c.parent, u.course_id, u.lvl+1,
               CONCAT(u.chain, ',', c.id)
        FROM modx_site_content c
        JOIN up u ON c.id=u.parent
        WHERE u.parent > 0 AND u.lvl < 10
    )
    SELECT DISTINCT
        u.course_id as id,
        u.id as direct_child_of_root,
        u.parent as root_id,
        (SELECT pagetitle FROM modx_site_content WHERE id=u.course_id) as pagetitle,
        (SELECT alias FROM modx_site_content WHERE id=u.course_id) as alias,
        (SELECT parent FROM modx_site_content WHERE id=u.course_id) as parent,
        (SELECT template FROM modx_site_content WHERE id=u.course_id) as template,
        (SELECT uri FROM modx_site_content WHERE id=u.course_id) as uri,
        u.chain as chain
    FROM up u
    WHERE u.parent IN ({roots_placeholder})
    ORDER BY u.course_id
    """
    with conn.cursor() as cur:
        cur.execute(sql)
        rows = cur.fetchall()

    courses = []
    for r in rows:
        chain_ids = [int(x) for x in r["chain"].split(",")]
        # chain идёт: [course_id, ..., direct_child_of_root]
        # root_id = r["root_id"] (46, 61 или 63)
        # ancestors = всё кроме самого курса
        ancestors = chain_ids[1:]
        # set_id = последний элемент цепочки (прямой child корня)
        # для курсов прямо под root (63 в основном) — это сам курс, тогда берём root_id
        set_id = r["direct_child_of_root"]
        if set_id == r["id"]:  # курс сидит прямо под root
            set_id = r["root_id"]

        courses.append({
            "id": r["id"],
            "pagetitle": r["pagetitle"],
            "alias": r["alias"],
            "parent": r["parent"],
            "template": r["template"],
            "uri": r["uri"],
            "root_id": r["root_id"],
            "ancestors": ancestors,
            "set_id": set_id,
        })
    return courses


def fetch_ms2_data(conn, ids: list[int]) -> dict:
    """price, old_price, image из modx_ms2_products."""
    if not ids:
        return {}
    placeholders = ",".join(str(i) for i in ids)
    sql = f"""
    SELECT id, price, old_price, image
    FROM modx_ms2_products
    WHERE id IN ({placeholders})
    """
    with conn.cursor() as cur:
        cur.execute(sql)
        return {r["id"]: r for r in cur.fetchall()}


def fetch_tv_values(conn, ids: list[int], tv_ids: list[int]) -> dict:
    """{course_id: {tv_id: value}}"""
    if not ids or not tv_ids:
        return {}
    ids_ph = ",".join(str(i) for i in ids)
    tv_ph = ",".join(str(i) for i in tv_ids)
    sql = f"""
    SELECT contentid, tmplvarid, value
    FROM modx_site_tmplvar_contentvalues
    WHERE contentid IN ({ids_ph}) AND tmplvarid IN ({tv_ph})
      AND value IS NOT NULL AND value <> ''
    """
    out: dict[int, dict[int, str]] = {}
    with conn.cursor() as cur:
        cur.execute(sql)
        for r in cur.fetchall():
            out.setdefault(r["contentid"], {})[r["tmplvarid"]] = r["value"]
    return out


def fetch_subcategory_names(conn, ids: list[int]) -> dict:
    if not ids:
        return {}
    placeholders = ",".join(str(i) for i in ids)
    with conn.cursor() as cur:
        cur.execute(f"SELECT id, pagetitle, menutitle FROM modx_site_content WHERE id IN ({placeholders})")
        return {r["id"]: (r["menutitle"] or r["pagetitle"]) for r in cur.fetchall()}


def parse_hours(title: str, content: str = "") -> int:
    """Извлекает кол-во часов из названия или контента."""
    text = f"{title}\n{content}"
    # "256 часов", "72 ч", "256 ак.ч"
    m = re.search(r'(\d{2,4})\s*(час|ч\b|ак)', text, re.IGNORECASE)
    if m:
        h = int(m.group(1))
        if 16 <= h <= 3000:  # разумный диапазон
            return h
    return 72  # дефолт по ДПО


def clean_price(raw) -> int:
    """Нормализует цену в int."""
    if raw is None or raw == "":
        return 0
    if isinstance(raw, (int, float)):
        return int(raw)
    s = str(raw).replace(" ", "").replace("\xa0", "").replace(",", ".")
    try:
        return int(float(s))
    except (ValueError, TypeError):
        return 0


def should_exclude(course: dict) -> bool:
    if course["id"] in EXCLUDE_IDS:
        return True
    low = course["pagetitle"].lower()
    return any(kw.lower() in low for kw in EXCLUDE_TITLE_KEYWORDS)


def main():
    conn = connect()
    print(f"[1/5] Получаю список курсов из {ROOT_CATEGORIES}...")
    courses = fetch_courses(conn)
    before = len(courses)
    courses = [c for c in courses if not should_exclude(c)]
    print(f"    Найдено {before}, после исключений {len(courses)}")

    ids = [c["id"] for c in courses]

    print(f"[2/5] Читаю ms2_products...")
    ms2 = fetch_ms2_data(conn, ids)

    print(f"[3/5] Читаю TV (priceAll=51, priceOld=52, offerImg=6, img=1, cont2Prog=17, offerDescription=2)...")
    tvs = fetch_tv_values(conn, ids, [51, 52, 6, 1, 17, 2])

    print(f"[4/5] Читаю контент страниц (для парсинга часов)...")
    content_by_id: dict[int, str] = {}
    with conn.cursor() as cur:
        placeholders = ",".join(str(i) for i in ids)
        cur.execute(f"SELECT id, content FROM modx_site_content WHERE id IN ({placeholders})")
        for r in cur.fetchall():
            content_by_id[r["id"]] = r["content"] or ""

    # subcategory names для <set> блоков
    sub_ids = list({c["set_id"] for c in courses})
    sub_names = fetch_subcategory_names(conn, sub_ids)

    print(f"[5/5] Собираю метаданные курсов...")
    result = []
    subcategories = {}  # для последующих <set>
    for c in courses:
        cid = c["id"]
        ms2_row = ms2.get(cid, {})
        tv = tvs.get(cid, {})

        # Цена: приоритет TV priceAll > ms2.old_price; скидка: TV priceOld > ms2.price
        price_new = clean_price(tv.get(51)) or clean_price(ms2_row.get("old_price"))
        price_discount = clean_price(tv.get(52)) or clean_price(ms2_row.get("price"))
        # если скидки нет, оставляем только base
        if price_discount and price_new and price_discount >= price_new:
            price_discount = 0

        # Картинка
        image = tv.get(6) or tv.get(1) or ms2_row.get("image") or ""

        # cont2Prog — оригинальный HTML программы курса
        cont_prog = tv.get(17, "")
        offer_desc = tv.get(2, "")

        # Часы
        hours = parse_hours(c["pagetitle"], content_by_id.get(cid, "")[:2000])

        # Yandex categoryId
        category_id = get_category_id(c)

        # Тип и результат
        offer_type, result_type = get_offer_type_and_result(c)

        course_data = {
            **c,
            "price_base": price_new if price_new else 0,
            "price_discount": price_discount if price_discount else 0,
            "image": image,
            "cont2Prog": cont_prog,
            "offer_description": offer_desc,
            "hours": hours,
            "category_id": category_id,
            "offer_type": offer_type,
            "result_type": result_type,
        }
        result.append(course_data)

        # Собираем <set>
        sid = c["set_id"]
        if sid not in subcategories:
            subcategories[sid] = {
                "id": sid,
                "name": sub_names.get(sid, f"Категория {sid}"),
                "url": f"{SITE_URL}/",  # заполним позже через uri
            }

    # Обновим URL подкатегорий
    with conn.cursor() as cur:
        sub_ids_str = ",".join(str(s) for s in subcategories)
        if sub_ids_str:
            cur.execute(f"SELECT id, uri FROM modx_site_content WHERE id IN ({sub_ids_str})")
            for r in cur.fetchall():
                subcategories[r["id"]]["url"] = f"{SITE_URL}/{r['uri']}?utm_source=yfeed"

    out = {"courses": result, "sets": list(subcategories.values())}
    COURSES_JSON.write_text(json.dumps(out, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"\n[OK] Сохранено {len(result)} курсов и {len(subcategories)} сетов в {COURSES_JSON}")

    # статистика
    by_root: dict[int, int] = {}
    by_yandex_cat: dict[int, int] = {}
    for c in result:
        by_root[c["root_id"]] = by_root.get(c["root_id"], 0) + 1
        by_yandex_cat[c["category_id"]] = by_yandex_cat.get(c["category_id"], 0) + 1

    print("\nПо корням:")
    for k, v in sorted(by_root.items()):
        print(f"  {k}: {v}")
    print("\nПо Yandex categoryId:")
    for k, v in sorted(by_yandex_cat.items(), key=lambda x: -x[1]):
        print(f"  {k}: {v}")


if __name__ == "__main__":
    main()
