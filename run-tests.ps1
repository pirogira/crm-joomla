# Запуск unit-тестов CRM
# Требуется: PHP 8.1+, Composer
# Использование: .\run-tests.ps1

$ErrorActionPreference = "Stop"

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "Ошибка: PHP не найден. Установите PHP 8.1+ и добавьте в PATH." -ForegroundColor Red
    exit 1
}

if (-not (Test-Path "vendor/autoload.php")) {
    Write-Host "Установка зависимостей (composer install)..." -ForegroundColor Yellow
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        composer install
    } else {
        Write-Host "Ошибка: Composer не найден. Установите Composer." -ForegroundColor Red
        exit 1
    }
}

Write-Host "Запуск тестов..." -ForegroundColor Cyan
& ./vendor/bin/phpunit --testdox
