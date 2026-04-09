#!/bin/bash
# ============================================================
# Деплой курсов LOCAL → PROD (obrprofi.ru)
# Заливает: БД (контент курсов) + изображения
# ============================================================

SSH_HOST="severmarin.beget.tech"
SSH_USER="severmarin_arkadiy"
PROD_PATH="~/obrprofi.ru/public_html"
PROD_DB_NAME="severmarin_prof"
PROD_DB_USER="severmarin_prof"
PROD_DB_PASS='C0uB9nh&TkRL'

MYSQLDUMP="/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump"
LOCAL_DB="obrprofi"
SITE_DIR="H:/666/TEST/SITE/ObrProfi_FULL/site"
BACKUP_DIR="H:/666/TEST/SITE/ObrProfi_FULL/backup"
TEMP_DIR="H:/666/TEST/SITE/ObrProfi_FULL/temp_back"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo "=========================================="
echo "  ДЕПЛОЙ КУРСОВ НА ПРОД — obrprofi.ru"
echo "  $TIMESTAMP"
echo "=========================================="
echo ""
echo "  ВНИМАНИЕ: это деплой на БОЕВОЙ сайт!"
read -p "  Продолжить? (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "Отменено."
    exit 0
fi

# 1. Бэкап ПРОД БД
echo ""
echo "[1/6] Бэкап ПРОД БД..."
ssh "$SSH_USER@$SSH_HOST" "mysqldump -u$PROD_DB_USER -p'$PROD_DB_PASS' $PROD_DB_NAME > ~/backup_prod_$TIMESTAMP.sql" 2>&1
if [ $? -ne 0 ]; then
    echo "ОШИБКА бэкапа ПРОД! Прерываю."
    exit 1
fi
echo "      Бэкап создан на сервере: ~/backup_prod_$TIMESTAMP.sql"

# 2. Скачиваем бэкап локально
echo ""
echo "[2/6] Скачиваю бэкап прода локально..."
mkdir -p "$BACKUP_DIR"
scp "$SSH_USER@$SSH_HOST:~/backup_prod_$TIMESTAMP.sql" "$BACKUP_DIR/backup_prod_$TIMESTAMP.sql" 2>&1
SIZE=$(du -h "$BACKUP_DIR/backup_prod_$TIMESTAMP.sql" | cut -f1)
echo "      Сохранён: $BACKUP_DIR/backup_prod_$TIMESTAMP.sql ($SIZE)"

# 3. Дамп локальных таблиц контента
echo ""
echo "[3/6] Дамп контента из локальной БД..."
TABLES="modx_site_content modx_ms2_products modx_site_tmplvar_contentvalues modx_msie_preset"
DUMP_FILE="$TEMP_DIR/courses_prod_$TIMESTAMP.sql"
"$MYSQLDUMP" -u root "$LOCAL_DB" $TABLES --single-transaction > "$DUMP_FILE" 2>&1
if [ $? -ne 0 ]; then
    echo "ОШИБКА дампа! Прерываю."
    exit 1
fi
SIZE=$(du -h "$DUMP_FILE" | cut -f1)
echo "      Дамп: $DUMP_FILE ($SIZE)"

# 4. Загрузка и импорт в ПРОД БД
echo ""
echo "[4/6] Загрузка дампа на ПРОД..."
scp "$DUMP_FILE" "$SSH_USER@$SSH_HOST:~/courses_prod_$TIMESTAMP.sql" 2>&1
echo "      Импортирую в ПРОД БД..."
ssh "$SSH_USER@$SSH_HOST" "mysql -u$PROD_DB_USER -p'$PROD_DB_PASS' $PROD_DB_NAME < ~/courses_prod_$TIMESTAMP.sql && rm -f ~/courses_prod_$TIMESTAMP.sql" 2>&1
if [ $? -ne 0 ]; then
    echo "ОШИБКА импорта! Бэкап: ~/backup_prod_$TIMESTAMP.sql"
    exit 1
fi
echo "      Импорт завершён"

# 5. Загрузка изображений
echo ""
echo "[5/6] Загрузка изображений на ПРОД..."
ssh "$SSH_USER@$SSH_HOST" "mkdir -p $PROD_PATH/assets/images/rp" 2>&1
scp "$SITE_DIR/assets/images/rp/"*.webp "$SSH_USER@$SSH_HOST:$PROD_PATH/assets/images/rp/" 2>&1
echo "      Изображения загружены"

# 6. Очистка кеша
echo ""
echo "[6/6] Очистка кеша ПРОД..."
ssh "$SSH_USER@$SSH_HOST" "rm -rf $PROD_PATH/core/cache/*" 2>&1
echo "      Кеш очищен"

echo ""
echo "=========================================="
echo "  ДЕПЛОЙ НА ПРОД ЗАВЕРШЁН"
echo "  Проверьте: https://obrprofi.ru"
echo "  Админка:   https://obrprofi.ru/manager/"
echo ""
echo "  Бэкап прода на сервере:"
echo "    ~/backup_prod_$TIMESTAMP.sql"
echo "  Бэкап прода локально:"
echo "    $BACKUP_DIR/backup_prod_$TIMESTAMP.sql"
echo ""
echo "  ОТКАТ (если что-то не так):"
echo "    ssh $SSH_USER@$SSH_HOST"
echo "    mysql -u$PROD_DB_USER -p'...' $PROD_DB_NAME < ~/backup_prod_$TIMESTAMP.sql"
echo "    rm -rf $PROD_PATH/core/cache/*"
echo "=========================================="
