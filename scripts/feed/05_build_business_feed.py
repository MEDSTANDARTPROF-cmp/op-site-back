"""Собирает YML-фид для Яндекс Бизнес (Справочник) / Маркет.

Формат отличается от образовательного фида Вебмастера:
- Обычные товарные поля (name, price, picture, description, vendor)
- Дерево категорий <categories> с parentId
- Никаких <param name="План"> и специальных образовательных параметров
- Атрибут available="true" у offer

Вход:
- data/courses.json — все курсы с метаданными

Выход:
- data/output/business_catalog.yml
"""
import html
import json
import re
import sys
from datetime import datetime, timezone, timedelta
from pathlib import Path
from xml.sax.saxutils import escape as xml_escape

sys.path.insert(0, str(Path(__file__).parent))
from config import COURSES_JSON, OUTPUT, SITE_URL

FEED_XML = OUTPUT / "business_catalog.yml"
PLACEHOLDER_IMAGE = "/assets/images/temp/002.jpg"

# Верхний уровень дерева категорий
ROOT_CATEGORY_ID = 1000
ROOT_CATEGORY_NAME = "Курсы и обучение"

# ID для типов в дереве (НЕ совпадают с modx root_id — чтобы не было коллизии с set_id=63)
TYPE_IDS = {
    46: 1001,  # Повышение квалификации
    61: 1002,  # Профессиональная переподготовка
    63: 1003,  # Рабочие профессии
}

TYPE_NAMES = {
    1001: "Повышение квалификации",
    1002: "Профессиональная переподготовка",
    1003: "Рабочие профессии",
}

# Фраза для описания по root_id (для граммотного предложения)
ROOT_DESCRIPTION_PHRASE = {
    46: "курс повышения квалификации",
    61: "программа профессиональной переподготовки",
    63: "программа обучения рабочей специальности",
}


def xe(s: str) -> str:
    """XML-escape + удаление управляющих символов."""
    if s is None:
        s = ""
    s = re.sub(r'[\x00-\x08\x0b\x0c\x0e-\x1f]', '', str(s))
    return xml_escape(s, {'"': "&quot;", "'": "&apos;"})


def clean_text(s: str) -> str:
    """Чистка текста от HTML и лишних пробелов."""
    if not s:
        return ""
    s = html.unescape(s)
    s = re.sub(r'<[^>]+>', ' ', s)
    s = re.sub(r'\s+', ' ', s).strip()
    return s


def extract_core_title(title: str) -> str:
    """Извлекает короткое название курса из шаблонного заголовка."""
    m = re.match(r'^(?:Обучение|Курс(?:ы)?)\s*[«"„]([^»"“]+)[»"“]', title)
    if m:
        return m.group(1).strip()
    m = re.match(r'^(?:Обучение|Курс(?:ы)?)\s+(?:по|на|для)\s+(.{5,120}?)(?:\.|,|:|$)', title)
    if m:
        return m.group(1).strip()
    t = re.split(r'[.,:]\s*(?:дистанц|с выдачей|повышение квал|курс[ыа])', title, maxsplit=1)[0]
    return t.strip()[:180]


def build_description(course: dict) -> str:
    """Полное описание курса для каталога (до 3000 символов)."""
    core = extract_core_title(course["pagetitle"])
    hours = course["hours"]
    rtype = course["result_type"]
    phrase = ROOT_DESCRIPTION_PHRASE.get(course["root_id"], "программа обучения")

    parts = [
        f"«{core}» — {phrase} в дистанционном формате.",
        f"Объём: {hours} академических часов.",
        "Обучение проходит онлайн, без отрыва от работы. Материалы доступны 24/7 "
        "с любого устройства — вы сами выбираете удобное время для занятий.",
        f"По итогам обучения выдаётся {rtype.lower()} установленного образца "
        "с внесением сведений в ФИС ФРДО (федеральный реестр документов об образовании).",
        "Документ действителен на всей территории Российской Федерации и принимается "
        "проверяющими органами (Ростехнадзор, МЧС, Роструд, Росприроднадзор и др.).",
    ]

    # Если в cont2Prog есть осмысленный фрагмент «Кому необходимо» — добавляем
    cp = clean_text(course.get("cont2Prog", ""))
    if cp and len(cp) > 50:
        parts.append(cp[:800])

    desc = " ".join(parts)
    return desc[:2990]  # запас до лимита 3000


def build_url(course: dict) -> str:
    uri = (course.get("uri") or "").lstrip("/")
    # Отдельный UTM, чтобы отличать переходы из Справочника
    return f"{SITE_URL}/{uri}?utm_source=ybusiness"


