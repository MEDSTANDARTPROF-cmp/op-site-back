# Логика импорта msImportExport — upsert

> Анализ от 2026-04-03

## Как работает пресет "Рабочие" (id=16)

### Алгоритм для каждой строки Excel:

1. Читает строку, маппит колонки на поля по порядку
2. Ищет ресурс по `checking_field = alias`
3. **Если найден** → `action = update`, обновляет все переданные поля
4. **Если не найден** → `action = create`, создаёт новый ресурс
5. Проверяет `skip_action = update` — **пропускает обновление** (!)

### Настройка skip_action

```
skip_action: ""  (пусто — обновление включено)
```

Изменено 2026-04-03 (было `"update"` — пропускало существующие).
Теперь: если ресурс найден по alias — он **обновляется** данными из Excel (по ТЗ п.3).

### published — сохранение текущего состояния

Поле `published` **убрано из пресета и из Excel** (2026-04-03).
Поведение:
- **Update** — published остаётся как в БД (опубликован → остаётся, неопубликован → остаётся)
- **Create** — `published_product_default = 0` → новые курсы создаются неопубликованными

Оригиналы батчей с колонкой published: `PSK/Import/unique/backup_with_published/`

### Поведение при пустых полях

При `action = update`:
- `published` — если в Excel пусто, берётся текущее значение из БД
- `template` — если в Excel пусто, берётся из БД
- `pagetitle` — если пусто, берётся из БД
- Остальные поля — перезаписываются значением из Excel

При `action = create`:
- `published` → берётся `published_product_default = 1` (из настроек пресета)
- `template` → берётся `template_product_default = 4`
- `hidemenu` → `1`

**Важно:** в пресете `published_product_default = 1`, но в Excel `published = 0`.
Excel-значение имеет приоритет → новые курсы создаются неопубликованными.

## Тестовый батч

Файл: `temp_back/test_batch_5items.xlsx`

| # | Артикул | Статус | Ожидание |
|---|---------|--------|----------|
| 1 | rp-svarshchik-ruchnoj-d-0 | В БД | Обновление |
| 2 | rp-mashinist-dorozhno-t-1 | В БД | Обновление |
| 3 | rp-detskaya-podrostkova-120 | Новый | Создание |
| 4 | rp-organizaciya-provede-121 | Новый | Создание |
| 5 | rp-psikhosomatika-122 | Новый | Создание |

Изображения для всех 5 — проверены, на диске.

## Порядок тестового импорта (Этап 6)

1. `bash scripts/backup-local.sh` — бэкап
2. Скопировать `temp_back/test_batch_5items.xlsx` в `site/assets/components/msimportexport/upload/`
3. Админка → msImportExport → пресет "Рабочие" → загрузить файл → запустить
4. Проверить:
   - 3 новых ресурса созданы в parent=63
   - 2 существующих пропущены
   - TV-поля заполнены
   - Изображения отображаются
5. Если ошибка → `bash scripts/restore-local.sh`
