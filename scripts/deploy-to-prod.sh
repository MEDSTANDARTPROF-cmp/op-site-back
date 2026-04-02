#!/bin/bash
# ============================================================
# Деплой файлов DEV → PROD
# ВНИМАНИЕ: Деплоим с DEV, а не с локала!
# Это гарантирует, что на прод попадает только проверенное.
# ============================================================
source "$(dirname "$0")/config.sh"

echo "=========================================="
echo "  ДЕПЛОЙ НА ПРОД — obrprofi.ru"
echo "  $TIMESTAMP"
echo "=========================================="
echo ""
echo "  !!! ВНИМАНИЕ: ДЕПЛОЙ НА БОЕВОЙ САЙТ !!!"
echo ""
read -p "  Вы уверены? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "Отменено."
    exit 0
fi

# 1. Полный бэкап ПРОД
echo ""
echo "[1/4] Полный бэкап ПРОД..."
bash "$(dirname "$0")/backup-prod.sh"

# 2. Копирование файлов DEV → PROD на сервере
echo ""
echo "[2/4] Копирую файлы DEV → PROD на сервере..."
run_ssh "
cp -a $DEV_PATH/assets/* $PROD_PATH/assets/
cp -a $DEV_PATH/core/components/* $PROD_PATH/core/components/ 2>/dev/null
echo 'Files copied'
"

# 3. Очистка кеша
echo ""
echo "[3/4] Очищаю кеш на ПРОД..."
run_ssh "rm -rf $PROD_PATH/core/cache/*"

# 4. Проверка
echo ""
echo "[4/4] Проверяю доступность..."
STATUS=$(run_ssh "curl -sI https://obrprofi.ru/ 2>/dev/null | head -1")
echo "      Статус: $STATUS"

echo ""
echo "=========================================="
echo "  ДЕПЛОЙ НА ПРОД ЗАВЕРШЁН"
echo "  Проверьте: https://obrprofi.ru"
echo "  Админка:   https://obrprofi.ru/manager/"
echo ""
echo "  Если что-то не так — откат:"
echo "  bash scripts/rollback-prod.sh"
echo "=========================================="
