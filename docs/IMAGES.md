# Изображения курсов — стандарт и статус

> Анализ от 2026-04-03

## Поддержка форматов

| Компонент | WebP | JPEG | PNG |
|-----------|------|------|-----|
| PHP 8.3 GD | Да | Да | Да |
| phpThumb MODX | Да | Да | Да |
| Кеш превью | Да (генерируются как .webp) | - | - |

**Вывод:** WebP полностью поддерживается, конвертация не нужна.

## Структура хранения

```
site/assets/images/rp/          <- оригиналы (hero-*, about-*)
site/assets/cache_image/rp/     <- превью (автогенерация phpThumb)
```

### Именование файлов
- Формат: `{тип}-b{batch}-{номер}.webp`
- Типы: `hero` (фон оффера, tv7), `about` (секция 2, tv10)
- Пример: `hero-b4-112.webp`, `about-b4-112.webp`
- Спецсимволы: **нет** (только a-z, 0-9, дефис, точка)

## Пути в Excel

TV-поля с изображениями в батчах:
- **Колонка 13** (tv7, offerImgBg): `assets/images/rp/hero-b{N}-{M}.webp`
- **Колонка 22** (tv10, cont2Img): `assets/images/rp/about-b{N}-{M}.webp`

Пути **относительные** от корня сайта (без ведущего `/`).

## Статус файлов

| Хранилище | Файлов | Формат |
|-----------|--------|--------|
| PSK/Import/images_ready/ | 522 | .webp |
| site/assets/images/rp/ | 244 | .webp |
| Кеш превью (cache_image/rp/) | 359 | .webp |

### Сверка Excel ↔ диск

| Метрика | Значение |
|---------|----------|
| Уникальных путей в Excel (все батчи) | 702 |
| Найдены на диске (site/) | 236 |
| **Отсутствуют на диске** | **466** |
| Есть в images_ready/ (PSK) | 522 |

### Что нужно сделать

466 изображений из батчей 4-12 ещё не скопированы из `PSK/Import/images_ready/` в `site/assets/images/rp/`.

Команда для копирования:
```bash
cp H:/666/TEST/PSK/Import/images_ready/*.webp H:/666/TEST/SITE/ObrProfi_FULL/site/assets/images/rp/
```

## Важно

- Папка `site/assets/images/rp/` **не в Git** (в .gitignore как `site/assets/images/products/`... но `rp/` не указана!)
- Нужно решить: добавить `site/assets/images/rp/` в .gitignore или коммитить
- На проде изображения загружаются отдельно через SCP/деплой
