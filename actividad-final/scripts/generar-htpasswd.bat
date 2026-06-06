@echo off
REM ============================================================
REM Generar archivo .htpasswd para control de acceso
REM ============================================================
REM Este script crea un archivo con usuarios y contrasenas cifradas
REM que Apache usa para proteger el acceso a la aplicacion.
REM
REM Ejecutar como Administrador (clic derecho > Ejecutar como administrador)
REM
REM Que es .htpasswd?
REM   Es un archivo de texto que contiene parejas usuario:contrasena
REM   La contrasena se guarda cifrada (hash), no en texto plano
REM   Ejemplo del contenido: admin:$apr1$xyz.../aBcDeFgHiJkL
REM
REM Por que se guarda fuera de htdocs?
REM   Si estuviera dentro de C:\xampp\htdocs\, alguien podria
REM   descargarlo desde el navegador accediendo a la URL directa.
REM   Al guardarlo en C:\xampp\passwords\, no es accesible por web.
REM ============================================================

echo.
echo === Generando archivo de contrasenas (.htpasswd) ===
echo.

REM Crear carpeta de contrasenas fuera de htdocs (mas seguro)
if not exist "C:\xampp\passwords" (
    mkdir "C:\xampp\passwords"
    echo Carpeta passwords creada en C:\xampp\passwords
)

REM Crear el usuario administrador
REM
REM Explicacion de htpasswd.exe:
REM   -c = crear archivo nuevo (SOLO la primera vez)
REM        Si ya existe el archivo y usas -c, lo sobreescribe
REM        Para agregar usuarios adicionales, NO uses -c
REM   El ultimo argumento es el nombre de usuario
REM
REM El sistema te pedira escribir la contrasena (no se muestra en pantalla)
echo Creando usuario: admin
echo (escribe la contrasena cuando te la pida y pulsa Enter)
echo.
"C:\xampp\apache\bin\htpasswd.exe" -c "C:\xampp\passwords\.htpasswd" admin

echo.
echo === Archivo .htpasswd generado ===
echo   Ubicacion: C:\xampp\passwords\.htpasswd
echo.

REM Preguntar si se quiere agregar otro usuario
REM (sin el flag -c para NO sobreescribir el archivo existente)
set /p otro="Quieres agregar otro usuario? (s/n): "
if /i "%otro%"=="s" (
    set /p usuario="Nombre del usuario: "
    "C:\xampp\apache\bin\htpasswd.exe" "C:\xampp\passwords\.htpasswd" %usuario%
    echo Usuario agregado correctamente.
)

echo.
pause
