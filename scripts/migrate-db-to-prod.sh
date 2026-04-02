#!/bin/bash
# ============================================================
# Миграция БД DEV → PROD
# Переносит шаблоны/чанки/сниппеты с DEV на PROD
# ============================================================
source "$(dirname "$0")/config.sh"

echo "=========================================="
echo "  МИГРАЦИЯ БД DEV → PROD"
echo "  $TIMESTAMP"
echo "=========================================="
echo ""
echo "  !!! ВНИМАНИЕ: ИЗМЕНЕНИЕ БОЕВОЙ БД !!!"
echo ""

# Таблицы для миграции
TABLES="modx_site_templates modx_site_htmlsnippets modx_site_snippets modx_site_plugins modx_site_plugin_events modx_site_tmplvars modx_site_tmplvar_access modx_site_tmplvar_contentvalues modx_site_tmplvar_templates modx_categories"

echo "Мигрируемые таблицы:"
for t in $TABLES; do echo "  - $t"; done

echo ""
read -p "Вы уверены? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "Отменено."
    exit 0
fi

# 1. Бэкап ПРОД
echo ""
echo "[1/3] Бэкап ПРОД БД..."
bash "$(dirname "$0")/backup-prod.sh"

# 2. Дамп таблиц с DEV
echo ""
echo "[2/3] Экспорт таблиц с DEV..."
run_ssh "
echo '[client]' > ~/.my_dev.cnf
echo 'user=$DEV_DB_USER' >> ~/.my_dev.cnf
echo 'password=$DEV_DB_PASS' >> ~/.my_dev.cnf
chmod 600 ~/.my_dev.cnf
mysqldump --defaults-extra-file=~/.my_dev.cnf $DEV_DB_NAME $TABLES > ~/migration_to_prod_$TIMESTAMP.sql
rm -f ~/.my_dev.cnf
"

# 3. Импорт в ПРОД
echo "[3/3] Импорт в ПРОД БД..."
run_ssh "
mysql -u $PROD_DB_USER -p'$PROD_DB_PASS' $PROD_DB_NAME < ~/migration_to_prod_$TIMESTAMP.sql
rm -f ~/migration_to_prod_$TIMESTAMP.sql
"

# 4. Очистка кеша
run_ssh "rm -rf $PROD_PATH/core/cache/*"

echo ""
echo "=========================================="
echo "  МИГРАЦИЯ БД НА ПРОД ЗАВЕРШЕНА"
echo "  Проверьте: https://obrprofi.ru/manager/"
echo "=========================================="
