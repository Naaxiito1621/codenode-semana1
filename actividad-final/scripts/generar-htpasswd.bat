@echo off
REM ============================================================
REM Generar archivo .htpasswd para control de acceso
REM Ejecutar como Administrador
REM ============================================================

echo.
echo === Generando archivo de contrasenas (.htpasswd) ===
echo.

REM Crear carpeta de contrasenas fuera de htdocs (mas seguro)
if not exist "C:\xampp\passwords" (
    mkdir "C:\xampp\passwords"
    echo Carpeta passwords creada.
)

REM Crear el usuario administrador
REM -c = crear archivo nuevo (solo la primera vez)
REM El sistema te pedira que escribas la contrasena
echo Creando usuario: admin
"C:\xampp\apache\bin\htpasswd.exe" -c "C:\xampp\passwords\.htpasswd" admin

echo.
echo === Archivo .htpasswd generado ===
echo   Ubicacion: C:\xampp\passwords\.htpasswd
echo.

REM Preguntar si se quiere agregar otro usuario
set /p otro="Quieres agregar otro usuario? (s/n): "
if /i "%otro%"=="s" (
    set /p usuario="Nombre del usuario: "
    "C:\xampp\apache\bin\htpasswd.exe" "C:\xampp\passwords\.htpasswd" %usuario%
    echo Usuario agregado.
)

echo.
pause
