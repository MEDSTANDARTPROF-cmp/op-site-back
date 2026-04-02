#!/bin/bash
# ============================================================
# Откат ПРОД из бэкапа
# ============================================================
source "$(dirname "$0")/config.sh"

echo "=========================================="
echo "  ОТКАТ ПРОД"
echo "=========================================="
echo ""

# Показать доступные бэкапы
echo "Доступные бэкапы:"
ls -d "$BACKUP_DIR"/prod_* 2>/dev/null | while read dir; do
    echo "  $(basename "$dir")"
done

echo ""
read -p "Введите имя бэкапа (например prod_20260402_120000): " backup_name

RESTORE_DIR="$BACKUP_DIR/$backup_name"

if [ ! -d "$RESTORE_DIR" ]; then
    echo "ОШИБКА: Бэкап $RESTORE_DIR не найден!"
    exit 1
fi

if [ ! -f "$RESTORE_DIR/db_dump.sql" ]; then
    echo "ОШИБКА: Файл БД не найден в бэкапе!"
    exit 1
fi

echo ""
echo "  !!! ОТКАТ БОЕВОЙ БД !!!"
echo "  Бэкап: $backup_name"
echo ""
read -p "Вы уверены? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "Отменено."
    exit 0
fi

# 1. Загрузка дампа на сервер
echo ""
echo "[1/3] Загружаю дамп на сервер..."
upload_file "$RESTORE_DIR/db_dump.sql" "~/rollback_$TIMESTAMP.sql"

# 2. Импорт
echo "[2/3] Восстанавливаю БД..."
run_ssh "mysql -u $PROD_DB_USER -p'$PROD_DB_PASS' $PROD_DB_NAME < ~/rollback_$TIMESTAMP.sql && rm -f ~/rollback_$TIMESTAMP.sql"

# 3. Очистка кеша
echo "[3/3] Очищаю кеш..."
run_ssh "rm -rf $PROD_PATH/core/cache/*"

echo ""
echo "=========================================="
echo "  ОТКАТ ЗАВЕРШЁН"
echo "  Проверьте: https://obrprofi.ru"
echo "=========================================="
