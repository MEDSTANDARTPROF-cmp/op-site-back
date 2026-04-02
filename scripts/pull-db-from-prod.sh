#!/bin/bash
# ============================================================
# Синхронизация БД PROD → LOCAL
# Скачивает свежую БД с прода и импортирует в локальный Docker
# ============================================================
source "$(dirname "$0")/config.sh"

echo "=========================================="
echo "  СИНХРОНИЗАЦИЯ БД PROD → LOCAL"
echo "  $TIMESTAMP"
echo "=========================================="
echo ""
echo "  ВНИМАНИЕ: Локальная БД будет перезаписана!"
echo ""
read -p "Продолжить? (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "Отменено."
    exit 0
fi

# 1. Дамп БД на сервере
echo ""
echo "[1/4] Создаю дамп ПРОД БД..."
run_ssh "mysqldump -u $PROD_DB_USER -p'$PROD_DB_PASS' $PROD_DB_NAME > ~/prod_dump_$TIMESTAMP.sql"

# 2. Скачиваю
echo "[2/4] Скачиваю дамп..."
download_file "~/prod_dump_$TIMESTAMP.sql" "$BACKUP_DIR/prod_dump_latest.sql"
run_ssh "rm -f ~/prod_dump_$TIMESTAMP.sql"

# 3. Импорт в локальный Docker
echo "[3/4] Импорт в локальную БД..."
docker exec -i $LOCAL_DB_CONTAINER mysql -u$LOCAL_DB_USER -p$LOCAL_DB_PASS $LOCAL_DB_NAME < "$BACKUP_DIR/prod_dump_latest.sql" 2>/dev/null

# 4. Очистка кеша
echo "[4/4] Очищаю локальный кеш..."
rm -rf "$SITE_DIR/core/cache/"*

echo ""
echo "=========================================="
echo "  СИНХРОНИЗАЦИЯ ЗАВЕРШЕНА"
echo "  Локальная БД обновлена с ПРОД"
echo "  http://localhost:8080"
echo "=========================================="
