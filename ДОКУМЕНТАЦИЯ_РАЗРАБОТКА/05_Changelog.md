# Changelog работ по oбрprofi.ru

## 2026-05-05 — DEPLOY на прод

### Деплой обновлений на прод (obrprofi.ru)

- **Бэкап прода:** `~/backup_prod_20260505_150655.sql` (341 МБ на сервере) — на случай отката
- **Дамп локальной БД (9 таблиц, 167 МБ):** `temp_back/full_deploy_20260505_150733.sql`
  - `modx_site_content` (включая `published=0` на id=7643)
  - `modx_ms2_products`
  - `modx_site_tmplvar_contentvalues` (46 значений FAQ/SEO)
  - `modx_msie_preset`
  - `modx_site_tmplvars` (новые TV faq_data, seo_article)
  - `modx_site_templates` (8 шаблонов с FaqBlock + SeoArticleBlock)
  - `modx_site_htmlsnippets` (5 чанков ObrForm + правки Footer/Navbar/etc.)
  - `modx_site_snippets` (renderFaq, sendLeadToB24)
  - `modx_categories` (категория ObrForm)
- **Залиты файлы на прод (обр-форма, FAQ, SEO):**
  - `core/components/chunks/ObrForm/*.tpl` (5 файлов)
  - `core/elements/snippets/{renderFaq,sendLeadToB24}.php`
  - `assets/components/obr-form/{form.css,form.js,icons/*}` (8 SVG)
  - `core/config/b24.config.php` создан с `test_hosts=[]` (без префикса [ТЕСТ])
- **Smoke-тесты:**
  - 8 страниц вернули HTTP 200 с FAQ + SEO
  - POST `/ajax-lead` → лид 609192 в B24 без префикса [ТЕСТ]
  - id=7643 → 404 (отключен)

### Постдеплой-фиксы (выявлены сразу после деплоя)

- **Относительные пути в `modalForm.tpl`** → абсолютные (`assets/...` → `/assets/...`) — коммит `0562d350`
  - 8 правок: form.css, form.js + 6 иконок каналов
  - Без фикса CSS не загружался на URL с многоуровневым путём типа `/povyshenie-kvalifikacii/pozharnaya-bezopasnost/`
- **CSS должен поддерживать ДВА формата SEO-статей** — коммит `7aa78019`
  - Партии 1-3 (17 файлов): `<div class="obr-seo-article">` + `<table class="obr-seo-table">` + `<p class="obr-seo-article__lead">`
  - Партии 4-5 (6 файлов): `<div class="seo-article">` + обычный `<table>`, без lead
  - Первая попытка фикса (0562d350) переписала селекторы только под `.seo-article` и сломала 17 страниц со старой разметкой
  - Финальный CSS таргетит ОБА класса: `.obr-seo .seo-article, .obr-seo .obr-seo-article` для типографики, и три варианта селекторов для таблиц (`.obr-seo .seo-article table`, `.obr-seo .obr-seo-article table`, `.obr-seo-table`)
  - Также убран `max-width: 980px` — текст больше не уже соседних блоков
- **TV `priceData` менял текст «апреля» → «мая»** на ресурсах 55, 63, 3727 (баннеры скидок). На локали — через MODX API, на проде — через `UPDATE modx_site_tmplvar_contentvalues SET value = REPLACE(value, 'апреля', 'мая') WHERE value LIKE '%апрел%'`
- **Курс с alias `sertifikatsiya` на самом деле имеет alias `sertifikacziya`** на проде/локали — учесть в будущих ссылках
- **404 на `/kontaktyi.html` и `/akkumulyatorshchik-2-go-razryada.html`** — это не сломанные страницы, а мои опечатки в smoke-тестах. Реальные URL: `/contact.html` и `/akkumulyatorshchik-3-razryad.html`

### Откат прод (если что-то сломается)

```bash
ssh severmarin_arkadiy@severmarin.beget.tech
mysql -usevermarin_prof -p'C0uB9nh&TkRL' severmarin_prof < ~/backup_prod_20260505_150655.sql
rm -rf ~/obrprofi.ru/public_html/core/cache/[a-z]*
```

После отката также удалить файлы (если хочется убрать совсем):
```bash
rm -rf ~/obrprofi.ru/public_html/core/components/chunks/ObrForm
rm -rf ~/obrprofi.ru/public_html/core/elements/snippets
rm -rf ~/obrprofi.ru/public_html/assets/components/obr-form
rm ~/obrprofi.ru/public_html/core/config/b24.config.php
```

### Доступы и пути для разработчика

- **Git remote:** github.com/MEDSTANDARTPROF-cmp/op-site-back, ветка master
- **Последний коммит до фиксов:** `5bc2bfaa` (после фиксов будет ещё коммит)
- **SSH прод:** `severmarin_arkadiy@severmarin.beget.tech` (ключ ed25519)
- **Прод-БД:** `severmarin_prof / C0uB9nh&TkRL` (см. `scripts/deploy-courses-to-prod.sh`)
- **Прод-путь:** `~/obrprofi.ru/public_html/`
- **MODX_CORE_PATH прод:** `/home/s/severmarin/obrprofi.ru/public_html/core/`
- **Локальный путь:** `H:/666/TEST/SITE/ObrProfi_FULL/site/`
- **Локальная БД:** `obrprofi / obrprofi` через Laragon (PHP 8.3.30, MySQL 8.4.3)

### Изменено в этом деплое

- 23 категории каталога курсов получили уникальный FAQ (14-15 вопросов) + SEO-статью (1500-2500 слов)
- Партии 1-5 закрыты (B2C-блок 100%)
- Курс id=7643 (organizaciya-obshchevojskovogo-tylovogo) отключён по просьбе клиента

---

## 2026-05-04

### Добавлено

- **Кастомная форма заявки ObrForm** в MODX
  - Файлы: `site/core/elements/snippets/sendLeadToB24.php`, `site/core/components/chunks/ObrForm/modalForm.tpl`, `site/assets/components/obr-form/{form.css,form.js,icons/*}`
  - PHP-сниппет принимает AJAX, валидирует, отправляет в B24 через REST API
  - 6 каналов связи на выбор: Звонок / Email / WhatsApp / Telegram / Viber / MAX
  - Отдельная модалка согласия на ПД с полным юридическим текстом и кнопками «Принимаю / Не принимаю»
  - Honeypot, rate-limit, тест-режим (префикс `[ТЕСТ-LOCAL]` на хостах локалки/dev)
  - Логирование в `site/core/cache/logs/b24_leads.log`
  - 6 SVG-иконок каналов скачаны с Iconify CDN (Material Design Icons + Simple Icons)
- **Конфиг B24** — `site/core/config/b24.config.php` (gitignored), URL входящего вебхука + tест-хосты
- **MODX endpoint** — ресурс `/ajax-lead` (id=8855), вызывает сниппет `[[!sendLeadToB24]]`
- **Категория, сниппет, чанк, шаблон в MODX** — зарегистрированы через `scripts/setup_obr_form.php`
- **Hero-CTA на категориях курсов** — переключён на новую форму:
  - Текст: «Подобрать программу →»
  - Стиль: синий primary с тенью, hover-эффектом, стрелка едет вправо
  - Заголовок модалки: подтягивается H1 страницы (`offerH1`)
  - Подзаголовок: «Подберём программу с расчётом стоимости и сроков. Менеджер свяжется в течение 15 минут.»
  - В B24 идёт контекст: «Категория курсов: [pagetitle]»
- **onclick `ym('TL')`** добавлен на 8 tel-ссылок в шаблонах (Navbar, Footer, contAdres, boxCTABanner, boxGuaranteePSK, msProduct.content.PSK)
- **Документация** в папке `ДОКУМЕНТАЦИЯ_РАЗРАБОТКА/` (не деплоится на прод)

### Изменено

- **Чанк Footer (id=3)** в БД MODX — добавлен `[[$ObrFormModal]]` перед старой модалкой `#MdGl`
- **Чанк catalogHeader (id=47)** в БД MODX — переписан hero-CTA
- **Текст согласия на ПД** — встроен полный юридический текст из B24-формы (с реквизитами ООО УЦ ОбрПрофи)

### Найдено и зафиксировано (без изменений)

- **Цели Метрики**: 17 целей, из них 6 мусорных (нулевые срабатывания, опечатки в идентификаторах) — рекомендация удалить через UI
- **Цель «Клик по телефону» (TL)**: 2 срабатывания/90д — было настроено только в мобильном меню. Поправлено (см. выше).
- **B24 источник**: используем `RC_GENERATOR` (Заявка с сайта obrprofi.ru). При попытке поставить `WEB` (которого нет в справочнике) B24 молча подставлял «Telegram МСП».
- **B24 UF-поля**: используем общие поля портала (`UF_CRM_LEAD_1737612814914`, `UF_CRM_1738819361031`, `UF_CRM_1739333675514`, `UF_CRM_1740631494137`) — те же что заполняются у medstandartprof.ru. Новых не создаём.
- **Боты Сингапура**: 3760 визитов/90д с 99,5% отказов — не вредят SEO/серверу. Решение: фильтр-сегмент в Метрике (через UI).
- **Скоуп OAuth-токена Метрики**: только чтение (403 Access Denied на запись). Цели создавать через UI.

### Удалено

- (Файлы) Закомментированный код WhatsApp в шаблонах — пока не удалён, оставлен на отдельную задачу.

## 2026-04-30

### Подготовлено к согласованию

- **3 HTML-отчёта** в `ПРЕДЛОЖЕНИЯ_2026-04-30/`:
  - Общий план оптимизации (8 спринтов)
  - Детальный отчёт по форме Битрикс24
  - Детальный отчёт по воронке категория→карточка

### Найдено

- Главная находка сверх исходного аудита: **сломан трекинг заявок**. 1812 кликов CTA → только 99 фиксируется как «Заявка». Воронка модалки — главное узкое горлышко.
- Аудитория тестов — профессионалы во время сдачи аккредитации. Не покупатели курсов «прямо сейчас», но идеальный B2B-канал через захват email/даты следующей аккредитации.
- B2B-канал (кадровики на блоге, малый бизнес на категориях) — недооценён, не упоминается в аудите.

## Тестовые лиды (можно удалить в B24)

Все с пометкой `[ТЕСТ-LOCAL]` или `[ТЕСТ-DIRECT]` в начале TITLE:

- 609090 (первый, до починки JSON→urlencoded — пустой)
- 609096 (Test_FixedPayload)
- 609098 (TestUF — первый с UF полями)
- 609100 (DBG прямой тест UF)
- 609102 (TestUF2)
- 609104 (DBG после очистки кеша — все UF заполнились)
- 609106 (TestSrc — проверка нового SOURCE_DESCRIPTION)
- (плюс несколько после правки иконок и UF полей)

Найти в CRM Битрикса поиском по `[ТЕСТ` — удалить пакетно.
