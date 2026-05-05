"""Собирает финальный XML-фид для Яндекс.Вебмастера.

Вход:
- data/courses.json — все курсы с метаданными
- data/plans_from_content.json — планы, распарсенные из cont2Prog (если есть)
- data/plans_generated.json — AI-сгенерированные планы

Выход:
- data/output/poiskfeed2026.yml
"""
import html
import json
import re
import sys
from datetime import datetime
from pathlib import Path
from xml.sax.saxutils import escape as xml_escape

sys.path.insert(0, str(Path(__file__).parent))
from config import (
    COURSES_JSON, PLANS_FROM_CONTENT, PLANS_GENERATED, FEED_XML, SITE_URL
)

PLACEHOLDER_IMAGE = "/assets/images/temp/002.jpg"  # fallback


def xe(s: str) -> str:
    """XML-escape + strip control chars."""
    if s is None:
        s = ""
    # Убираем управляющие символы
    s = re.sub(r'[\x00-\x08\x0b\x0c\x0e-\x1f]', '', str(s))
    return xml_escape(s, {'"': "&quot;", "'": "&apos;"})


def clean_text(s: str) -> str:
    """Очищает текст от HTML, лишних пробелов, кавычек."""
    if not s:
        return ""
    s = html.unescape(s)
    s = re.sub(r'<[^>]+>', ' ', s)
    s = re.sub(r'\s+', ' ', s).strip()
    # Убираем запрещённые Яндексом символы в offer content
    s = s.replace('"', '«').replace("'", "")
    return s


def build_url(course: dict) -> str:
    uri = (course.get("uri") or "").lstrip("/")
    # Amp'ы в query заменяем на &amp; (xe делает это через xml_escape)
    return f"{SITE_URL}/{uri}?utm_source=yfeed"


def build_image(course: dict) -> str:
    img = (course.get("image") or "").strip()
    if not img:
        img = PLACEHOLDER_IMAGE
    # абсолютный URL
    if not img.startswith("http"):
        img = f"{SITE_URL}/{img.lstrip('/')}"
    return img


def extract_core_title(title: str) -> str:
    """Извлекает суть курса из шаблонного названия.

    "Обучение «Начальник службы пожарной безопасности»: курсы профпереподготовки..."
      → "Начальник службы пожарной безопасности"
    "Курс «Директор пожарной части»: повышение квалификации..."
      → "Директор пожарной части"
    Если шаблон не распознан — возвращаем обрезанное название.
    """
    # Паттерн с кавычками
    m = re.match(r'^(?:Обучение|Курс(?:ы)?)\s*[«"„]([^»"“]+)[»"“]', title)
    if m:
        return m.group(1).strip()
    # Без кавычек: "Обучение по X" / "Курс по X"
    m = re.match(r'^(?:Обучение|Курс(?:ы)?)\s+(?:по|на|для)\s+(.{5,120}?)(?:\.|,|:|$)', title)
    if m:
        return m.group(1).strip()
    # Фолбэк: обрезаем хвост с "дистанционно" / "с выдачей"
    t = re.split(r'[.,:]\s*(?:дистанц|с выдачей|повышение квал|курс[ыа])', title, maxsplit=1)[0]
    return t.strip()[:180]


def build_description(course: dict) -> str:
    """Короткое описание для сниппета (~300 символов)."""
    core = extract_core_title(course["pagetitle"])
    rtype = course["result_type"].lower()
    hours = course["hours"]
    cat_label = {
        1001: "строительству и ЖКХ",
        1003: "экономике и бухгалтерии",
        1004: "юриспруденции",
        1005: "кадровому делу и охране труда",
        1006: "медицине и здравоохранению",
        1007: "инженерии и производству",
        1008: "педагогике",
        1009: "социальной работе",
        1010: "транспорту и машиностроению",
        1011: "туризму и гостиничному делу",
        1012: "физической культуре и спорту",
        1013: "экологии",
        1099: "профессиональной подготовке",
    }.get(course["category_id"], "профессиональной подготовке")

    desc = (
        f"«{core}» — дистанционный курс по {cat_label}. "
        f"{hours} академических часов, онлайн, без отрыва от работы. "
        f"По итогам — {rtype} установленного образца с внесением в ФИС ФРДО."
    )
    return clean_text(desc)[:500]