def build_picture(course: dict) -> str:
    img = (course.get("image") or "").strip()
    if not img:
        img = PLACEHOLDER_IMAGE
    if not img.startswith("http"):
        img = f"{SITE_URL}/{img.lstrip('/')}"
    return img


def build_categories_xml(sets: list[dict]) -> str:
    """Строит дерево категорий:
    1000 Курсы и обучение
      1001 Повышение квалификации
        {set_id} {set_name}
      1002 Профессиональная переподготовка
        {set_id} {set_name}
      1003 Рабочие профессии
        {set_id} {set_name}
    """
    lines = []
    # Корневая категория
    lines.append(f'            <category id="{ROOT_CATEGORY_ID}">{xe(ROOT_CATEGORY_NAME)}</category>')

    # Типы обучения
    for type_id, type_name in TYPE_NAMES.items():
        lines.append(
            f'            <category id="{type_id}" parentId="{ROOT_CATEGORY_ID}">{xe(type_name)}</category>'
        )

    # Сеты (раскладываем по родительским типам по URL сета)
    for s in sets:
        sid = s["id"]
        name = s["name"]
        url = s.get("url", "")
        if "/povyshenie-kvalifikacii/" in url:
            parent = TYPE_IDS[46]
        elif "/perepodgotovka/" in url:
            parent = TYPE_IDS[61]
        elif "/rabochie-spetsialnosti/" in url:
            parent = TYPE_IDS[63]
        else:
            parent = ROOT_CATEGORY_ID
        lines.append(
            f'            <category id="{sid}" parentId="{parent}">{xe(name)}</category>'
        )

    return "\n".join(lines)


def build_offer_xml(course: dict) -> str:
    oid = course["id"]
    name = clean_text(course["pagetitle"])
    # Ограничиваем длину имени (для Маркета ~300, но для Бизнеса рекомендуется короче)
    name = name[:250]
    url = build_url(course)
    picture = build_picture(course)
    description = build_description(course)
    vendor = "ОБРПРОФИ"

    price_base = course.get("price_base", 0) or 0
    price_discount = course.get("price_discount", 0) or 0
    if price_base and price_discount:
        price = price_discount
        oldprice = price_base
    elif price_discount:
        price, oldprice = price_discount, 0
    elif price_base:
        price, oldprice = price_base, 0
    else:
        # Дефолты: переподготовка дороже повышения, рабочие специальности отдельно
        defaults = {46: 6900, 61: 11900, 63: 29900}
        price = defaults.get(course["root_id"], 9900)
        oldprice = 0

    category_id = course.get("set_id", course["root_id"])

    sales_notes = f"Обучение {course['hours']} ак.часов. Удостоверение/диплом с внесением в ФИС ФРДО."

    oldprice_line = f"\n                <oldprice>{oldprice}</oldprice>" if oldprice else ""

    xml = (
        f'            <offer id="{oid}" available="true">\n'
        f'                <name>{xe(name)}</name>\n'
        f'                <url>{xe(url)}</url>\n'
        f'                <price>{price}</price>{oldprice_line}\n'
        f'                <currencyId>RUB</currencyId>\n'
        f'                <categoryId>{category_id}</categoryId>\n'
        f'                <picture>{xe(picture)}</picture>\n'
        f'                <vendor>{xe(vendor)}</vendor>\n'
        f'                <description><![CDATA[{description}]]></description>\n'
        f'                <sales_notes>{xe(sales_notes)}</sales_notes>\n'
        f'            </offer>'
    )
    return xml


def main():
    print("Загрузка данных...")
    data = json.loads(COURSES_JSON.read_text(encoding="utf-8"))
    courses = data["courses"]
    sets = data["sets"]

    print(f"Курсов: {len(courses)}")
    print(f"Сетов:  {len(sets)}")

    # Дата в формате RFC 3339 с московским поясом
    tz_msk = timezone(timedelta(hours=3))
    now = datetime.now(tz_msk).strftime("%Y-%m-%dT%H:%M:%S%z")
    # Добавляем двоеточие в смещение (RFC 3339)
    now = now[:-2] + ":" + now[-2:]

    print("\nГенерация XML...")
    categories_xml = build_categories_xml(sets)
    offers_xml = "\n".join(build_offer_xml(c) for c in courses)

    xml = f"""<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="{now}">
    <shop>
        <name>ОБРПРОФИ</name>
        <company>ОБРПРОФИ</company>
        <url>{SITE_URL}/</url>
        <platform>MODX Revolution</platform>
        <currencies>
            <currency id="RUB" rate="1"/>
        </currencies>
        <categories>
{categories_xml}
        </categories>
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
    print(f"     Категорий в дереве: {1 + len(TYPE_NAMES) + len(sets)}")


if __name__ == "__main__":
    main()
