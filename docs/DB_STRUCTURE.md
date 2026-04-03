# Структура БД для импорта курсов (template=26, parent=63)

> Анализ от 2026-04-03. Всего курсов в БД: 113, из них 112 с артикулом.

## Ключ сверки — артикул

**Поле:** `modx_ms2_products.article` (varchar 50)
**Формат:** `rp-*` (например `rp-svarshchik-ruchnoj-d-0`)
**Проверка дублей:** пресет msImportExport использует `checking_field: "alias"`, но артикул надёжнее.

---

## Таблицы, участвующие в импорте

### 1. modx_site_content (основные поля ресурса)

| Поле | Тип | Описание | Пример |
|------|-----|----------|--------|
| id | int PK | ID ресурса (авто) | 7127 |
| pagetitle | varchar(191) | Название курса | Сварщик ручной дуговой сварки... |
| longtitle | varchar(191) | Длинный заголовок | |
| description | text | SEO описание | |
| alias | varchar(191) | URL-алиас | svarshchik-ruchnoj-dugovoj-svarki |
| menutitle | varchar(191) | Короткое название для меню | |
| parent | int | Родитель | 63 |
| template | int | Шаблон | 26 |
| published | tinyint | Опубликован | 1 |
| content | mediumtext | Основной контент | |
| class_key | varchar(100) | Тип объекта | msProduct |

### 2. modx_ms2_products (miniShop2 — товарные поля)

| Поле | Тип | Описание | Пример |
|------|-----|----------|--------|
| id | int PK | = id из site_content | 7127 |
| article | varchar(50) | **АРТИКУЛ (ключ сверки)** | rp-svarshchik-ruchnoj-d-0 |
| price | decimal(12,2) | Цена | 8900.00 |
| old_price | decimal(12,2) | Старая цена | 11900.00 |
| image | varchar(255) | Главное изображение | |
| weight | decimal(13,3) | Вес (не используется) | 0.000 |
| vendor | int | Вендор (не используется) | 0 |

### 3. modx_site_tmplvar_contentvalues (TV-поля для template=26)

| TV ID | Имя | Тип | Описание |
|-------|-----|-----|----------|
| 2 | offerDescription | ace | Описание оффера |
| 3 | offerMarker | ace | Маркер (бейджик) |
| 5 | offerH1 | ace | H1 заголовок оффера |
| 7 | offerImgBg | image | Фон оффера |
| 9 | cont3Lic | ace | Лицензия/аккредитация |
| 10 | cont2Img | image | Изображение секции 2 |
| 14 | cont2 | richtext | Контент секции 2 |
| 15 | cont4 | ace | Контент секции 4 |
| 17 | cont2Prog | richtext | Программа обучения |
| 19 | offerProd | listbox | Оформлен как товар? (1/0) |
| 24 | crumbsTv | text | Хлебные крошки |
| 25 | offerDescription2 | ace | Дополнительное описание |
| 26 | Stadii | ace | Этапы работы |
| 29 | offerImgBgSm | image | Фон оффера (мобильный) |
| 50 | cont2faqNew | ace | FAQ блок |

**TV-поля с изображениями (важно для п.4 ТЗ):**
- tv1 (img), tv6 (offerImg), tv7 (offerImgBg), tv8 (offerIcon)
- tv10 (cont2Img), tv11-13 (cont3ImgA/B/C), tv16 (cont4Img)
- tv18 (icon), tv29 (offerImgBgSm), tv42 (YdPicture)

---

## Пресет msImportExport (id=16, "Рабочие")

**Порядок колонок в Excel:**
```
article | parent | menutitle | tv5 | pagetitle | description | alias | tv24 | price | old_price | tv19 | published | tv7 | template | tv2 | tv25 | tv14 | tv17 | tv15 | tv26 | tv9 | tv10 | longtitle | tv50
```

**Настройки:**
- checking_field: `alias` (сверка по алиасу)
- check_existence: `1` (проверяет существование)
- skip_action: `update` (обновляет существующие)
- template_product_default: `4` (шаблон по умолчанию)
- start_from_line: `3` (пропускает 2 строки заголовков)
- source_encode: `cp1251` (кодировка исходника)
- first_delimiter: `|`, second_delimiter: `%`

---

## Маппинг Excel → БД (итого)

| # | Колонка Excel | Поле в БД | Таблица |
|---|---------------|-----------|---------|
| 1 | article | article | ms2_products |
| 2 | parent | parent | site_content |
| 3 | menutitle | menutitle | site_content |
| 4 | tv5 (offerH1) | tmplvar_contentvalues | TV |
| 5 | pagetitle | pagetitle | site_content |
| 6 | description | description | site_content |
| 7 | alias | alias | site_content |
| 8 | tv24 (crumbsTv) | tmplvar_contentvalues | TV |
| 9 | price | price | ms2_products |
| 10 | old_price | old_price | ms2_products |
| 11 | tv19 (offerProd) | tmplvar_contentvalues | TV |
| 12 | published | published | site_content |
| 13 | tv7 (offerImgBg) | tmplvar_contentvalues | TV |
| 14 | template | template | site_content |
| 15 | tv2 (offerDescription) | tmplvar_contentvalues | TV |
| 16 | tv25 (offerDescription2) | tmplvar_contentvalues | TV |
| 17 | tv14 (cont2) | tmplvar_contentvalues | TV |
| 18 | tv17 (cont2Prog) | tmplvar_contentvalues | TV |
| 19 | tv15 (cont4) | tmplvar_contentvalues | TV |
| 20 | tv26 (Stadii) | tmplvar_contentvalues | TV |
| 21 | tv9 (cont3Lic) | tmplvar_contentvalues | TV |
| 22 | tv10 (cont2Img) | tmplvar_contentvalues | TV |
| 23 | longtitle | longtitle | site_content |
| 24 | tv50 (cont2faqNew) | tmplvar_contentvalues | TV |

---

## Важные замечания

1. **class_key = msProduct** — все курсы являются товарами miniShop2
2. **Артикул vs alias:** пресет сверяет по alias, но по ТЗ нужна сверка по артикулу — нужно изменить `checking_field` на `article`
3. **Кодировка:** пресет ожидает cp1251, но батчи из PSK/ генерируются в UTF-8 — нужно либо конвертировать, либо поменять настройку
4. **Изображения:** пути в TV-полях относительные (например `assets/images/rp/hero-tig-welding-1.webp`)
