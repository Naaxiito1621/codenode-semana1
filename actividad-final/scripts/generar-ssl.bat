@echo off
REM ============================================================
REM Generar certificado SSL autofirmado para inventario.local
REM ============================================================
REM Este script genera dos archivos:
REM   1. Clave privada (.key) — La clave secreta del servidor
REM   2. Certificado (.crt) — La parte publica que se envia al navegador
REM
REM Ejecutar como Administrador (clic derecho > Ejecutar como administrador)
REM
REM Que es un certificado SSL?
REM   Es un archivo digital que permite cifrar la comunicacion
REM   entre el navegador y el servidor (HTTPS).
REM   Un certificado "autofirmado" lo generas tu mismo.
REM   El navegador mostrara una advertencia porque no fue emitido
REM   por una Autoridad Certificadora (CA) reconocida.
REM   Esto es normal en desarrollo/pruebas locales.
REM ============================================================

echo.
echo === Generando certificado SSL autofirmado para inventario.local ===
echo.

REM Crear la carpeta para guardar el certificado y la clave
if not exist "C:\xampp\apache\conf\ssl" (
    mkdir "C:\xampp\apache\conf\ssl"
    echo Carpeta ssl creada en C:\xampp\apache\conf\ssl
)

REM Generar clave privada RSA (2048 bits) y certificado X.509
REM
REM Explicacion de cada parametro:
REM   req          = solicitud de certificado
REM   -x509        = generar certificado autofirmado (no CSR)
REM   -nodes       = no cifrar la clave con contrasena
REM                  (para que Apache la use sin pedir contrasena al arrancar)
REM   -days 365    = validez del certificado: 365 dias (1 anio)
REM   -newkey rsa:2048 = generar nueva clave RSA de 2048 bits
REM   -keyout      = ruta donde guardar la clave privada
REM   -out         = ruta donde guardar el certificado
REM   -subj        = datos del certificado:
REM                  C=ES (pais: Espania)
REM                  ST=Madrid (provincia)
REM                  L=Madrid (ciudad)
REM                  O=TecnoSoluciones SA (organizacion)
REM                  CN=inventario.local (dominio)

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
echo.
echo   Clave privada: C:\xampp\apache\conf\ssl\inventario.local.key
echo   Certificado:   C:\xampp\apache\conf\ssl\inventario.local.crt
echo   Validez:       365 dias
echo.
echo   IMPORTANTE: La clave privada (.key) NUNCA debe compartirse.
echo.
pause
