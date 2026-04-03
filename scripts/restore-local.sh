#!/bin/bash
# ============================================================
# Восстановление ЛОКАЛЬНОЙ БД из бэкапа
# Использование: ./restore-local.sh [файл.sql]
# Без аргумента — восстановит из последнего бэкапа
# ============================================================

MYSQL="/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql"
DB_NAME="obrprofi"
DB_USER="root"
BACKUP_DIR="$(dirname "$0")/../temp_back"

if [ -n "$1" ]; then
    BACKUP_FILE="$1"
else
    BACKUP_FILE=$(ls -t "$BACKUP_DIR"/backup_*.sql 2>/dev/null | head -1)
fi

if [ -z "$BACKUP_FILE" ] || [ ! -f "$BACKUP_FILE" ]; then
    echo "ОШИБКА: Файл бэкапа не найден!"
    echo "Использование: $0 [путь/к/файлу.sql]"
    echo "Доступные бэкапы:"
    ls -lh "$BACKUP_DIR"/backup_*.sql 2>/dev/null || echo "  (нет бэкапов)"
    exit 1
fi

SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
echo "=========================================="
echo "  ВОССТАНОВЛЕНИЕ БД — $DB_NAME"
echo "  Файл: $BACKUP_FILE ($SIZE)"
echo "=========================================="
echo ""
read -p "Вы уверены? Текущая БД будет ПЕРЕЗАПИСАНА! (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "Отменено."
    exit 0
fi

echo "Восстанавливаю..."
"$MYSQL" -u "$DB_USER" "$DB_NAME" < "$BACKUP_FILE" 2>&1

if [ $? -eq 0 ]; then
    echo "БД успешно восстановлена!"
else
    echo "ОШИБКА при восстановлении!"
    exit 1
fi
