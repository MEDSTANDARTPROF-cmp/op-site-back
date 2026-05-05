# Кастомная форма ObrForm

Своя форма заявки в MODX с пробросом в Битрикс24 через REST API. Заменяет (на пилотных страницах) старую форму Битрикс24 (`#MdGl`, ID 330) которая теряла 95% посетителей в момент открытия модалки.

## Поток данных

```
[Кнопка-CTA на странице]
    │ data-bs-toggle="modal" data-bs-target="#MdGlNew"
    │ + data-form-title, data-form-subtitle, data-form-source
    ▼
[Модалка ObrForm]                       (HTML+CSS из чанка ObrFormModal в Footer.tpl)
    │ JS читает data-* атрибуты, подставляет в заголовок и hidden-поля
    │ Пользователь заполняет, выбирает канал связи, клик "Отправить"
    ▼
[POST /ajax-lead]                        (MODX-ресурс, alias=ajax-lead, id=8855)
    │ → выполняется сниппет [[!sendLeadToB24]]
    ▼
[sendLeadToB24.php]                      (site/core/elements/snippets/sendLeadToB24.php)
    │ honeypot, валидация, rate-limit, логирование
    │ формирует payload через http_build_query
    ▼
[POST https://ecoprof.bitrix24.ru/rest/.../crm.lead.add.json]
    │ создаётся лид с TITLE/NAME/PHONE/EMAIL/COMMENTS/SOURCE_ID + UF-поля
    ▼
[Битрикс24 CRM]
    │ лид попадает в воронку по правилам B24, ответственный назначается round-robin
    ▼
[Менеджер обрабатывает лид]
```

## Файлы

### Конфиг

`site/core/config/b24.config.php` — gitignored

```php
return [
    'webhook_url' => 'https://ecoprof.bitrix24.ru/rest/26/<token>/',
    'test_hosts' => ['localhost', 'obrprofi.local', 'obrprofi.test', '127.0.0.1'],
    'rate_limit_seconds' => 60,
    'log_file' => 'b24_leads.log',
];
```

При деплое на dev/prod создать вручную с боевым URL вебхука.

### PHP-обработчик

`site/core/elements/snippets/sendLeadToB24.php`

Принимает POST:
- `name` (опционально)
- `phone` (обязательно если канал не "email")
- `email` (обязательно если канал = "email")
- `message` (опционально)
- `channel` (`phone`/`email`/`wa`/`tg`/`viber`/`max`)
- `form_source` (контекст кнопки)
- `page_title` (заголовок страницы)
- `page_url` (URL страницы)
- `website` (honeypot, должно быть пустым)

Возвращает JSON: `{"ok": true, "lead_id": 12345}` или `{"ok": false, "error": "..."}`.

Особенности:
- При hostname-у в `test_hosts` к TITLE добавляется префикс `[ТЕСТ-LOCAL]` — чтобы менеджер мог отфильтровать тестовые лиды
- Логирование всех попыток (success/error) в `site/core/cache/logs/b24_leads.log`
- Rate-limit per (IP + phone) — 60 сек по умолчанию (через APCu если доступен)
- Тип устройства определяется автоматически из User-Agent

### HTML-чанк

`site/core/components/chunks/ObrForm/modalForm.tpl`

Содержит **две модалки**:
- `#MdGlNew` — основная форма заявки
- `#ObrFormConsentModal` — модалка с текстом согласия на ПД (открывается из основной по клику на ссылку «обработку персональных данных», паттерн как у Битрикс24)

Поля формы:
- Имя
- Телефон (отображается всегда, обязательно если канал ≠ email)
- Email (показывается через JS только если канал = email)
- Сообщение (textarea, опционально)
- Радио-выбор канала: 6 вариантов (Звонок / Email / WhatsApp / Telegram / Viber / MAX)
- Чекбокс согласия (предотмечен — юрист подтвердил)
- Honeypot `name="website"` (скрытое поле, спам-фильтр)

### CSS / JS

- `site/assets/components/obr-form/form.css` — стили формы и модалки согласия
- `site/assets/components/obr-form/form.js` — логика: контекст из data-*, событие Метрики `form_open` при показе модалки, `form_focus` на первом фокусе, `form_submit` на успехе, переключение email-поля по выбранному каналу, обработка модалки согласия (Принимаю/Не принимаю → checkbox), AJAX-отправка через fetch, рендер ошибок и успеха

### Иконки

`site/assets/components/obr-form/icons/` — 6 SVG скачаны с Iconify CDN (Material Design Icons и Simple Icons), белые (`color=%23fff`) для использования на цветном фоне через `<img src="...">`:

- `phone.svg` (mdi:phone)
- `email.svg` (mdi:email-outline)
- `whatsapp.svg` (mdi:whatsapp)
- `telegram.svg` (mdi:telegram)
- `viber.svg` (simple-icons:viber)
- `max.svg` (кастомный, белая M в SVG path)

## События Метрики (`ym('reachGoal', ...)`)

| Событие | Когда |
|---|---|
| `form_open` | Открытие модалки (`shown.bs.modal`) |
| `form_focus` | Первый фокус на любом поле |
| `form_submit` | Успешная отправка (после `ok:true` от сервера) |
| `form_channel_phone` | Выбран канал «Звонок», после успеха |
| `form_channel_email` | Выбран канал «Email», после успеха |
| `form_channel_messenger` | Выбран WhatsApp/Telegram/Viber/MAX, после успеха |

Цели заведены руками в кабинете Метрики (см. 03_Метрика-цели.md).

## Подключение к CTA на странице

В шаблоне (любой кнопке) ставим:

```html
<a href="#"
   data-bs-toggle="modal"
   data-bs-target="#MdGlNew"
   data-form-title="Подбор курса"
   data-form-subtitle="Подберём программу с расчётом стоимости..."
   data-form-source="Категория курсов: [[*pagetitle]]">
  Подобрать программу
</a>
```

JS прочитает `data-form-*` и подставит в форму. `data-form-source` уйдёт в B24 в UF-поле «Кнопка, через которую заполнили форму».

## Регистрация в MODX

Один раз через скрипт:

```bash
php scripts/setup_obr_form.php
```

Создаёт:
- Категорию `ObrForm` (id=32)
- Сниппет `sendLeadToB24` (id=52, статичный → файл)
- Чанк `ObrFormModal` (id=108, статичный → файл)
- Шаблон `clean-template` (id=28)
- Ресурс `/ajax-lead` (id=8855, content `[[!sendLeadToB24]]`)

Скрипт идемпотентный — повторный запуск не создаёт дубликатов.

## Безопасность

1. **URL вебхука** хранится только на сервере (config/b24.config.php), не в JS
2. **Honeypot-поле** `name="website"` (скрыто CSS) — если бот его заполнит, лид молча игнорируется
3. **Rate-limit** через APCu — один и тот же IP+phone не может слать чаще раза в минуту
4. **Валидация на сервере** — телефон min 10 цифр, email через `filter_var`
5. **Allowed channels** — белый список (`phone`, `email`, `wa`, `tg`, `viber`, `max`), любое другое значение приводится к `phone`
