# ==============================================================================
# 1-Click Deployment Script for Bright Education v1
# Target: brhub-web (VM101, 192.168.0.110)
# ==============================================================================

param(
    [string]$ServerHost = "",
    [string]$ServerUser = "",
    [string]$KeyPath = "",
    [string]$RemoteDir = ""
)

throw "Deployment is disabled for victoria-v1 until its own server host and document root are configured."

throw "Deployment is disabled for victoria-v1 until its own server host and document root are configured."

$ErrorActionPreference = "Stop"
if ($RemoteDir -notmatch '^/[A-Za-z0-9_./-]+$') {
    throw "RemoteDir must be an absolute path containing only letters, numbers, dots, underscores, slashes, and hyphens."
}

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  BRIGHT EDUCATION v1 - AUTOMATED PRODUCTION DEPLOYMENT   " -ForegroundColor Cyan
Write-Host "  Target: ${ServerUser}@${ServerHost}:${RemoteDir}        " -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan

# 1. Run local test suite before deploying
Write-Host "`n[1/6] Running regression and feature test suites..." -ForegroundColor Yellow
$test1 = php tests/run_tests.php
if ($LASTEXITCODE -ne 0) {
    Write-Host "[FAIL] Core tests failed! Aborting deployment." -ForegroundColor Red
    exit 1
}
$test2 = php tests/bright_edu_feature_test.php
if ($LASTEXITCODE -ne 0) {
    Write-Host "[FAIL] Bright Education feature tests failed! Aborting deployment." -ForegroundColor Red
    exit 1
}
Write-Host "[OK] All local tests passed cleanly." -ForegroundColor Green

# 2. Test SSH connectivity
Write-Host "`n[2/6] Testing SSH connectivity to $ServerHost..." -ForegroundColor Yellow
$sshTarget = "${ServerUser}@${ServerHost}"
$sshCheck = ssh -i $KeyPath -o StrictHostKeyChecking=yes -o ConnectTimeout=5 $sshTarget "echo 'SSH_OK'"
if ($sshCheck -ne "SSH_OK") {
    Write-Host "[FAIL] Unable to connect to $ServerHost via SSH. Check network / key." -ForegroundColor Red
    exit 1
}
Write-Host "[OK] SSH connection established." -ForegroundColor Green

# 3. Create server database backup
Write-Host "`n[3/6] Backing up existing database on server..." -ForegroundColor Yellow
ssh -i $KeyPath -o StrictHostKeyChecking=yes $sshTarget "sudo mkdir -p $RemoteDir/database/backups && if [ -f $RemoteDir/database/blog.db ]; then sudo cp -p $RemoteDir/database/blog.db $RemoteDir/database/backups/blog.db.`$(date +%s).bak; echo '[OK] Remote database backed up.'; fi"
if ($LASTEXITCODE -ne 0) { throw "Remote database backup failed." }

# 4. Package application
Write-Host "`n[4/6] Creating deployment package..." -ForegroundColor Yellow
$tarFile = "$PSScriptRoot\deploy_package.tar.gz"
if (Test-Path $tarFile) { Remove-Item $tarFile -Force }

tar --exclude=".git" `
    --exclude="database/blog.db*" `
    --exclude="database/.secret_key" `
    --exclude="database/.initial_credentials" `
    --exclude=".env" `
    --exclude=".env.*" `
    --exclude="error.log" `
    --exclude="*.log" `
    --exclude="deploy_package.tar.gz" `
    -czf $tarFile -C "$PSScriptRoot" .
if ($LASTEXITCODE -ne 0) { throw "Deployment package creation failed." }

Write-Host "[OK] Package created: $([math]::Round((Get-Item $tarFile).Length / 1MB, 2)) MB" -ForegroundColor Green

# 5. Transfer & Extract
Write-Host "`n[5/6] Transferring and extracting on remote server..." -ForegroundColor Yellow
$scpDest = "${ServerUser}@${ServerHost}:/tmp/deploy_package.tar.gz"
scp -i $KeyPath -o StrictHostKeyChecking=yes $tarFile $scpDest
if ($LASTEXITCODE -ne 0) { throw "Deployment package transfer failed." }

