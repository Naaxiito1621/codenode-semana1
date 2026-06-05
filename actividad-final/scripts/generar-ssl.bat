@echo off
REM ============================================================
REM Generar certificado SSL autofirmado para inventario.local
REM Ejecutar como Administrador
REM ============================================================

echo.
echo === Generando certificado SSL autofirmado ===
echo.

REM Crear la carpeta para el certificado
if not exist "C:\xampp\apache\conf\ssl" (
    mkdir "C:\xampp\apache\conf\ssl"
    echo Carpeta ssl creada.
)

REM Generar clave privada y certificado (valido 365 dias)
"C:\xampp\apache\bin\openssl.exe" req ^
    -x509 ^
    -nodes ^
    -days 365 ^
    -newkey rsa:2048 ^
    -keyout "C:\xampp\apache\conf\ssl\inventario.local.key" ^
    -out "C:\xampp\apache\conf\ssl\inventario.local.crt" ^
    -subj "/C=ES/ST=Madrid/L=Madrid/O=TecnoSoluciones SA/CN=inventario.local"

echo.
echo === Certificado generado con exito ===
echo   Clave:       C:\xampp\apache\conf\ssl\inventario.local.key
echo   Certificado: C:\xampp\apache\conf\ssl\inventario.local.crt
echo.
pause
