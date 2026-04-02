#!/bin/bash
# ============================================================
# Миграция БД LOCAL → DEV
# Экспорт шаблонов/чанков/сниппетов из локальной БД
# и импорт в DEV
# ============================================================
source "$(dirname "$0")/config.sh"

echo "=========================================="
echo "  МИГРАЦИЯ БД → DEV"
echo "  $TIMESTAMP"
echo "=========================================="

# Таблицы для миграции (шаблоны, чанки, сниппеты, плагины, TV)
TABLES="modx_site_templates modx_site_htmlsnippets modx_site_snippets modx_site_plugins modx_site_plugin_events modx_site_tmplvars modx_site_tmplvar_access modx_site_tmplvar_contentvalues modx_site_tmplvar_templates modx_categories"

echo ""
echo "Мигрируемые таблицы:"
for t in $TABLES; do echo "  - $t"; done

echo ""
read -p "Продолжить? (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "Отменено."
    exit 0
fi

# 1. Дамп из локальной Docker БД
echo ""
echo "[1/3] Экспорт из локальной БД..."
docker exec $LOCAL_DB_CONTAINER mysqldump -u$LOCAL_DB_USER -p$LOCAL_DB_PASS $LOCAL_DB_NAME $TABLES > "$BACKUP_DIR/local_migration_$TIMESTAMP.sql" 2>/dev/null
echo "      Экспорт завершён"

# 2. Загрузка на сервер
echo "[2/3] Загрузка дампа на сервер..."
upload_file "$BACKUP_DIR/local_migration_$TIMESTAMP.sql" "~/migration_$TIMESTAMP.sql"

# 3. Импорт в DEV БД
echo "[3/3] Импорт в DEV БД..."
run_ssh "
echo '[client]' > ~/.my_dev.cnf
echo 'user=$DEV_DB_USER' >> ~/.my_dev.cnf
echo 'password=$DEV_DB_PASS' >> ~/.my_dev.cnf
chmod 600 ~/.my_dev.cnf
mysql --defaults-extra-file=~/.my_dev.cnf $DEV_DB_NAME < ~/migration_$TIMESTAMP.sql
rm -f ~/.my_dev.cnf ~/migration_$TIMESTAMP.sql
"

# 4. Очистка кеша
run_ssh "rm -rf $DEV_PATH/core/cache/*"

echo ""
echo "=========================================="
echo "  МИГРАЦИЯ БД НА DEV ЗАВЕРШЕНА"
echo "  Проверьте: https://dev.obrprofi.ru"
echo "=========================================="
