@echo off
set "PROJECT_DIR=%~dp0"

start "Financeiro - Laravel" cmd /k "cd /d "%PROJECT_DIR%" && php artisan serve"
start "Financeiro - Vite" cmd /k "cd /d "%PROJECT_DIR%" && npm run dev"

echo Servidores iniciando...
echo Laravel: http://127.0.0.1:8000
echo Vite:    http://127.0.0.1:5173
echo.
echo Pode fechar esta janela.
pause
