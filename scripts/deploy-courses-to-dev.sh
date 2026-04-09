#!/bin/bash
# ============================================================
# Деплой курсов LOCAL → DEV (Laragon + OpenSSH)
# Заливает: изображения + БД (контент курсов)
# ============================================================

# SSH доступы (из config.sh)
SSH_HOST="severmarin.beget.tech"
SSH_USER="severmarin_arkadiy"
SSH_PASS="SLNR*paRRTQ0"
DEV_PATH="~/dev.obrprofi.ru/public_html"
DEV_DB_NAME="severmarin_devpr"
DEV_DB_USER="severmarin_devpr"
DEV_DB_PASS='2duam0xdjcZ&'

# Локальные пути (Laragon)
MYSQLDUMP="/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump"
LOCAL_DB="obrprofi"
SITE_DIR="H:/666/TEST/SITE/ObrProfi_FULL/site"
TEMP_DIR="H:/666/TEST/SITE/ObrProfi_FULL/temp_back"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo "=========================================="
echo "  ДЕПЛОЙ КУРСОВ НА DEV"
echo "  $TIMESTAMP"
echo "=========================================="

# 1. Бэкап DEV БД
echo ""
echo "[1/5] Бэкап DEV БД..."
ssh "$SSH_USER@$SSH_HOST" "mysqldump -u$DEV_DB_USER -p'$DEV_DB_PASS' $DEV_DB_NAME > ~/backup_dev_$TIMESTAMP.sql" 2>&1
if [ $? -ne 0 ]; then
    echo "ОШИБКА бэкапа DEV! Прерываю."
    exit 1
fi
echo "      Бэкап создан на сервере: ~/backup_dev_$TIMESTAMP.sql"

# 2. Дамп таблиц контента из локальной БД
echo ""
echo "[2/5] Дамп контента курсов из локальной БД..."
TABLES="modx_site_content modx_ms2_products modx_site_tmplvar_contentvalues modx_msie_preset"
DUMP_FILE="$TEMP_DIR/courses_dump_$TIMESTAMP.sql"
"$MYSQLDUMP" -u root "$LOCAL_DB" $TABLES --single-transaction > "$DUMP_FILE" 2>&1
if [ $? -ne 0 ]; then
    echo "ОШИБКА дампа! Прерываю."
    exit 1
fi
SIZE=$(du -h "$DUMP_FILE" | cut -f1)
echo "      Дамп создан: $DUMP_FILE ($SIZE)"

# 3. Загрузка дампа на сервер
echo ""
echo "[3/5] Загрузка дампа на DEV..."
scp "$DUMP_FILE" "$SSH_USER@$SSH_HOST:~/courses_dump_$TIMESTAMP.sql" 2>&1
echo "      Загружен"

# 4. Импорт в DEV БД
echo ""
echo "[4/5] Импорт в DEV БД..."
ssh "$SSH_USER@$SSH_HOST" "mysql -u$DEV_DB_USER -p'$DEV_DB_PASS' $DEV_DB_NAME < ~/courses_dump_$TIMESTAMP.sql && rm -f ~/courses_dump_$TIMESTAMP.sql" 2>&1
echo "      Импортирован"

# 5. Загрузка изображений
echo ""
echo "[5/5] Загрузка изображений на DEV..."
scp -r "$SITE_DIR/assets/images/rp/" "$SSH_USER@$SSH_HOST:$DEV_PATH/assets/images/rp/" 2>&1
echo "      Изображения загружены"

# Очистка кеша
echo ""
echo "Очистка кеша DEV..."
ssh "$SSH_USER@$SSH_HOST" "rm -rf $DEV_PATH/core/cache/*" 2>&1

echo ""
echo "=========================================="
echo "  ДЕПЛОЙ ЗАВЕРШЁН"
echo "  Проверьте: https://dev.obrprofi.ru"
echo "  Бэкап DEV: ~/backup_dev_$TIMESTAMP.sql"
echo "=========================================="
