#!/bin/bash
# ============================================================
# Полный бэкап ПРОД (файлы + БД)
# Запускать ПЕРЕД деплоем на прод
# ============================================================
source "$(dirname "$0")/config.sh"

echo "=========================================="
echo "  БЭКАП ПРОД — obrprofi.ru"
echo "  $TIMESTAMP"
echo "=========================================="

BACKUP_SUBDIR="$BACKUP_DIR/prod_$TIMESTAMP"
mkdir -p "$BACKUP_SUBDIR"

# 1. Дамп БД
echo ""
echo "[1/3] Создаю дамп БД на сервере..."
run_ssh "mysqldump -u $PROD_DB_USER -p'$PROD_DB_PASS' $PROD_DB_NAME > ~/backup_prod_$TIMESTAMP.sql"
echo "      Дамп создан на сервере"

# 2. Скачиваю дамп
echo "[2/3] Скачиваю дамп БД..."
download_file "~/backup_prod_$TIMESTAMP.sql" "$BACKUP_SUBDIR/db_dump.sql"
echo "      Сохранён: $BACKUP_SUBDIR/db_dump.sql"

# 3. Удаляю дамп с сервера
echo "[3/3] Удаляю временный дамп с сервера..."
run_ssh "rm -f ~/backup_prod_$TIMESTAMP.sql"

echo ""
echo "=========================================="
echo "  БЭКАП ЗАВЕРШЁН"
echo "  Папка: $BACKUP_SUBDIR"
echo "=========================================="
