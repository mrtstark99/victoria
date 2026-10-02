#!/usr/bin/env bash
# ==============================================================================
# Linux/CI 1-Click Deployment Script for Bright Education v1
# Target: brhub-web (VM101, 192.168.0.110)
# ==============================================================================

set -euo pipefail

echo "Deployment is disabled for victoria-v1 until its own server host and document root are configured." >&2
exit 2

echo "Deployment is disabled for victoria-v1 until its own server host and document root are configured." >&2
exit 2

if [[ ! "$REMOTE_DIR" =~ ^/[A-Za-z0-9_./-]+$ || "$REMOTE_DIR" == *".."* ]]; then
    echo "REMOTE_DIR must be a safe absolute path without traversal segments." >&2
    exit 2
fi

echo "=========================================================="
echo "  BRIGHT EDUCATION v1 - LINUX DEPLOYMENT SCRIPT           "
echo "  Target: $SERVER_USER@$SERVER_HOST:$REMOTE_DIR           "
echo "=========================================================="

echo "==> [1/5] Testing SSH connection to $SERVER_HOST..."
ssh -i "$SSH_KEY" -o StrictHostKeyChecking=yes "$SERVER_USER@$SERVER_HOST" "echo 'SSH_OK'"

echo "==> [2/5] Creating remote database backup..."
ssh -i "$SSH_KEY" -o StrictHostKeyChecking=yes "$SERVER_USER@$SERVER_HOST" "
    sudo mkdir -p $REMOTE_DIR/database/backups
    if [ -f $REMOTE_DIR/database/blog.db ]; then
        sudo cp -p $REMOTE_DIR/database/blog.db $REMOTE_DIR/database/backups/blog.db.\$(date +%Y%m%d_%H%M%S).bak
        echo '[OK] Database backed up.'
    fi
"

echo "==> [3/5] Packaging and transferring application..."
TMP_ARCHIVE="/tmp/bright_edu_deploy.tar.gz"
tar --exclude=".git" \
    --exclude="database/blog.db*" \
    --exclude="database/.secret_key" \
    --exclude="database/.initial_credentials" \
    --exclude=".env" \
    --exclude=".env.*" \
    --exclude="error.log" \
    --exclude="*.log" \
    -czf "$TMP_ARCHIVE" .

scp -i "$SSH_KEY" -o StrictHostKeyChecking=yes "$TMP_ARCHIVE" "$SERVER_USER@$SERVER_HOST:/tmp/deploy_package.tar.gz"
rm -f "$TMP_ARCHIVE"

echo "==> [4/5] Extracting & Applying Updates..."
ssh -i "$SSH_KEY" -o StrictHostKeyChecking=yes "$SERVER_USER@$SERVER_HOST" "
    set -e
    sudo mkdir -p $REMOTE_DIR
    sudo tar -xzf /tmp/deploy_package.tar.gz -C $REMOTE_DIR/
    rm -f /tmp/deploy_package.tar.gz

    sudo mkdir -p $REMOTE_DIR/database $REMOTE_DIR/public/uploads
    sudo chown -R root:root $REMOTE_DIR
    sudo find $REMOTE_DIR -type d -exec chmod 755 {} +
    sudo find $REMOTE_DIR -type f -exec chmod 644 {} +
    sudo mkdir -p $REMOTE_DIR/public/uploads/posts $REMOTE_DIR/public/uploads/pages
    sudo chown www:www $REMOTE_DIR/database
    sudo chmod 750 $REMOTE_DIR/database
    sudo find $REMOTE_DIR/database -maxdepth 1 -type f -name 'blog.db*' -exec chown www:www {} +
    sudo find $REMOTE_DIR/database -maxdepth 1 -type f -name 'blog.db*' -exec chmod 640 {} +
    sudo chown -R www:www $REMOTE_DIR/public/uploads
    sudo find $REMOTE_DIR/public/uploads -type d -exec chmod 750 {} +
    sudo find $REMOTE_DIR/public/uploads -type f -exec chmod 640 {} +
    sudo chown www:www $REMOTE_DIR/database/blog.db 2>/dev/null || true
    sudo chmod 640 $REMOTE_DIR/database/blog.db 2>/dev/null || true
    sudo chmod 600 $REMOTE_DIR/database/.secret_key $REMOTE_DIR/database/.initial_credentials 2>/dev/null || true

    echo '==> Running migrations...'
    if [ -f $REMOTE_DIR/database/migrate_bright_edu.php ]; then
        sudo -u www php $REMOTE_DIR/database/migrate_bright_edu.php
    fi
    if [ -f $REMOTE_DIR/database/migrate_consultations_and_qa.php ]; then
        sudo -u www php $REMOTE_DIR/database/migrate_consultations_and_qa.php
    fi
    if [ -f $REMOTE_DIR/database/migrate_seo_enhancements.php ]; then
        sudo -u www php $REMOTE_DIR/database/migrate_seo_enhancements.php
    fi
    if [ -f $REMOTE_DIR/database/migrate_post_element_contract.php ]; then
        sudo -u www php $REMOTE_DIR/database/migrate_post_element_contract.php
    fi
    sudo chown www:www $REMOTE_DIR/database/blog.db
    sudo chmod 640 $REMOTE_DIR/database/blog.db
    sudo chown root:www $REMOTE_DIR/database/.secret_key 2>/dev/null || true
    sudo chmod 640 $REMOTE_DIR/database/.secret_key 2>/dev/null || true
    sudo chmod 600 $REMOTE_DIR/database/.initial_credentials 2>/dev/null || true

    echo '==> Reloading web services...'
    if systemctl is-active --quiet php-fpm-82; then
        sudo systemctl reload php-fpm-82
    elif [ -f /etc/init.d/php-fpm-82 ]; then
        sudo /etc/init.d/php-fpm-82 reload
    fi

    if systemctl is-active --quiet nginx; then
        sudo systemctl reload nginx
    elif [ -f /etc/init.d/nginx ]; then
        sudo /etc/init.d/nginx reload
    fi
"

echo "==> [5/5] Performing health check..."
HTTP_CODE=$(curl -sS -o /dev/null -w "%{http_code}" https://blog.dev-br.xyz || echo "000")
echo "HTTP Response: $HTTP_CODE"
if [ "$HTTP_CODE" -eq 200 ] || [ "$HTTP_CODE" -eq 301 ] || [ "$HTTP_CODE" -eq 302 ]; then
    echo "==> DEPLOYMENT SUCCESSFUL! Website is online."
else
    echo "==> Health check warning: returned code $HTTP_CODE"
fi
