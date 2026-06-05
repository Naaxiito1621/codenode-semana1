@echo off
REM ============================================================
REM Prueba de carga basica con Apache Bench (ab)
REM Mide el rendimiento del servidor tras las optimizaciones
REM ============================================================

echo.
echo ====================================================
echo   PRUEBA DE CARGA — inventario.local
echo ====================================================
echo.
echo Configuracion:
echo   - Total de peticiones:    100
echo   - Peticiones simultaneas: 10
echo   - URL: https://inventario.local/
echo.
echo NOTA: Si usas HTTPS con certificado autofirmado,
echo       ab no lo soporta bien. Prueba con HTTP:
echo.
echo --- Prueba HTTP (puerto 80) ---
echo.

"C:\xampp\apache\bin\abs.exe" -n 100 -c 10 http://inventario.local/

echo.
echo ====================================================
echo.
echo --- Prueba HTTPS (puerto 443) ---
echo (puede dar error de certificado, es normal)
echo.

"C:\xampp\apache\bin\abs.exe" -n 100 -c 10 -f TLS1.2 https://inventario.local/

echo.
echo ====================================================
echo   PRUEBA COMPLETADA
echo ====================================================
echo.
echo Indicadores clave a revisar:
echo   - Requests per second: peticiones por segundo
echo   - Time per request:    tiempo medio por peticion
echo   - Failed requests:     peticiones fallidas (debe ser 0)
echo.
pause
