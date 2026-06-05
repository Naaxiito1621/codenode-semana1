@echo off
REM ============================================================
REM Prueba de carga basica con Apache Bench (ab/abs)
REM ============================================================
REM Este script mide el rendimiento del servidor despues de
REM aplicar las optimizaciones de rendimiento.
REM
REM Que es Apache Bench?
REM   Es una herramienta de linea de comandos que envia muchas
REM   peticiones HTTP al servidor para medir su rendimiento.
REM   Viene incluida con XAMPP (como abs.exe en Windows).
REM
REM Que mide?
REM   - Cuantas peticiones por segundo puede atender el servidor
REM   - Cuanto tarda en responder cada peticion
REM   - Si hay peticiones que fallan bajo carga
REM
REM Requisito: Apache debe estar corriendo en XAMPP
REM ============================================================

echo.
echo ====================================================
echo   PRUEBA DE CARGA — inventario.local
echo ====================================================
echo.
echo Configuracion de la prueba:
echo   - Total de peticiones:    100
echo   - Peticiones simultaneas: 10  (simula 10 usuarios a la vez)
echo   - URL objetivo:           inventario.local
echo.
echo NOTA: La prueba HTTPS puede dar error de certificado
echo       con abs.exe. Esto es normal con certificados
echo       autofirmados. Los resultados HTTP son suficientes.
echo.

REM -------------------------------------------------------
REM PRUEBA 1: HTTP (puerto 80)
REM -------------------------------------------------------
REM Parametros de abs.exe:
REM   -n 100  = enviar 100 peticiones en total
REM   -c 10   = enviar 10 peticiones a la vez (concurrencia)
REM
REM La URL debe terminar con / (barra)
echo --- Prueba HTTP (puerto 80) ---
echo Ejecutando: abs.exe -n 100 -c 10 http://inventario.local/
echo.

"C:\xampp\apache\bin\abs.exe" -n 100 -c 10 http://inventario.local/

echo.
echo ====================================================
echo.

REM -------------------------------------------------------
REM PRUEBA 2: HTTPS (puerto 443)
REM -------------------------------------------------------
REM   -f TLS1.2  = forzar protocolo TLS version 1.2
REM
REM Esta prueba puede fallar por el certificado autofirmado.
REM Si falla, no te preocupes: la prueba HTTP ya es suficiente.
echo --- Prueba HTTPS (puerto 443) ---
echo (puede dar error de certificado, es normal)
echo Ejecutando: abs.exe -n 100 -c 10 -f TLS1.2 https://inventario.local/
echo.

"C:\xampp\apache\bin\abs.exe" -n 100 -c 10 -f TLS1.2 https://inventario.local/

echo.
echo ====================================================
echo   PRUEBA COMPLETADA
echo ====================================================
echo.
echo Como interpretar los resultados:
echo.
echo   Requests per second  = Peticiones atendidas por segundo
echo                          (mayor es mejor; mas de 50 es aceptable)
echo.
echo   Time per request     = Tiempo medio por peticion en milisegundos
echo                          (menor es mejor; menos de 100ms es bueno)
echo.
echo   Failed requests      = Peticiones que fallaron
echo                          (DEBE SER 0)
echo.
echo   Transfer rate        = Velocidad de transferencia en KB/s
echo                          (mayor es mejor)
echo.
pause
