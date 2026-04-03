# Маппинг Excel → БД MODX (импорт курсов)

> Анализ от 2026-04-03

## Источники данных

| Источник | Путь | Описание |
|----------|------|----------|
| parsed_courses.json | `H:/666/TEST/PSK/Import/parsed_courses.json` | 3716 курсов (raw, name, hours, razryad) |
| Батчи (Excel) | `H:/666/TEST/PSK/Import/unique/batch_NNN.xlsx` | 12 батчей, 351 курс |
| Python-генераторы | `H:/666/TEST/PSK/Import/gen_batch*_unique.py` | Генерируют контент + картинки |

## Прогресс импорта

| Метрика | Значение |
|---------|----------|
| Всего курсов в каталоге | 3716 |
| Подготовлено в батчах (1-12) | 351 |
| Импортировано в БД | 111 |
| Готовы к импорту (в батчах, но не в БД) | 240 |
| Ещё не в батчах | 3365 |

## Структура Excel-файла (24 колонки)

Строка 1 — технические имена полей (для msImportExport)
Строка 2 — человекочитаемые названия
Строки 3+ — данные

| # | Колонка | Поле БД | Таблица | Заполнено? |
|---|---------|---------|---------|------------|
| 1 | article | article | ms2_products | Да, уникальный ключ `rp-*` |
| 2 | parent | parent | site_content | Да, всегда `63` |
| 3 | menutitle | menutitle | site_content | Да |
| 4 | tv5 (offerH1) | TV | tmplvar_contentvalues | Да |
| 5 | pagetitle | pagetitle | site_content | Да |
| 6 | description | description | site_content | Да |
| 7 | alias | alias | site_content | Да |
| 8 | tv24 (crumbsTv) | TV | tmplvar_contentvalues | **Пусто (100%)** |
| 9 | price | price | ms2_products | **Пусто (100%)** |
| 10 | old_price | old_price | ms2_products | **Пусто (100%)** |
| 11 | tv19 (offerProd) | TV | tmplvar_contentvalues | Да, всегда `1` |
| 12 | published | published | site_content | Да, всегда `0` |
| 13 | tv7 (offerImgBg) | TV | tmplvar_contentvalues | Да, путь к изображению |
| 14 | template | template | site_content | Да, всегда `26` |
| 15 | tv2 (offerDescription) | TV | tmplvar_contentvalues | **Пусто (100%)** |
| 16 | tv25 (offerDescription2) | TV | tmplvar_contentvalues | Да, HTML |
| 17 | tv14 (cont2) | TV | tmplvar_contentvalues | Да, HTML |
| 18 | tv17 (cont2Prog) | TV | tmplvar_contentvalues | Да, HTML |
| 19 | tv15 (cont4) | TV | tmplvar_contentvalues | **Пусто (100%)** |
| 20 | tv26 (Stadii) | TV | tmplvar_contentvalues | Да, HTML |
| 21 | tv9 (cont3Lic) | TV | tmplvar_contentvalues | **Пусто (100%)** |
| 22 | tv10 (cont2Img) | TV | tmplvar_contentvalues | Да, путь к изображению |
| 23 | longtitle | longtitle | site_content | Да |
| 24 | tv50 (cont2faqNew) | TV | tmplvar_contentvalues | Да, HTML (FAQ) |

## Всегда пустые поля (6 шт.)

Во всех 12 батчах эти колонки пустые:
- `tv24` (crumbsTv) — хлебные крошки
- `price` — цена
- `old_price` — старая цена
- `tv2` (offerDescription) — описание оффера
- `tv15` (cont4) — контент секции 4
- `tv9` (cont3Lic) — лицензия/аккредитация

**Вопрос:** Цены и эти поля заполняются позже вручную или нужно добавить в генератор?

## Важные замечания

### 1. published = 0
Все курсы в Excel идут неопубликованными. В БД же 111 из 113 опубликованы.
Видимо, публикация делалась вручную после проверки — это правильная практика.

### 2. Кодировка
- Excel (.xlsx) — UTF-8 (стандарт openpyxl)
- Пресет msImportExport настроен на `source_encode: cp1251`
- **Нужно** изменить на UTF-8 или конвертировать файлы

### 3. Сверка (checking_field)
- Пресет использует `alias` для сверки
- По ТЗ нужна сверка по `article`
- **Нужно** изменить `checking_field` в пресете с `alias` на `article`

### 4. Пути к изображениям
Формат: `assets/images/rp/hero-*.webp` (относительный)
Изображения должны быть физически размещены в `site/assets/images/rp/`

### 5. Артикулы
- Формат: `rp-{alias}-{N}` (например `rp-svarshchik-ruchnoj-d-0`)
- Все 351 артикул уникальны
- 111 уже в БД, 240 новых
