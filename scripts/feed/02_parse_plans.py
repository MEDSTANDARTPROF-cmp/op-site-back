"""Парсит HTML из TV cont2Prog у курсов и извлекает структурированные планы обучения.

Ищем в HTML блоки вида:
- <h3>Блок 1. Название</h3> + <ul><li>пункт</li>...
- <h3>Модуль 1</h3> + список
- нумерованные заголовки

Если нашли >=3 блока — используем.
Если нет — в plans_needed.json для AI-генерации.
"""
import json
import re
import sys
from html import unescape
from pathlib import Path

from bs4 import BeautifulSoup

sys.path.insert(0, str(Path(__file__).parent))
from config import COURSES_JSON, PLANS_FROM_CONTENT, PLANS_NEEDED


# Маркеры заголовков блоков в русском HTML
BLOCK_HEADER_RE = re.compile(
    r'(блок|модуль|раздел|тема|часть|глава)\s*[№#]?\s*(\d+)[\.\):\s]+(.{3,180})',
    re.IGNORECASE
)


def strip_html(s: str) -> str:
    """Удаляет HTML-теги, оставляя текст."""
    if not s:
        return ""
    s = BeautifulSoup(s, "html.parser").get_text(" ", strip=True)
    return " ".join(s.split())


def parse_cont2prog(html: str) -> list[dict]:
    """Извлекает блоки программы из HTML cont2Prog.

    Возвращает список dict: {order, title, body}
    """
    if not html or len(html) < 50:
        return []

    soup = BeautifulSoup(html, "html.parser")

    # Стратегия 1: ищем заголовки h2/h3/h4 со словом "блок/модуль/раздел N"
    blocks: list[dict] = []
    headers = soup.find_all(["h2", "h3", "h4", "h5", "p", "div"])

    current_block: dict | None = None
    for el in headers:
        text = el.get_text(" ", strip=True)
        if not text:
            continue

        m = BLOCK_HEADER_RE.search(text[:200])
        if m and el.name in ("h2", "h3", "h4", "h5") or (m and el.name == "p" and len(text) < 150):
            # Это заголовок блока — стартуем новый
            if current_block and current_block.get("body"):
                blocks.append(current_block)
            order = int(m.group(2))
            title = m.group(3).strip().rstrip(":.,;").strip()
            # если в тексте есть больше после заголовка — добавим к body
            current_block = {
                "order": order,
                "title": title[:200],
                "body_parts": [],
            }
        elif current_block:
            # Собираем тело блока: ul/ol/p
            if el.name in ("ul", "ol"):
                items = [li.get_text(" ", strip=True) for li in el.find_all("li")]
                items = [x for x in items if x and len(x) > 3]
                if items:
                    current_block["body_parts"].extend(items[:8])
            elif el.name == "p":
                t = el.get_text(" ", strip=True)
                if t and len(t) > 15 and len(current_block.get("body_parts", [])) < 6:
                    current_block["body_parts"].append(t[:300])

    if current_block and current_block.get("body_parts"):
        blocks.append(current_block)

    # Финализация — склеиваем body из частей
    result = []
    for b in blocks:
        body = " • ".join(b.get("body_parts", []))
        if len(body) < 20:  # слишком короткий блок — пропускаем
            continue
        result.append({
            "order": b["order"],
            "title": b["title"],
            "body": body[:800],
        })

    # Если несколько блоков с одинаковым order — переиндексируем
    if result:
        result.sort(key=lambda x: x["order"])
        for i, b in enumerate(result, 1):
            b["order"] = i

    return result


def main():
    data = json.loads(COURSES_JSON.read_text(encoding="utf-8"))
    courses = data["courses"]

    parsed: dict[int, list[dict]] = {}
    needed: list[dict] = []

    stats = {"has_cont2prog": 0, "parsed_3plus": 0, "parsed_less": 0, "empty": 0}

    for c in courses:
        cid = c["id"]
        html = c.get("cont2Prog", "") or ""

        if not html or len(html) < 80:
            stats["empty"] += 1
            needed.append({
                "id": cid,
                "pagetitle": c["pagetitle"],
                "category_id": c["category_id"],
                "offer_type": c["offer_type"],
                "hours": c["hours"],
            })
            continue

        stats["has_cont2prog"] += 1
        blocks = parse_cont2prog(html)

        if len(blocks) >= 3:
            stats["parsed_3plus"] += 1
            parsed[cid] = blocks[:5]  # не более 5 блоков
        else:
            stats["parsed_less"] += 1
            needed.append({
                "id": cid,
                "pagetitle": c["pagetitle"],
                "category_id": c["category_id"],
                "offer_type": c["offer_type"],
                "hours": c["hours"],
            })

    PLANS_FROM_CONTENT.write_text(
        json.dumps(parsed, ensure_ascii=False, indent=2), encoding="utf-8"
    )
    PLANS_NEEDED.write_text(
        json.dumps(needed, ensure_ascii=False, indent=2), encoding="utf-8"
    )

    print(f"Всего курсов:                 {len(courses)}")
    print(f"  С заполненным cont2Prog:    {stats['has_cont2prog']}")
    print(f"    Распарсено 3+ блока:      {stats['parsed_3plus']}")
    print(f"    Распарсено меньше 3:      {stats['parsed_less']}")
    print(f"  Без cont2Prog:              {stats['empty']}")
    print()
    print(f"→ plans_from_content.json: {len(parsed)} курсов с готовыми планами")
    print(f"→ plans_needed.json:       {len(needed)} курсов для AI-генерации")


if __name__ == "__main__":
    main()
