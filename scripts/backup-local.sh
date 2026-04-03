#!/bin/bash
# ============================================================
# Бэкап ЛОКАЛЬНОЙ БД (Laragon MySQL)
# Запускать ПЕРЕД любой работой с импортом/миграцией
# ============================================================

MYSQLDUMP="/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump"
DB_NAME="obrprofi"
DB_USER="root"
BACKUP_DIR="$(dirname "$0")/../temp_back"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/backup_${TIMESTAMP}.sql"

mkdir -p "$BACKUP_DIR"

echo "=========================================="
echo "  БЭКАП ЛОКАЛЬНОЙ БД — $DB_NAME"
echo "  $(date)"
echo "=========================================="

echo ""
echo "[1/2] Создаю дамп..."
"$MYSQLDUMP" -u "$DB_USER" "$DB_NAME" --routines --triggers --single-transaction > "$BACKUP_FILE" 2>&1

if [ $? -eq 0 ]; then
    SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "      Дамп создан: $BACKUP_FILE ($SIZE)"
else
    echo "      ОШИБКА при создании дампа!"
    exit 1
fi

# Удаляем бэкапы старше 7 дней
echo "[2/2] Очистка старых бэкапов (>7 дней)..."
find "$BACKUP_DIR" -name "backup_*.sql" -mtime +7 -delete 2>/dev/null
TOTAL=$(ls "$BACKUP_DIR"/backup_*.sql 2>/dev/null | wc -l)
echo "      Всего бэкапов: $TOTAL"

echo ""
echo "=========================================="
echo "  БЭКАП ЗАВЕРШЁН"
echo "  Файл: $BACKUP_FILE"
echo "=========================================="
