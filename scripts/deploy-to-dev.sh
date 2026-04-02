#!/bin/bash
# ============================================================
# Деплой файлов LOCAL → DEV
# Загружает изменённые файлы на dev.obrprofi.ru
# ============================================================
source "$(dirname "$0")/config.sh"

echo "=========================================="
echo "  ДЕПЛОЙ НА DEV — dev.obrprofi.ru"
echo "  $TIMESTAMP"
echo "=========================================="

# 1. Бэкап DEV перед деплоем
echo ""
echo "[1/4] Бэкап DEV БД перед деплоем..."
BACKUP_SUBDIR="$BACKUP_DIR/dev_before_deploy_$TIMESTAMP"
mkdir -p "$BACKUP_SUBDIR"

run_ssh "
echo '[client]' > ~/.my_dev.cnf
echo 'user=$DEV_DB_USER' >> ~/.my_dev.cnf
echo 'password=$DEV_DB_PASS' >> ~/.my_dev.cnf
chmod 600 ~/.my_dev.cnf
mysqldump --defaults-extra-file=~/.my_dev.cnf $DEV_DB_NAME > ~/backup_dev_$TIMESTAMP.sql
rm -f ~/.my_dev.cnf
"
download_file "~/backup_dev_$TIMESTAMP.sql" "$BACKUP_SUBDIR/db_dump.sql"
run_ssh "rm -f ~/backup_dev_$TIMESTAMP.sql"
echo "      Бэкап сохранён: $BACKUP_SUBDIR/"

# 2. Синхронизация файлов (assets, core/components, core/elements)
echo ""
echo "[2/4] Загружаю assets..."
"$PSCP" -pw "$SSH_PASS" -batch -r "$SITE_DIR/assets" "$SSH_USER@$SSH_HOST:$DEV_PATH/" 2>&1

echo ""
echo "[3/4] Загружаю core/components..."
"$PSCP" -pw "$SSH_PASS" -batch -r "$SITE_DIR/core/components" "$SSH_USER@$SSH_HOST:$DEV_PATH/core/" 2>&1

# 3. Очистка кеша
echo ""
echo "[4/4] Очищаю кеш на DEV..."
run_ssh "rm -rf $DEV_PATH/core/cache/*"

echo ""
echo "=========================================="
echo "  ДЕПЛОЙ НА DEV ЗАВЕРШЁН"
echo "  Проверьте: https://dev.obrprofi.ru"
echo "  Админка:   https://dev.obrprofi.ru/manager/"
echo "=========================================="
