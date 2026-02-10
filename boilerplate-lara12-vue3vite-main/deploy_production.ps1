# deploy_production.ps1

$VPS_USER = "kelingstudio"
$VPS_IP = "103.127.136.48"
$VPS_PATH = "/var/www/html/invoice-app-complete/boilerplate-lara12-vue3vite-main"
$VPS_PASS = "Work&Study2022" # User provided password for sudo operations

Write-Host "Building for Production (Keling Studio)..." -ForegroundColor Cyan

# 1. Set Environment Variables
$env:VITE_APP_URL="https://kelingstudio.web.id"
$env:VITE_REVERB_HOST="kelingstudio.web.id"
$env:VITE_REVERB_PORT="443"
$env:VITE_REVERB_SCHEME="https"

if (-not $env:VITE_GOOGLE_CLIENT_ID) {
    Write-Host "Google Client ID not set." -ForegroundColor Yellow
    $id = Read-Host "Enter Production Google Client ID (Enter to skip)"
    if ($id) { $env:VITE_GOOGLE_CLIENT_ID = $id }
}

# 2. Build
if (Test-Path "public/build") { Remove-Item "public/build" -Recurse -Force }
if (Test-Path "build.zip") { Remove-Item "build.zip" -Force }

Write-Host "npm run build..." -ForegroundColor Green
npm run build

if ($LASTEXITCODE -ne 0) {
    Write-Host "Build Failed." -ForegroundColor Red
    exit
}

# 3. Zip
Write-Host "Zipping..." -ForegroundColor Cyan
Compress-Archive -Path "public/build" -DestinationPath "build.zip"

# 4. Upload to HOME directory (Always writable)
Write-Host "Uploading build.zip to HOME directory..." -ForegroundColor Cyan
Write-Host "Please enter SSH Password manually if asked ($VPS_USER)." -ForegroundColor Yellow

# Upload to home directory (~/)
scp -P 22 build.zip "${VPS_USER}@${VPS_IP}:build.zip"

if ($LASTEXITCODE -eq 0) {
    Write-Host "Upload Success! Installing on server..." -ForegroundColor Green
    
    # 5. Remote Install using Sudo
    # We use echo password | sudo -S to run commands as root
    
    $Commands = @(
        "echo '$VPS_PASS' | sudo -S rm -rf $VPS_PATH/public/build",
        "echo '$VPS_PASS' | sudo -S unzip -o build.zip -d $VPS_PATH/public/",
        "echo '$VPS_PASS' | sudo -S rm build.zip",
        "echo '$VPS_PASS' | sudo -S php $VPS_PATH/artisan config:clear",
        "echo '$VPS_PASS' | sudo -S php $VPS_PATH/artisan cache:clear"
    )
    
    $RemoteCmd = $Commands -join " && "
    
    ssh "${VPS_USER}@${VPS_IP}" $RemoteCmd
    
    Write-Host "Deployment Complete!" -ForegroundColor Green
} else {
    Write-Host "Upload Failed." -ForegroundColor Red
}

# Cleanup
Remove-Item Env:\VITE_APP_URL
Remove-Item Env:\VITE_REVERB_HOST
Remove-Item Env:\VITE_REVERB_PORT
Remove-Item Env:\VITE_REVERB_SCHEME
if ($id) { Remove-Item Env:\VITE_GOOGLE_CLIENT_ID }