def build_plan_blocks(course: dict, plans_parsed: dict, plans_generated: dict) -> list[dict]:
    """Возвращает 3–5 блоков плана для курса."""
    cid = str(course["id"])
    if cid in plans_parsed and len(plans_parsed[cid]) >= 3:
        return plans_parsed[cid][:5]
    if cid in plans_generated and len(plans_generated[cid]) >= 3:
        return plans_generated[cid][:5]
    # Фолбэк — дефолтный план (лучше чем ничего)
    return default_plan(course)


def default_plan(course: dict) -> list[dict]:
    """Уникализированный шаблонный план на случай отсутствия сгенерированного."""
    title = course["pagetitle"]
    hours = course["hours"]
    h1 = max(round(hours * 0.25), 8)
    h2 = max(round(hours * 0.45), 16)
    h3 = hours - h1 - h2

    return [
        {
            "order": 1,
            "title": "Нормативно-правовая база",
            "hours": h1,
            "body": (
                f"Актуальные федеральные законы, ГОСТы и профстандарты по теме «{title}». "
                "Требования Минтруда и Ростехнадзора, изменения законодательства."
            ),
        },
        {
            "order": 2,
            "title": "Основная программа",
            "hours": h2,
            "body": (
                "Профессиональные методики, практические кейсы, алгоритмы действий, "
                "типовые ошибки и их предотвращение, разбор инцидентов из практики."
            ),
        },
        {
            "order": 3,
            "title": "Итоговая аттестация и выдача документа",
            "hours": h3,
            "body": (
                f"Тестирование по программе курса. Выдача {course['result_type'].lower()}а "
                "установленного образца, внесение в ФИС ФРДО."
            ),
        },
    ]


def build_sets_xml(sets: list[dict]) -> str:
    parts = []
    for s in sets:
        parts.append(
            f'        <set id="set_{s["id"]}">\n'
            f'            <name>{xe(s["name"])}</name>\n'
            f'            <url>{xe(s["url"])}</url>\n'
            f'        </set>'
        )
    return "\n".join(parts)


def build_offer_xml(c: dict, plans_parsed: dict, plans_generated: dict) -> str:
    """Формирует один <offer>."""
    cid = c["id"]
    name = clean_text(c["pagetitle"])
    url = build_url(c)
    image = build_image(c)
    description = build_description(c)

    # Цены: base — старая (зачёркнутая), discount — новая (жирная)
    price_base = c.get("price_base", 0) or 0
    price_discount = c.get("price_discount", 0) or 0

    # Если только одна цена известна — используем её как price
    if price_base and price_discount:
        main_price = price_base  # старая = цена без скидки
        discount = price_discount  # новая = цена по скидке
    elif price_discount:
        main_price, discount = price_discount, 0
    elif price_base:
        main_price, discount = price_base, 0
    else:
        main_price, discount = 14900, 0  # дефолт

    hours = c["hours"]
    category_id = c["category_id"]
    offer_type = c["offer_type"]
    result_type = c["result_type"]
    set_id = c.get("set_id", c["root_id"])

    plan_blocks = build_plan_blocks(c, plans_parsed, plans_generated)
    plan_xml_parts = []
    for b in plan_blocks[:5]:
        order = b["order"]
        title_block = xe(clean_text(b.get("title", f"Раздел {order}")))[:180]
        body = xe(clean_text(b.get("body", "")))[:700]
        h = b.get("hours") or max(hours // len(plan_blocks), 4)
        plan_xml_parts.append(
            f'    <param name="План" order="{order}" unit="{title_block}" hours="{h}">'
            f'<![CDATA[ {body} ]]></param>'
        )
    plan_xml = "\n".join(plan_xml_parts)

    # Дополнительные параметры
    discount_line = (
        f'\n    <param name="Цена по скидке">{discount}</param>' if discount else ""
    )

    xml = f"""<offer id="{cid}">
    <name>{xe(name)[:300]}</name>
    <url>{xe(url)}</url>
    <set-ids>set_{set_id}</set-ids>
    <categoryId>{category_id}</categoryId>
    <description>{xe(description)}</description>
    <picture>{xe(image)}</picture>
    <price>{main_price}</price>
    <currencyId>RUB</currencyId>{discount_line}
    <param name="Оплата в рассрочку" unit="месяц">12</param>
    <param name="Продолжительность" unit="час">{hours}</param>
    <param name="Часы в неделю">{min(20, max(hours // 4, 4))}</param>
    <param name="Формат обучения">Самостоятельно</param>
    <param name="Тип обучения">{offer_type}</param>
    <param name="Сложность">Для опытных</param>
    <param name="Результат обучения">{result_type}</param>
    <param name="Есть текстовые уроки">true</param>
    <param name="Есть видеоуроки">true</param>
    <param name="Есть тренажеры">true</param>
    <param name="Есть домашние работы">true</param>
    <param name="Есть бесплатная часть">false</param>
{plan_xml}
</offer>"""
    return xml


def main():
    print("Загрузка данных...")
    data = json.loads(COURSES_JSON.read_text(encoding="utf-8"))
    courses = data["courses"]
    sets = data["sets"]

    plans_parsed = {}
    if PLANS_FROM_CONTENT.exists():
        plans_parsed = json.loads(PLANS_FROM_CONTENT.read_text(encoding="utf-8"))

    plans_generated = {}
    if PLANS_GENERATED.exists():
        plans_generated = json.loads(PLANS_GENERATED.read_text(encoding="utf-8"))

    print(f"Курсов:              {len(courses)}")
    print(f"Сетов:               {len(sets)}")
    print(f"Распарсенных планов: {len(plans_parsed)}")
    print(f"AI-планов:           {len(plans_generated)}")

    with_real_plan = 0
    with_fallback = 0
    for c in courses:
        cid = str(c["id"])
        if cid in plans_parsed or cid in plans_generated:
            with_real_plan += 1
        else:
            with_fallback += 1
    print(f"  С реальным планом:   {with_real_plan}")
    print(f"  С fallback-планом:   {with_fallback}")

    print("\nГенерация XML...")
    now = datetime.now().strftime("%Y-%m-%dT%H:%M:%S%z") or datetime.now().strftime("%Y-%m-%dT%H:%M:%S+03:00")

    offers_xml = "\n".join(
        build_offer_xml(c, plans_parsed, plans_generated) for c in courses
    )
    sets_xml = build_sets_xml(sets)

    xml = f"""<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<yml_catalog date="{now}">
    <shop>
        <name>ОБРПРОФИ</name>
        <company>ОБРПРОФИ</company>
        <url>{SITE_URL}/</url>
        <email>info@obrprofi.ru</email>
        <picture>{SITE_URL}/assets/icon/obr.png</picture>
        <description>Каталог дистанционных курсов повышения квалификации, переподготовки и обучения рабочим профессиям</description>
        <currencies>
            <currency id="RUB" rate="1"/>
        </currencies>
        <sets>
{sets_xml}
        </sets>
        <offers>
{offers_xml}
        </offers>
    </shop>
</yml_catalog>
"""

    FEED_XML.write_text(xml, encoding="utf-8")
    size_kb = FEED_XML.stat().st_size / 1024
    print(f"\n[OK] Фид записан: {FEED_XML}")
    print(f"     Размер: {size_kb:.1f} KB ({size_kb/1024:.2f} MB)")
    print(f"     Офферов: {len(courses)}")


if __name__ == "__main__":
    main()
