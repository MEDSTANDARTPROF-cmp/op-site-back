# Результаты тестового импорта

> Дата: 2026-04-03

## Конфигурация теста

- Пресет: "Рабочие" (id=16)
- Файл: `test_batch_5items.xlsx` (2 существующих + 3 новых)
- Способ: PHP CLI через msImportExport API
- Бэкап: `backup_20260403_182540.sql`

## Результат: УСПЕХ

| Метрика | До | После |
|---------|-----|-------|
| Всего курсов (parent=63) | 113 | 116 |
| Новых создано | - | 3 |
| Обновлено | - | 2 |

### Созданные курсы

| ID | Артикул | Alias | Published |
|----|---------|-------|-----------|
| 7251 | rp-detskaya-podrostkova-120 | detskaya-podrostkovaya-ginekologiya | 0 |
| 7252 | rp-organizaciya-provede-121 | organizaciya-provedeniya-profilakticheskikh | 0 |
| 7253 | rp-psikhosomatika-122 | psikhosomatika | 0 |

TV-поля: offerH1, offerImgBg, cont2Img, cont2, cont2Prog, offerProd, offerDescription2, Stadii, cont2faqNew — все заполнены корректно.

### Обновлённые курсы

| ID | Артикул | Что изменилось |
|----|---------|---------------|
| 7127 | rp-svarshchik-ruchnoj-d-0 | published: 1→0 (!) |
| 7128 | rp-mashinist-dorozhno-t-1 | published: 1→0 (!) |

## Найденная проблема: published при обновлении

При обновлении существующих ресурсов поле `published` перезаписывается значением из Excel (0).
Это снимает публикацию с курсов которые уже были на сайте!

**Исправлено вручную:** `UPDATE modx_site_content SET published=1 WHERE id IN (7127, 7128);`

### Решения на будущее (выбрать одно):

1. **Убрать колонку published из Excel** — тогда при update берётся текущее значение из БД
2. **Ставить published=1 в Excel** — но тогда новые курсы сразу публикуются без проверки
3. **Вернуть skip_action=update** — дубли не обновляются, только создаются новые
4. **Публиковать пакетно после проверки** — оставить published=0 в Excel, после проверки публиковать SQL-запросом

**Рекомендация:** вариант 4 — самый безопасный. Импорт с published=0, потом:
```sql
UPDATE modx_site_content SET published=1 WHERE parent=63 AND template=26 AND published=0;
```

## Команда для запуска импорта через CLI

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe -d extension=zip run_test_import.php
```

Необходимо расширение `zip` (в php.ini закомментировано, подключаем через `-d`).

## Ошибки (некритичные)

- `IeMsOptionsColor`, `IeMsSalePrice`, `IeGallery`, `IeMsProductRemains`, `IeYandexMarket` — отсутствующие дополнительные модули, не влияют на импорт
- `deprecated` warnings от PHP 8.3 — косметические, не влияют на работу