$remoteScript = @"
set -e
REMOTE_DIR='$RemoteDir'
echo '==> Deploying Bright Education v1...'
sudo mkdir -p "`$REMOTE_DIR"
sudo tar -xzf /tmp/deploy_package.tar.gz -C "`$REMOTE_DIR/"
rm -f /tmp/deploy_package.tar.gz
sudo mkdir -p "`$REMOTE_DIR/database" "`$REMOTE_DIR/public/uploads/posts" "`$REMOTE_DIR/public/uploads/pages"
sudo chown -R root:root "`$REMOTE_DIR"
sudo find "`$REMOTE_DIR" -type d -exec chmod 755 {} +
sudo find "`$REMOTE_DIR" -type f -exec chmod 644 {} +
sudo chown www:www "`$REMOTE_DIR/database"
sudo chmod 750 "`$REMOTE_DIR/database"
sudo find "`$REMOTE_DIR/database" -maxdepth 1 -type f -name 'blog.db*' -exec chown www:www {} +
sudo find "`$REMOTE_DIR/database" -maxdepth 1 -type f -name 'blog.db*' -exec chmod 640 {} +
sudo chown -R www:www "`$REMOTE_DIR/public/uploads"
sudo find "`$REMOTE_DIR/public/uploads" -type d -exec chmod 750 {} +
sudo find "`$REMOTE_DIR/public/uploads" -type f -exec chmod 640 {} +
echo '==> Running migrations...'
if [ -f "`$REMOTE_DIR/database/migrate_bright_edu.php" ]; then sudo -u www php "`$REMOTE_DIR/database/migrate_bright_edu.php"; fi
if [ -f "`$REMOTE_DIR/database/migrate_consultations_and_qa.php" ]; then sudo -u www php "`$REMOTE_DIR/database/migrate_consultations_and_qa.php"; fi
if [ -f "`$REMOTE_DIR/database/migrate_seo_enhancements.php" ]; then sudo -u www php "`$REMOTE_DIR/database/migrate_seo_enhancements.php"; fi
if [ -f "`$REMOTE_DIR/database/migrate_post_element_contract.php" ]; then sudo -u www php "`$REMOTE_DIR/database/migrate_post_element_contract.php"; fi
sudo chown www:www "`$REMOTE_DIR/database/blog.db"
sudo chmod 640 "`$REMOTE_DIR/database/blog.db"
if [ -f "`$REMOTE_DIR/database/.secret_key" ]; then sudo chown root:www "`$REMOTE_DIR/database/.secret_key"; sudo chmod 640 "`$REMOTE_DIR/database/.secret_key"; fi
if [ -f "`$REMOTE_DIR/database/.initial_credentials" ]; then sudo chmod 600 "`$REMOTE_DIR/database/.initial_credentials"; fi
echo '==> Reloading web services...'
if systemctl is-active --quiet php-fpm-82; then sudo systemctl reload php-fpm-82; elif [ -f /etc/init.d/php-fpm-82 ]; then sudo /etc/init.d/php-fpm-82 reload; else echo 'PHP-FPM service not found'; exit 1; fi
if systemctl is-active --quiet nginx; then sudo systemctl reload nginx; elif [ -f /etc/init.d/nginx ]; then sudo /etc/init.d/nginx reload; else echo 'Nginx service not found'; exit 1; fi
echo '==> Remote deployment completed.'
"@
$remoteScript | ssh -i $KeyPath -o StrictHostKeyChecking=yes $sshTarget "bash -s"
if ($LASTEXITCODE -ne 0) { throw "Remote deployment failed. See SSH output above." }

Remove-Item $tarFile -Force

# 6. Verify health
Write-Host "`n[6/6] Verifying live endpoints..." -ForegroundColor Yellow
Start-Sleep -Seconds 2

try {
    $res = Invoke-WebRequest -Uri "https://blog.dev-br.xyz" -UseBasicParsing -TimeoutSec 10
    Write-Host "[SUCCESS] https://blog.dev-br.xyz is LIVE! Status code: $($res.StatusCode) (Bytes: $($res.RawContentLength))" -ForegroundColor Green
} catch {
    Write-Host "[WARNING] Public HTTPS check returned: $_. Testing direct LAN IP..." -ForegroundColor Yellow
    try {
        $lanRes = Invoke-WebRequest -Uri "http://192.168.0.110" -Headers @{ "Host" = "blog.dev-br.xyz" } -UseBasicParsing -TimeoutSec 5
        Write-Host "[SUCCESS] Direct host response: Status $($lanRes.StatusCode)" -ForegroundColor Green
    } catch {
        Write-Host "[ERROR] Could not reach endpoint: $_" -ForegroundColor Red
        exit 1
    }
}

Write-Host "`n==========================================================" -ForegroundColor Cyan
Write-Host "  DEPLOYMENT COMPLETED SUCCESSFULLY!                      " -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan
