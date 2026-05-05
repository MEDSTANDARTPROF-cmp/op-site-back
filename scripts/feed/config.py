"""Настройки для генерации фида obrprofi.ru → Яндекс.Вебмастер."""
from pathlib import Path

# БД (локальный Laragon)
DB = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "obrprofi",
    "charset": "utf8mb4",
}

SITE_URL = "https://obrprofi.ru"

# Корни из которых берём курсы (ДПО-комплекты root=4860 исключены — они B2B)
ROOT_CATEGORIES = [46, 61, 63]

# Шаблоны, которые считаем реальными курсами
COURSE_TEMPLATES = [22, 26, 21]

# Исключения по ID (отдельные курсы, которые не должны идти в фид Яндекса)
# id 5455 "Курс по маркетингу" — не относится к образовательной тематике фида
EXCLUDE_IDS = {5455}

# Исключения по ключевым словам (пусто — 4 курса «гостиничного маркетинга» оставляем в туризме)
EXCLUDE_TITLE_KEYWORDS: list[str] = []

# Пути
ROOT = Path(__file__).parent
DATA = ROOT / "data"
OUTPUT = DATA / "output"

COURSES_JSON = DATA / "courses.json"
PLANS_FROM_CONTENT = DATA / "plans_from_content.json"
PLANS_NEEDED = DATA / "plans_needed.json"
PLANS_GENERATED = DATA / "plans_generated.json"
FEED_XML = OUTPUT / "poiskfeed2026.yml"

DATA.mkdir(exist_ok=True)
OUTPUT.mkdir(exist_ok=True)
