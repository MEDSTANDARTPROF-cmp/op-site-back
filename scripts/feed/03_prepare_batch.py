"""Готовит очередной батч курсов для AI-генерации планов.

Читает plans_needed.json и plans_generated.json, выдаёт следующие N курсов.

Usage:
    python 03_prepare_batch.py N
    → выводит JSON с метаданными для batch_input.json
"""
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from config import PLANS_NEEDED, PLANS_GENERATED, DATA


CATEGORY_LABELS = {
    1001: "строительство и ЖКХ",
    1003: "экономика, финансы, бухгалтерия",
    1004: "юриспруденция, госслужба",
    1005: "кадровое дело, делопроизводство, HR, охрана труда",
    1006: "здравоохранение, медицина",
    1007: "инженерия и производство",
    1008: "педагогика",
    1009: "социальная работа",
    1010: "транспорт и машиностроение",
    1011: "туризм и гостиничное дело",
    1012: "физическая культура и спорт",
    1013: "экология",
    1099: "профессиональная подготовка",
}


def main():
    n = int(sys.argv[1]) if len(sys.argv) > 1 else 20

    needed = json.loads(PLANS_NEEDED.read_text(encoding="utf-8"))
    already = {}
    if PLANS_GENERATED.exists():
        already = json.loads(PLANS_GENERATED.read_text(encoding="utf-8"))

    remaining = [c for c in needed if str(c["id"]) not in already]

    print(f"Всего в plans_needed:     {len(needed)}", file=sys.stderr)
    print(f"Уже сгенерировано:        {len(already)}", file=sys.stderr)
    print(f"Осталось:                 {len(remaining)}", file=sys.stderr)
    print(f"Беру в батч:              {min(n, len(remaining))}", file=sys.stderr)

    batch = remaining[:n]
    compact = []
    for c in batch:
        compact.append({
            "id": c["id"],
            "title": c["pagetitle"],
            "category": CATEGORY_LABELS.get(c["category_id"], "профессиональная подготовка"),
            "type": c["offer_type"],  # Курс или Профессия
            "hours": c["hours"],
        })

    # Пишем в файл и также в stdout
    out_file = DATA / "batch_input.json"
    out_file.write_text(json.dumps(compact, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"→ {out_file}", file=sys.stderr)
    print(json.dumps(compact, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
