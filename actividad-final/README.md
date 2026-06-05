# Actividad Final — Gestor de Inventario

**Despliegue optimizado y seguro de un sistema de gestión corporativo**
**Empresa: TecnoSoluciones S.A.**

---

## Índice

1. [Requisitos previos](#requisitos-previos)
2. [Estructura del proyecto](#estructura-del-proyecto)
3. [Paso 1 — Infraestructura y dominio personalizado](#paso-1--infraestructura-y-dominio-personalizado)
4. [Paso 2 — Optimización del rendimiento](#paso-2--optimización-del-rendimiento)
5. [Paso 3 — Seguridad y cifrado (HTTPS + SSL)](#paso-3--seguridad-y-cifrado-https--ssl)
6. [Paso 4 — Base de datos y aplicación PHP](#paso-4--base-de-datos-y-aplicación-php)
7. [Paso 5 — Arrancar y verificar](#paso-5--arrancar-y-verificar)
8. [Paso 6 — Prueba de carga (Monitoreo)](#paso-6--prueba-de-carga-monitoreo)
9. [Explicación detallada del código PHP](#explicación-detallada-del-código-php)
10. [Explicación detallada de cada archivo de configuración](#explicación-detallada-de-cada-archivo-de-configuración)
11. [Resolución de problemas](#resolución-de-problemas)
12. [Checklist final](#checklist-final)

---

## Requisitos previos

Antes de empezar, necesitas tener instalado lo siguiente en tu PC con **Windows 10 u 11**:

### Instalar XAMPP

XAMPP es un paquete que incluye Apache (servidor web), MySQL/MariaDB (base de datos) y PHP (lenguaje de programación), todo preconfigurado para funcionar en Windows.

1. Ve a la página oficial: [https://www.apachefriends.org/es/index.html](https://www.apachefriends.org/es/index.html)
2. Descarga la versión para **Windows** (el archivo se llama algo como `xampp-windows-x64-8.2.x-installer.exe`)
3. Ejecuta el instalador:
   - Cuando pregunte qué componentes instalar, asegúrate de que estén marcados: **Apache**, **MySQL**, **PHP** y **phpMyAdmin**
   - Deja la ruta de instalación por defecto: `C:\xampp`
   - Completa la instalación
4. **Importante**: Siempre ejecuta el Panel de Control de XAMPP como **Administrador**:
   - Ve a `C:\xampp\xampp-control.exe`
   - Clic derecho → **Ejecutar como administrador**

### ¿Qué es cada componente?

| Componente | ¿Qué es? | ¿Para qué lo usamos? |
|---|---|---|
| **Apache** | Servidor web HTTP | Sirve las páginas PHP al navegador |
| **MySQL/MariaDB** | Sistema de base de datos relacional | Almacena los productos del inventario |
| **PHP** | Lenguaje de programación del lado del servidor | Procesa la lógica de la aplicación (listar, registrar) |
| **phpMyAdmin** | Interfaz web para gestionar MySQL | Facilita crear tablas y ejecutar consultas SQL |

---

## Estructura del proyecto

```
actividad-final/
├── conf/                                   ← Archivos de configuración de Apache
│   ├── httpd-vhosts-inventario.conf        ← VirtualHost HTTP (80) + HTTPS (443)
│   └── httpd-performance.conf              ← Optimización de rendimiento
├── htdocs/                                 ← Archivos de la aplicación web
│   └── inventario/
│       ├── .htaccess                       ← Control de acceso con contraseña
│       ├── index.php                       ← Página principal: listado de productos
│       ├── registrar.php                   ← Formulario para registrar productos
│       ├── conexion.php                    ← Archivo de conexión a la base de datos
│       └── css/
│           └── style.css                   ← Estilos visuales de la aplicación
├── sql/
│   └── empresa_db.sql                      ← Script SQL para crear la base de datos
├── scripts/
│   ├── generar-ssl.bat                     ← Script para generar certificado SSL
│   ├── generar-htpasswd.bat                ← Script para generar archivo de contraseñas
│   └── prueba-carga.bat                    ← Script para prueba de rendimiento
└── README.md                               ← Esta guía
```

### ¿Qué hace cada carpeta?

- **`conf/`**: Contiene los archivos de configuración que deberás copiar en la configuración de Apache. Estos archivos NO funcionan solos; deben ser incluidos/copiados dentro de los archivos de configuración de XAMPP.
- **`htdocs/inventario/`**: Es la aplicación web completa. Todo el contenido de esta carpeta se copia a `C:\xampp\htdocs\inventario\` para que Apache pueda servirla.
- **`sql/`**: Contiene el script SQL que crea la base de datos y la tabla con datos de ejemplo.
- **`scripts/`**: Scripts `.bat` (archivos por lotes de Windows) que automatizan tareas como generar certificados SSL y contraseñas.

---

## Paso 1 — Infraestructura y dominio personalizado

### ¿Qué vamos a hacer?

Vamos a configurar nuestro PC para que cuando escribamos `inventario.local` en el navegador, el sistema sepa que debe buscar la página en nuestro propio servidor Apache (en `localhost`, es decir, `127.0.0.1`). Además, configuraremos un **VirtualHost** en Apache para que responda específicamente a ese dominio.

### ¿Qué es un VirtualHost?

Un VirtualHost permite que un solo servidor Apache sirva múltiples sitios web diferentes. Cada VirtualHost se asocia a un nombre de dominio. Cuando un navegador pide `inventario.local`, Apache busca qué VirtualHost tiene configurado ese `ServerName` y le sirve los archivos de esa carpeta.

### ¿Qué es el archivo `hosts` de Windows?

El archivo `hosts` es un archivo del sistema operativo que actúa como un "mini DNS local". Antes de que tu PC busque un dominio en Internet, primero mira este archivo. Si encuentra una línea como `127.0.0.1 inventario.local`, sabe que `inventario.local` apunta a tu propia máquina sin salir a Internet.

---

### 1.1 Configurar el dominio `inventario.local` en el archivo hosts

**Paso a paso detallado:**

1. **Abre el Bloc de notas como Administrador** (esto es obligatorio porque el archivo `hosts` es un archivo del sistema protegido):
   - Haz clic en el botón **Inicio** de Windows (esquina inferior izquierda)
   - Escribe `bloc de notas` o `notepad`
   - Cuando aparezca la aplicación, haz **clic derecho** sobre ella
   - Selecciona **"Ejecutar como administrador"**
   - Si aparece una ventana de confirmación (UAC), haz clic en **"Sí"**

2. **Abre el archivo hosts**:
   - En el Bloc de notas, ve a **Archivo → Abrir**
   - En la barra de dirección de arriba, escribe esta ruta y pulsa Enter:
     ```
     C:\Windows\System32\drivers\etc\
     ```
   - **Importante**: En la esquina inferior derecha del cuadro de diálogo, donde dice "Documentos de texto (*.txt)", cámbialo a **"Todos los archivos (*.*)"**. Si no haces esto, no verás el archivo `hosts`.
   - Selecciona el archivo llamado `hosts` (sin extensión) y haz clic en **Abrir**

3. **Añade la línea del dominio**:
   - Ve al final del archivo (después de todas las líneas existentes)
   - Añade una nueva línea vacía y escribe exactamente esto:
     ```
     127.0.0.1    inventario.local
     ```
   - **Explicación**: `127.0.0.1` es la dirección IP de "tu propia máquina" (localhost). Estamos diciendo: "cuando alguien busque `inventario.local`, redirige a mi PC".

4. **Guarda el archivo**:
   - Pulsa `Ctrl + S` o ve a **Archivo → Guardar**
   - Si no puedes guardar, es porque no abriste el Bloc de notas como Administrador. Cierra y repite desde el paso 1.

5. **Verifica que funciona**:
   - Abre una ventana de **CMD** (Símbolo del sistema):
     - Pulsa `Win + R`, escribe `cmd` y pulsa Enter
   - Escribe este comando:
     ```cmd
     ping inventario.local
     ```
   - Deberías ver algo como:
     ```
     Haciendo ping a inventario.local [127.0.0.1] con 32 bytes de datos:
     Respuesta desde 127.0.0.1: bytes=32 tiempo<1m TTL=128
     ```
   - Si ves `127.0.0.1` en la respuesta, ¡está bien configurado!

---

### 1.2 Copiar los archivos de la aplicación a XAMPP

**¿Por qué hacemos esto?**

Apache sirve los archivos web desde la carpeta `C:\xampp\htdocs\`. Para que nuestra aplicación sea accesible, debemos copiar la carpeta `inventario` dentro de `htdocs`.

**Paso a paso detallado:**

1. **Abre el Explorador de archivos** (pulsa `Win + E`)

2. **Navega hasta la carpeta del proyecto descargado**:
   - Ve a donde hayas descargado/clonado este repositorio
   - Entra en la carpeta `actividad-final/htdocs/inventario/`

3. **Copia toda la carpeta `inventario`**:
   - Haz clic derecho sobre la carpeta `inventario` → **Copiar** (o `Ctrl + C`)

4. **Pega en la carpeta de XAMPP**:
   - Navega a `C:\xampp\htdocs\`
   - Haz clic derecho en un espacio vacío → **Pegar** (o `Ctrl + V`)

5. **Verifica la estructura resultante**:
   Debe quedar exactamente así:
   ```
   C:\xampp\htdocs\inventario\
       ├── .htaccess
       ├── index.php
       ├── registrar.php
       ├── conexion.php
       └── css\
           └── style.css
   ```

> **Nota sobre el archivo `.htaccess`**: Este archivo empieza con un punto, por lo que Windows lo puede ocultar. Para ver archivos ocultos en el Explorador de archivos: haz clic en **Vista** (en la barra superior) y marca la casilla **"Elementos ocultos"**.

---

### 1.3 Configurar el VirtualHost en Apache

**¿Qué vamos a hacer?**

Vamos a decirle a Apache: "cuando alguien acceda a `inventario.local`, muéstrale los archivos que están en `C:\xampp\htdocs\inventario\`".

**Paso a paso detallado:**

1. **Abre el archivo de VirtualHosts**:
   - Navega con el Explorador de archivos a:
     ```
     C:\xampp\apache\conf\extra\
     ```
   - Haz clic derecho en el archivo `httpd-vhosts.conf` → **Abrir con** → **Bloc de notas**

2. **Copia el contenido del VirtualHost**:
   - Abre el archivo `conf/httpd-vhosts-inventario.conf` de este proyecto (con el Bloc de notas)
   - Selecciona TODO el contenido (`Ctrl + A`) y cópialo (`Ctrl + C`)
   - Ve al archivo `httpd-vhosts.conf` que abriste en el paso 1
   - Ve al **final del archivo** y pega el contenido (`Ctrl + V`)
   - Guarda el archivo (`Ctrl + S`)

3. **Verifica que el módulo de VirtualHosts está habilitado**:
   - Abre el archivo principal de configuración de Apache:
     ```
     C:\xampp\apache\conf\httpd.conf
     ```
   - Busca esta línea (puedes usar `Ctrl + F` para buscar):
     ```
     Include conf/extra/httpd-vhosts.conf
     ```
   - Si la línea tiene un `#` al inicio (como `#Include conf/extra/httpd-vhosts.conf`), **elimina el `#`** para activarla
   - El símbolo `#` significa "comentario" en la configuración de Apache. Una línea comentada no se ejecuta.
   - Guarda el archivo

**¿Qué significa cada parte del VirtualHost?** (archivo `conf/httpd-vhosts-inventario.conf`):

```apache
# --- VirtualHost HTTP (puerto 80) ---
<VirtualHost *:80>
    ServerName inventario.local              # Nombre del dominio que responderá
    DocumentRoot "C:/xampp/htdocs/inventario" # Carpeta raíz de los archivos
    Redirect permanent / https://inventario.local/  # Redirige TODO a HTTPS

    <Directory "C:/xampp/htdocs/inventario">
        Options Indexes FollowSymLinks       # Permite listar archivos y seguir enlaces
        AllowOverride All                    # Permite que .htaccess funcione
        Require all granted                  # Permite acceso a todos
    </Directory>

    ErrorLog  "logs/inventario-error.log"    # Log de errores
    CustomLog "logs/inventario-access.log" combined  # Log de accesos
</VirtualHost>

# --- VirtualHost HTTPS (puerto 443) ---
<VirtualHost *:443>
    ServerName inventario.local
    DocumentRoot "C:/xampp/htdocs/inventario"

    SSLEngine on                                      # Activa SSL/HTTPS
    SSLCertificateFile    "C:/xampp/apache/conf/ssl/inventario.local.crt"  # Certificado
    SSLCertificateKeyFile "C:/xampp/apache/conf/ssl/inventario.local.key"  # Clave privada

    <Directory "C:/xampp/htdocs/inventario">
        Options -Indexes +FollowSymLinks     # -Indexes: NO listar archivos (más seguro)
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog  "logs/inventario-ssl-error.log"
    CustomLog "logs/inventario-ssl-access.log" combined
</VirtualHost>
```

**Conceptos clave:**
- `*:80` = escuchar en el puerto 80 (HTTP normal, sin cifrar)
- `*:443` = escuchar en el puerto 443 (HTTPS, cifrado con SSL)
- `Redirect permanent` = cuando alguien entre por HTTP, se le redirige automáticamente a HTTPS (más seguro)
- `AllowOverride All` = permite que el archivo `.htaccess` (control de acceso) funcione
- `-Indexes` = evita que se listen los archivos del directorio si alguien accede a una carpeta sin `index.php`

---

## Paso 2 — Optimización del rendimiento

### ¿Qué vamos a hacer?

Vamos a configurar Apache para que funcione de forma más eficiente:
- **Limitar y ajustar recursos**: cuántas conexiones puede manejar a la vez
- **Habilitar caché de disco**: guardar copias de archivos estáticos (CSS, imágenes) para no tener que procesarlos cada vez
- **Configurar tiempos de espera**: evitar que conexiones inactivas consuman recursos
- **Activar compresión GZIP**: reducir el tamaño de las respuestas para que carguen más rápido

### ¿Qué son los módulos de Apache?

Apache funciona con un sistema modular. Cada funcionalidad (caché, compresión, SSL, etc.) es un **módulo** que se puede activar o desactivar. Los módulos se activan con la directiva `LoadModule` en el archivo `httpd.conf`. Si una línea `LoadModule` tiene un `#` delante, el módulo está **desactivado**.

---

### 2.1 Habilitar los módulos necesarios

**Paso a paso detallado:**

1. **Abre el archivo de configuración principal de Apache**:
   - Navega a `C:\xampp\apache\conf\`
   - Abre `httpd.conf` con el Bloc de notas

2. **Busca y activa cada módulo** (usa `Ctrl + F` para buscar):

   Busca cada una de estas líneas y **quita el `#` del principio** si lo tienen:

   ```apache
   LoadModule cache_module modules/mod_cache.so
   ```
   **¿Qué hace?** Activa el sistema de caché de Apache. Permite almacenar respuestas en memoria o disco para servirlas más rápido la próxima vez.

   ```apache
   LoadModule cache_disk_module modules/mod_cache_disk.so
   ```
   **¿Qué hace?** Extiende el módulo de caché para guardar las respuestas en el disco duro. Así, si alguien pide el mismo archivo CSS o imagen dos veces, Apache lo sirve directamente desde la caché sin procesarlo de nuevo.

   ```apache
   LoadModule expires_module modules/mod_expires.so
   ```
   **¿Qué hace?** Permite establecer cabeceras `Expires` en las respuestas HTTP. Esto le dice al navegador: "no vuelvas a pedir este archivo durante X tiempo, usa tu copia local".

   ```apache
   LoadModule deflate_module modules/mod_deflate.so
   ```
   **¿Qué hace?** Activa la compresión GZIP. Antes de enviar una respuesta al navegador, Apache la comprime para que pese menos y viaje más rápido por la red.

   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
   **¿Qué hace?** Permite reescribir URLs. Es necesario para que las redirecciones y el archivo `.htaccess` funcionen correctamente.

3. **Guarda el archivo** (`Ctrl + S`)

> **¿Cómo saber si un módulo ya está activo?** Si la línea NO tiene `#` al principio, ya está activo. No pasa nada si ya estaba activo; simplemente no toques esa línea.

---

### 2.2 Copiar la configuración de rendimiento

**Paso a paso detallado:**

1. **Abre el archivo de rendimiento** de este proyecto:
   - Abre `conf/httpd-performance.conf` con el Bloc de notas
   - Selecciona TODO el contenido (`Ctrl + A`) y cópialo (`Ctrl + C`)

2. **Pega al final del archivo `httpd.conf`**:
   - Vuelve al archivo `C:\xampp\apache\conf\httpd.conf` que tienes abierto
   - Ve al **final del archivo** (pulsa `Ctrl + Fin` para ir al final)
   - Pega el contenido (`Ctrl + V`)
   - Guarda el archivo (`Ctrl + S`)

---

### 2.3 Crear la carpeta de caché

Apache necesita una carpeta donde guardar los archivos cacheados. Debemos crearla manualmente.

**Paso a paso detallado:**

1. **Abre el Explorador de archivos** (`Win + E`)
2. Navega a `C:\xampp\tmp\`
3. Si no existe la carpeta `tmp`, créala dentro de `C:\xampp\`
4. Dentro de `C:\xampp\tmp\`, haz clic derecho → **Nuevo** → **Carpeta**
5. Nombre la carpeta `cache` (debe quedar como `C:\xampp\tmp\cache`)

**Alternativa por CMD:**
```cmd
mkdir C:\xampp\tmp\cache
```

---

### 2.4 ¿Qué hace cada optimización? (explicación detallada)

#### A) Control de hilos (`mpm_winnt`)

```apache
<IfModule mpm_winnt_module>
    ThreadsPerChild      150
    MaxConnectionsPerChild 10000
</IfModule>
```

- **`ThreadsPerChild 150`**: En Windows, Apache usa un modelo llamado `mpm_winnt` que crea **un solo proceso** con múltiples **hilos** (threads). Cada hilo maneja una conexión. Con 150, Apache puede atender hasta 150 usuarios simultáneos. El valor por defecto suele ser 64, así que lo estamos aumentando.
- **`MaxConnectionsPerChild 10000`**: Después de atender 10.000 peticiones, el proceso de Apache se reinicia automáticamente. Esto libera memoria que pueda haberse "fugado" (memory leak). `0` significaría "nunca reiniciar".

#### B) Tiempos de espera

```apache
Timeout 60
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 5
```

- **`Timeout 60`**: Si una petición tarda más de 60 segundos en completarse, Apache la cancela. Evita que peticiones "colgadas" consuman recursos indefinidamente.
- **`KeepAlive On`**: Habilita conexiones persistentes. Cuando un navegador pide una página, también necesita pedir CSS, imágenes, JS, etc. Con KeepAlive activado, se reutiliza la misma conexión TCP para todas esas peticiones en vez de abrir una nueva cada vez. Esto es **mucho más eficiente**.
- **`MaxKeepAliveRequests 100`**: Máximo 100 peticiones por conexión persistente.
- **`KeepAliveTimeout 5`**: Si pasan 5 segundos sin que llegue una nueva petición por la misma conexión, se cierra. Evita mantener conexiones inactivas ocupando recursos.

#### C) Caché de disco

```apache
<IfModule mod_cache.c>
    <IfModule mod_cache_disk.c>
        CacheEnable disk "/"
        CacheRoot   "C:/xampp/tmp/cache"
        CacheDirLevels 2
        CacheDirLength 1
        CacheDefaultExpire 3600
        CacheMaxExpire 86400
    </IfModule>
</IfModule>
```

- **`CacheEnable disk "/"`**: Activa la caché en disco para TODO el sitio web (`/` = raíz).
- **`CacheRoot`**: Carpeta donde Apache guardará los archivos cacheados.
- **`CacheDirLevels 2` y `CacheDirLength 1`**: Estructura de subdirectorios dentro de la caché (para organización interna).
- **`CacheDefaultExpire 3600`**: Si un archivo no tiene fecha de expiración definida, se cachea 3600 segundos (1 hora).
- **`CacheMaxExpire 86400`**: El tiempo máximo que un archivo puede estar en caché: 86400 segundos (24 horas).

#### D) Cabeceras Expires

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg  "access plus 1 month"
    ExpiresByType image/png   "access plus 1 month"
    ExpiresByType text/css    "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
</IfModule>
```

Esto le dice al **navegador del usuario**: "Una vez que descargues este archivo, no lo vuelvas a pedir durante X tiempo. Usa tu copia local". Así se reduce el tráfico de red y la carga del servidor.

- Imágenes JPEG/PNG: 1 mes
- CSS: 1 semana
- JavaScript: 1 semana

#### E) Compresión GZIP

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css
    AddOutputFilterByType DEFLATE application/javascript application/json
</IfModule>
```

Antes de enviar la respuesta al navegador, Apache comprime el contenido. Un archivo HTML de 50 KB puede reducirse a 10 KB con GZIP. El navegador lo descomprime automáticamente. Resultado: las páginas cargan más rápido.

---

## Paso 3 — Seguridad y cifrado (HTTPS + SSL)

### ¿Qué vamos a hacer?

1. **Habilitar HTTPS con un certificado SSL autofirmado**: Para que la comunicación entre el navegador y el servidor esté cifrada.
2. **Configurar .htaccess con contraseña**: Para que solo las personas con usuario y contraseña puedan acceder a la aplicación.

### Conceptos clave antes de empezar

#### ¿Qué es SSL/TLS?
SSL (Secure Sockets Layer) y su sucesor TLS (Transport Layer Security) son protocolos que **cifran** la comunicación entre el navegador y el servidor. Cuando ves `https://` en la URL (la "s" es de "secure"), significa que se está usando SSL/TLS.

#### ¿Qué es un certificado SSL?
Es un archivo digital que demuestra la identidad del servidor. Contiene una clave pública que permite cifrar los datos. Normalmente, los certificados los emite una **Autoridad Certificadora** (CA) de confianza (como Let's Encrypt, DigiCert, etc.). Un certificado **autofirmado** es uno que tú mismo generas; funciona igual para cifrar, pero el navegador mostrará una advertencia porque no fue emitido por una CA conocida.

#### ¿Qué es .htaccess?
Es un archivo de configuración de Apache que se coloca dentro de un directorio web. Permite controlar el acceso, redirecciones, y otras reglas **solo para ese directorio**. En nuestro caso, lo usamos para pedir usuario y contraseña.

#### ¿Qué es .htpasswd?
Es un archivo que contiene la lista de usuarios y sus contraseñas (cifradas con hash). Apache lo consulta cuando alguien intenta acceder a una página protegida por `.htaccess`.

---

### 3.1 Habilitar SSL en Apache

**Paso a paso detallado:**

1. **Abre `httpd.conf`**:
   ```
   C:\xampp\apache\conf\httpd.conf
   ```

2. **Busca y activa estos tres módulos/líneas** (usa `Ctrl + F`):

   **Línea 1** — Busca:
   ```apache
   LoadModule ssl_module modules/mod_ssl.so
   ```
   - Si tiene `#` delante → quita el `#`
   - **¿Qué hace?** Carga el módulo SSL de Apache, que permite manejar conexiones HTTPS.

   **Línea 2** — Busca:
   ```apache
   LoadModule socache_shmcb_module modules/mod_socache_shmcb.so
   ```
   - Si tiene `#` delante → quita el `#`
   - **¿Qué hace?** Proporciona un sistema de caché en memoria compartida que SSL necesita para almacenar sesiones y mejorar el rendimiento de las conexiones cifradas.

   **Línea 3** — Busca:
   ```apache
   Include conf/extra/httpd-ssl.conf
   ```
   - Si tiene `#` delante → quita el `#`
   - **¿Qué hace?** Incluye el archivo de configuración SSL de XAMPP que define los parámetros de cifrado.

3. **Guarda el archivo** (`Ctrl + S`)

---

### 3.2 Generar el certificado SSL autofirmado

**¿Qué vamos a generar?**
- Un archivo `.key` (clave privada): Es la clave secreta del servidor. Nunca se comparte.
- Un archivo `.crt` (certificado): Es la parte pública que contiene la información del servidor.

**Paso a paso detallado:**

1. **Ejecuta el script de generación**:
   - Navega a la carpeta `scripts\` de este proyecto
   - Haz **clic derecho** en `generar-ssl.bat` → **Ejecutar como administrador**
   - Si te pregunta si quieres permitir que la aplicación haga cambios, haz clic en **"Sí"**

2. **¿Qué hace el script por dentro?** (esto es lo que ejecuta):
   ```bat
   "C:\xampp\apache\bin\openssl.exe" req ^
       -x509 ^
       -nodes ^
       -days 365 ^
       -newkey rsa:2048 ^
       -keyout "C:\xampp\apache\conf\ssl\inventario.local.key" ^
       -out "C:\xampp\apache\conf\ssl\inventario.local.crt" ^
       -subj "/C=ES/ST=Madrid/L=Madrid/O=TecnoSoluciones SA/CN=inventario.local"
   ```

   Explicación de cada parámetro:
   - `openssl.exe`: Es la herramienta de OpenSSL que viene incluida con XAMPP
   - `req -x509`: Genera un certificado autofirmado (no necesita una CA externa)
   - `-nodes`: No cifra la clave privada con contraseña (para que Apache pueda usarla sin pedir contraseña cada vez que arranca)
   - `-days 365`: El certificado será válido durante 365 días (1 año)
   - `-newkey rsa:2048`: Genera una nueva clave RSA de 2048 bits (nivel de seguridad estándar)
   - `-keyout`: Ruta donde se guarda la clave privada
   - `-out`: Ruta donde se guarda el certificado
   - `-subj`: Información del certificado (país, ciudad, organización, dominio)

3. **Verifica que se crearon los archivos**:
   - Navega a `C:\xampp\apache\conf\ssl\`
   - Deberías ver dos archivos:
     - `inventario.local.crt` (el certificado)
     - `inventario.local.key` (la clave privada)

> **Nota importante**: Como el certificado es **autofirmado** (no emitido por una CA reconocida), el navegador mostrará una advertencia de seguridad al acceder a `https://inventario.local`. Esto es **completamente normal** en un entorno de desarrollo/pruebas local. En producción, usarías un certificado de una CA como Let's Encrypt.

---

### 3.3 Generar el archivo de contraseñas (.htpasswd)

**¿Qué vamos a hacer?**

Crear un archivo con un usuario (`admin`) y su contraseña cifrada. Apache consultará este archivo cada vez que alguien intente acceder a la aplicación.

**Paso a paso detallado:**

1. **Ejecuta el script de generación**:
   - Navega a la carpeta `scripts\` de este proyecto
   - Haz **clic derecho** en `generar-htpasswd.bat` → **Ejecutar como administrador**

2. **El script te pedirá una contraseña**:
   ```
   Creando usuario: admin
   New password: _
   ```
   - Escribe la contraseña que quieras (por ejemplo: `Admin123!`) y pulsa **Enter**
   - Te pedirá confirmarla: escríbela de nuevo y pulsa **Enter**

3. **Opcionalmente, puedes agregar más usuarios**:
   - El script preguntará: `¿Quieres agregar otro usuario? (s/n):`
   - Si escribes `s`, te pedirá el nombre y la contraseña del nuevo usuario

4. **¿Qué genera el script?**
   - Un archivo en: `C:\xampp\passwords\.htpasswd`
   - El contenido del archivo se ve algo así:
     ```
     admin:$apr1$xyz.../aBcDeFgHiJkLmNoPqRsT0
     ```
   - La contraseña NO se guarda en texto plano; se guarda como un **hash** (cifrada irreversiblemente).

5. **¿Por qué se guarda fuera de `htdocs`?**
   - El archivo `.htpasswd` se guarda en `C:\xampp\passwords\` (fuera de `C:\xampp\htdocs\`)
   - Esto es una **medida de seguridad**: si estuviera dentro de `htdocs`, alguien podría descargarlo directamente desde el navegador accediendo a `https://inventario.local/.htpasswd`
   - Al estar fuera de la carpeta web, no es accesible desde Internet

**Si necesitas crear el usuario manualmente por CMD:**
```cmd
C:\xampp\apache\bin\htpasswd.exe -c "C:\xampp\passwords\.htpasswd" admin
```
- `-c` = crear archivo nuevo (solo usar la primera vez; sin `-c` añade usuarios a uno existente)
- `admin` = nombre del usuario

---

### 3.4 Verificar que AllowOverride está activo

Para que el archivo `.htaccess` funcione, Apache debe tener habilitado `AllowOverride All` en la configuración del directorio.

**Paso a paso detallado:**

1. **Abre `httpd.conf`**:
   ```
   C:\xampp\apache\conf\httpd.conf
   ```

2. **Busca el bloque del directorio htdocs** (usa `Ctrl + F` para buscar `<Directory "C:/xampp/htdocs">`):
   ```apache
   <Directory "C:/xampp/htdocs">
       ...
       AllowOverride All
       ...
   </Directory>
   ```

3. **Si dice `AllowOverride None`**, cámbialo a `AllowOverride All`

4. **¿Qué significa esto?**
   - `AllowOverride None` = Apache ignora los archivos `.htaccess` (no se aplica ninguna regla)
   - `AllowOverride All` = Apache lee y aplica las reglas de `.htaccess` (autenticación, redirecciones, etc.)

5. **Guarda el archivo** (`Ctrl + S`)

---

### 3.5 ¿Cómo funciona el `.htaccess` de nuestra aplicación?

El archivo `.htaccess` que está en `htdocs/inventario/.htaccess` contiene:

```apache
AuthType Basic
AuthName "Acceso restringido — TecnoSoluciones S.A."
AuthUserFile "C:/xampp/passwords/.htpasswd"
Require valid-user
```

Explicación línea por línea:
- **`AuthType Basic`**: Usa autenticación HTTP básica (el navegador muestra un cuadro de diálogo pidiendo usuario y contraseña).
- **`AuthName "Acceso restringido..."`**: El mensaje que aparece en el cuadro de diálogo de autenticación.
- **`AuthUserFile "C:/xampp/passwords/.htpasswd"`**: Ruta al archivo que contiene los usuarios y contraseñas cifradas.
- **`Require valid-user`**: Solo permite el acceso a usuarios que estén en el archivo `.htpasswd` y proporcionen la contraseña correcta.

---

## Paso 4 — Base de datos y aplicación PHP

### ¿Qué vamos a hacer?

Crear la base de datos `empresa_db` con una tabla `productos` y verificar que la aplicación PHP se conecta correctamente a ella.

### Conceptos clave

#### ¿Qué es MySQLi?
MySQLi (MySQL Improved) es una extensión de PHP para interactuar con bases de datos MySQL/MariaDB. Es la versión mejorada de la antigua extensión `mysql` (ya obsoleta). Permite ejecutar consultas SQL desde PHP.

#### ¿Qué son las consultas preparadas (Prepared Statements)?
Son una técnica para ejecutar consultas SQL de forma **segura**. En vez de insertar los valores directamente en la consulta (lo que permite **inyecciones SQL**), se usa un "molde" con `?` como marcadores y luego se "rellenan" los valores por separado. Esto evita que un atacante pueda modificar la consulta SQL introduciendo código malicioso en un campo del formulario.

#### ¿Qué es una inyección SQL?
Es un ataque donde alguien introduce código SQL malicioso en un campo de entrada (por ejemplo, en el nombre del producto). Si la aplicación no usa consultas preparadas, el código malicioso se ejecutaría en la base de datos.

**Ejemplo de inyección SQL (inseguro):**
```php
// MAL — vulnerable a inyección SQL
$sql = "INSERT INTO productos (nombre) VALUES ('$nombre')";
// Si $nombre = "'; DROP TABLE productos; --"
// La consulta se convierte en:
// INSERT INTO productos (nombre) VALUES (''); DROP TABLE productos; --')
// ¡Esto BORRARÍA toda la tabla!
```

**Con consultas preparadas (seguro):**
```php
// BIEN — seguro contra inyección SQL
$stmt = $conexion->prepare("INSERT INTO productos (nombre) VALUES (?)");
$stmt->bind_param("s", $nombre);
$stmt->execute();
// El valor de $nombre SIEMPRE se trata como dato, nunca como código SQL
```

---

### 4.1 Iniciar MySQL

**Paso a paso detallado:**

1. **Abre el Panel de Control de XAMPP** (como Administrador):
   - Ve a `C:\xampp\xampp-control.exe`
   - Clic derecho → **Ejecutar como administrador**

2. **Inicia MySQL**:
   - Junto a "MySQL", haz clic en el botón **Start**
   - Espera a que el nombre "MySQL" se ponga en **verde** y aparezca el puerto `3306`
   - Si te sale una advertencia del Firewall de Windows, haz clic en **"Permitir acceso"**

3. **Si MySQL no arranca**:
   - Comprueba que el puerto 3306 no esté siendo usado por otro programa
   - Revisa los logs en `C:\xampp\mysql\data\mysql_error.log`

---

### 4.2 Crear la base de datos

Hay dos formas de hacerlo. Elige la que prefieras:

#### Opción A — Desde phpMyAdmin (interfaz gráfica, más fácil)

1. **Asegúrate de que Apache y MySQL estén iniciados** en el Panel de Control de XAMPP

2. **Abre phpMyAdmin**:
   - Abre tu navegador y ve a: `http://localhost/phpmyadmin`
   - Se abrirá la interfaz de administración de MySQL

3. **Ve a la pestaña SQL**:
   - En la parte superior de phpMyAdmin, haz clic en la pestaña **"SQL"**
   - Aparecerá un cuadro de texto grande donde puedes escribir consultas

4. **Copia el contenido del archivo SQL**:
   - Abre el archivo `sql/empresa_db.sql` de este proyecto con el Bloc de notas
   - Selecciona TODO el contenido (`Ctrl + A`) y cópialo (`Ctrl + C`)
   - Pega en el cuadro de texto de phpMyAdmin (`Ctrl + V`)

5. **Ejecuta el SQL**:
   - Haz clic en el botón **"Continuar"** o **"Ejecutar"** (Go)
   - Deberías ver un mensaje de éxito en verde

6. **Verifica**:
   - En el panel izquierdo de phpMyAdmin, deberías ver una nueva base de datos llamada `empresa_db`
   - Haz clic en ella para expandirla
   - Verás una tabla llamada `productos`
   - Haz clic en `productos` y luego en la pestaña **"Examinar"** para ver los 5 productos de ejemplo

#### Opción B — Desde la línea de comandos (CMD)

1. **Abre CMD como Administrador**:
   - Pulsa `Win + R`, escribe `cmd`, pulsa Enter

2. **Ejecuta el script SQL**:
   ```cmd
   C:\xampp\mysql\bin\mysql.exe -u root < "C:\ruta-a-tu-proyecto\actividad-final\sql\empresa_db.sql"
   ```
   Sustituye `C:\ruta-a-tu-proyecto` por la carpeta real donde tienes el proyecto.

   **Ejemplo real:**
   ```cmd
   C:\xampp\mysql\bin\mysql.exe -u root < "C:\Users\TuNombre\Desktop\actividad-final\sql\empresa_db.sql"
   ```

3. **Verifica desde CMD** (opcional):
   ```cmd
   C:\xampp\mysql\bin\mysql.exe -u root -e "USE empresa_db; SELECT * FROM productos;"
   ```
   Deberías ver los 5 productos de ejemplo en formato tabla.

---

### 4.3 ¿Qué hace el script SQL? (línea por línea)

```sql
-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS empresa_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_spanish_ci;
```
- **`CREATE DATABASE IF NOT EXISTS empresa_db`**: Crea la base de datos solo si no existe ya (seguro de ejecutar múltiples veces)
- **`CHARACTER SET utf8mb4`**: Usa UTF-8 completo para soportar todos los caracteres (incluyendo emojis y ñ)
- **`COLLATE utf8mb4_spanish_ci`**: Ordenación y comparación de texto según reglas del español (la ñ va después de la n, etc.)

```sql
USE empresa_db;
```
- Selecciona la base de datos para ejecutar los siguientes comandos dentro de ella

```sql
CREATE TABLE IF NOT EXISTS productos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100)   NOT NULL,
    descripcion     VARCHAR(500)   DEFAULT '',
    cantidad        INT            NOT NULL DEFAULT 0,
    precio          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    fecha_registro  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
- **`id INT AUTO_INCREMENT PRIMARY KEY`**: Identificador único que se incrementa automáticamente (1, 2, 3...)
- **`nombre VARCHAR(100) NOT NULL`**: Texto de hasta 100 caracteres, obligatorio
- **`descripcion VARCHAR(500) DEFAULT ''`**: Texto de hasta 500 caracteres, opcional (por defecto vacío)
- **`cantidad INT NOT NULL DEFAULT 0`**: Número entero, obligatorio, por defecto 0
- **`precio DECIMAL(10,2) NOT NULL DEFAULT 0.00`**: Número decimal con 10 dígitos totales y 2 decimales
- **`fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP`**: Fecha/hora que se rellena automáticamente al insertar
- **`ENGINE=InnoDB`**: Motor de almacenamiento que soporta transacciones y claves foráneas

```sql
INSERT INTO productos (nombre, descripcion, cantidad, precio) VALUES
    ('Teclado mecánico',     'Teclado RGB switches Cherry MX Blue',  25,  49.99),
    ('Monitor 27 pulgadas',  'Monitor IPS 4K 60Hz',                  10, 329.00),
    ('Ratón inalámbrico',    'Ratón ergonómico Bluetooth 5.0',       50,  24.50),
    ('Cable HDMI 2m',        'Cable HDMI 2.1 alta velocidad',       100,   8.99),
    ('Disco SSD 1TB',        'SSD NVMe M.2 lectura 3500MB/s',       15,  89.90);
```
- Inserta 5 productos de ejemplo para verificar que todo funciona correctamente

---

## Paso 5 — Arrancar y verificar

### 5.1 Reiniciar Apache

**Después de hacer todos los cambios de configuración**, es necesario reiniciar Apache para que los aplique.

**Paso a paso detallado:**

1. **Abre el Panel de Control de XAMPP** (como Administrador)

2. **Reinicia Apache**:
   - Si Apache ya está corriendo (aparece en verde):
     - Haz clic en **Stop** junto a Apache
     - Espera a que desaparezca el color verde
     - Haz clic en **Start**
   - Si Apache no está corriendo:
     - Haz clic en **Start**

3. **Verifica que arrancó correctamente**:
   - "Apache" debe aparecer en **verde** con los puertos `80, 443`
   - Si aparecen los puertos `80` y `443`, significa que tanto HTTP como HTTPS están funcionando

4. **Si Apache NO arranca** (aparece en rojo o muestra un error):
   - Haz clic en el botón **"Logs"** junto a Apache → **"Apache (error.log)"**
   - Los errores más comunes:
     - **"Port 80 already in use"**: Otro programa (como Skype, IIS, o W3SVC) está usando el puerto 80. Solución: cierra ese programa o cambia el puerto de Apache.
     - **"SSLCertificateFile: file does not exist"**: El certificado SSL no se ha generado. Ejecuta `generar-ssl.bat` (Paso 3.2).
     - **"Syntax error on line X"**: Hay un error de escritura en la configuración. Revisa el archivo que modificaste.

---

### 5.2 Probar la aplicación

**Paso a paso detallado:**

1. **Abre el navegador** (Chrome, Firefox o Edge)

2. **Escribe la URL**:
   ```
   https://inventario.local
   ```

3. **Advertencia SSL** (es normal):
   - **En Chrome**:
     - Verás un mensaje "Tu conexión no es privada" o "La conexión no es segura"
     - Haz clic en **"Avanzado"** (o "Advanced")
     - Haz clic en **"Acceder a inventario.local (no seguro)"** (o "Proceed to inventario.local (unsafe)")
   - **En Firefox**:
     - Verás "Advertencia: Riesgo potencial de seguridad"
     - Haz clic en **"Avanzado"**
     - Haz clic en **"Aceptar el riesgo y continuar"**
   - **En Edge**:
     - Similar a Chrome: "Avanzado" → "Continuar"

4. **Ventana de autenticación**:
   - Aparecerá un cuadro de diálogo pidiendo usuario y contraseña
   - **Usuario**: `admin`
   - **Contraseña**: la que elegiste al ejecutar `generar-htpasswd.bat`
   - Haz clic en **"Iniciar sesión"**

5. **¡Listo!** Deberías ver:
   - La página principal con el título "Gestor de Inventario" y "TecnoSoluciones S.A."
   - Una tabla con los 5 productos de ejemplo
   - Un menú de navegación con "Listado" y "Registrar producto"

6. **Prueba registrar un producto**:
   - Haz clic en **"Registrar producto"** en el menú
   - Rellena el formulario:
     - Nombre: `Webcam HD 1080p`
     - Descripción: `Webcam con micrófono integrado`
     - Cantidad: `20`
     - Precio: `39.99`
   - Haz clic en **"Registrar producto"**
   - Deberías ver el mensaje "Producto registrado correctamente" en verde
   - Vuelve al "Listado" para ver el nuevo producto en la tabla

---

## Paso 6 — Prueba de carga (Monitoreo)

### ¿Qué es una prueba de carga?

Es una prueba que simula muchos usuarios accediendo al servidor al mismo tiempo para medir:
- ¿Cuántas peticiones por segundo puede atender?
- ¿Cuánto tarda en responder a cada petición?
- ¿Hay errores bajo carga?

Usamos **Apache Bench (ab)**, una herramienta de línea de comandos que viene incluida con XAMPP.

---

### 6.1 Ejecutar la prueba

**Paso a paso detallado:**

1. **Asegúrate de que Apache está corriendo**

2. **Ejecuta el script de prueba**:
   - Navega a la carpeta `scripts\` de este proyecto
   - Haz **doble clic** en `prueba-carga.bat`

3. **¿Qué hace el script?**
   Ejecuta este comando:
   ```cmd
   "C:\xampp\apache\bin\abs.exe" -n 100 -c 10 http://inventario.local/
   ```
   Significado:
   - `abs.exe`: Apache Bench (en XAMPP para Windows se llama `abs.exe` en lugar de `ab`)
   - `-n 100`: Enviar **100 peticiones** en total
   - `-c 10`: Enviar **10 peticiones simultáneas** a la vez (simula 10 usuarios al mismo tiempo)
   - `http://inventario.local/`: La URL a la que se envían las peticiones

4. **También prueba con HTTPS**:
   ```cmd
   "C:\xampp\apache\bin\abs.exe" -n 100 -c 10 -f TLS1.2 https://inventario.local/
   ```
   - `-f TLS1.2`: Fuerza el uso de TLS 1.2 para la conexión cifrada

---

### 6.2 Interpretar los resultados

Después de ejecutar la prueba, verás una salida larga. Los valores más importantes son:

```
Concurrency Level:      10           ← Peticiones simultáneas
Time taken for tests:   0.XXX seconds ← Tiempo total de la prueba
Complete requests:      100          ← Peticiones completadas
Failed requests:        0            ← Peticiones fallidas (DEBE SER 0)
Requests per second:    XXX.XX [#/sec] ← Peticiones atendidas por segundo
Time per request:       X.XXX [ms]    ← Tiempo medio por petición
Transfer rate:          XXX.XX [Kbytes/sec] ← Velocidad de transferencia
```

**¿Qué significan y qué valores son buenos?**

| Indicador | Significado | Valor bueno |
|---|---|---|
| **Requests per second** | Cuántas peticiones atiende Apache cada segundo | > 50 es aceptable, > 200 es bueno |
| **Time per request** | Tiempo promedio que tarda cada petición | < 100 ms es bueno, < 50 ms es excelente |
| **Failed requests** | Peticiones que fallaron | **Debe ser 0** |
| **Transfer rate** | Velocidad a la que se transfieren datos | Cuanto mayor, mejor |

> **Nota**: La prueba HTTP dará mejores resultados que HTTPS porque el cifrado SSL añade procesamiento extra. La prueba HTTPS puede dar un error de certificado con `abs.exe`, lo cual es normal con certificados autofirmados. Los resultados HTTP son suficientes para validar el rendimiento.

---

## Explicación detallada del código PHP

### Archivo: `conexion.php` — Conexión a la base de datos

```php
<?php
$servidor   = "localhost";     // Dirección del servidor MySQL (en la misma máquina)
$usuario    = "root";          // Usuario por defecto de XAMPP
$contrasena = "";              // XAMPP no tiene contraseña por defecto para root
$base_datos = "empresa_db";    // Nombre de nuestra base de datos

// Crear la conexión usando MySQLi (orientado a objetos)
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificar si hay errores de conexión
if ($conexion->connect_error) {
    // die() detiene la ejecución y muestra el mensaje de error
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer la codificación de caracteres a UTF-8
// Esto es necesario para que las tildes (á, é, ñ) se muestren correctamente
$conexion->set_charset("utf8");
?>
```

**¿Por qué separamos la conexión en un archivo aparte?**
- Para no repetir el mismo código en cada página
- Si cambias los datos de conexión (servidor, usuario, etc.), solo los cambias en un lugar
- Este patrón se llama **"separación de responsabilidades"**

---

### Archivo: `index.php` — Listado de productos

Flujo del archivo:
1. Incluye `conexion.php` para tener acceso a `$conexion`
2. Ejecuta una consulta SELECT para obtener todos los productos
3. Genera una tabla HTML con los resultados

**Puntos de seguridad importantes:**

```php
// htmlspecialchars() convierte caracteres especiales HTML en entidades seguras
// Esto previene ataques XSS (Cross-Site Scripting)
echo htmlspecialchars($fila['nombre']);
// Si alguien registró un producto con nombre: <script>alert('hackeado')</script>
// htmlspecialchars lo convierte en: &lt;script&gt;alert('hackeado')&lt;/script&gt;
// Así se muestra como texto plano en vez de ejecutarse como código
```

```php
// number_format() formatea el precio con 2 decimales, coma decimal y punto para miles
echo number_format($fila['precio'], 2, ',', '.');
// 329.00 → 329,00
// 1329.50 → 1.329,50
```

---

### Archivo: `registrar.php` — Formulario de registro

Flujo del archivo:
1. Si el formulario se envió (método POST):
   a. Recoge los datos del formulario
   b. Valida los datos
   c. Inserta en la base de datos usando consulta preparada
2. Muestra el formulario HTML

**Desglose del código PHP:**

```php
// Comprobar si el formulario se envió por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger datos del formulario y limpiar espacios extras
    $nombre      = trim($_POST['nombre']      ?? '');
    // trim() quita espacios en blanco al inicio y final
    // ?? '' es el operador "null coalescing": si no existe, usa '' (cadena vacía)

    $descripcion = trim($_POST['descripcion'] ?? '');
    $cantidad    = intval($_POST['cantidad']   ?? 0);
    // intval() convierte el valor a entero (si alguien pone "abc", se convierte en 0)

    $precio      = floatval($_POST['precio']   ?? 0);
    // floatval() convierte el valor a número decimal

    // Validación del lado del servidor
    // NUNCA confiar solo en la validación HTML del navegador
    if ($nombre === '' || $cantidad < 0 || $precio < 0) {
        $mensaje = "Por favor, completa todos los campos correctamente.";
        $tipo_mensaje = "error";
    } else {
        // ========= CONSULTA PREPARADA (Prepared Statement) =========
        // Paso 1: Preparar la consulta con marcadores ?
        $stmt = $conexion->prepare(
            "INSERT INTO productos (nombre, descripcion, cantidad, precio) VALUES (?, ?, ?, ?)"
        );
        // Los ? son marcadores de posición. Los valores reales se insertan después.

        // Paso 2: Vincular los valores a los marcadores
        $stmt->bind_param("ssid", $nombre, $descripcion, $cantidad, $precio);
        // "ssid" indica el tipo de cada parámetro:
        //   s = string (texto)     → $nombre
        //   s = string (texto)     → $descripcion
        //   i = integer (entero)   → $cantidad
        //   d = double (decimal)   → $precio

        // Paso 3: Ejecutar la consulta
        if ($stmt->execute()) {
            $mensaje = "Producto registrado correctamente.";
            $tipo_mensaje = "exito";
        } else {
            $mensaje = "Error al registrar: " . htmlspecialchars($stmt->error);
            $tipo_mensaje = "error";
        }

        // Paso 4: Cerrar la consulta preparada
        $stmt->close();
    }
}
```

**¿Por qué usamos `$stmt->bind_param("ssid", ...)` en vez de concatenar directamente?**

Porque con `bind_param`, MySQL trata los valores **siempre como datos** y **nunca como código SQL**. Esto hace **imposible** la inyección SQL, independientemente de lo que escriba el usuario en el formulario.

---

## Explicación detallada de cada archivo de configuración

### Archivo: `conf/httpd-vhosts-inventario.conf`

Define dos bloques VirtualHost:

**Bloque 1: HTTP (puerto 80)**
- Recibe las peticiones que llegan por `http://inventario.local` (sin cifrar)
- Las redirige automáticamente a `https://inventario.local` (cifrado)
- Esto asegura que toda la comunicación pase por HTTPS

**Bloque 2: HTTPS (puerto 443)**
- Recibe las peticiones cifradas
- Activa el motor SSL (`SSLEngine on`)
- Apunta a los archivos del certificado y la clave privada
- Sirve los archivos desde `C:/xampp/htdocs/inventario`
- `Options -Indexes` impide que se listen los archivos del directorio (seguridad)

### Archivo: `conf/httpd-performance.conf`

Cinco secciones de optimización (detalladas en el Paso 2.4 anterior):
1. Control de hilos (mpm_winnt)
2. Tiempos de espera (KeepAlive)
3. Caché de disco (mod_cache_disk)
4. Cabeceras Expires (mod_expires)
5. Compresión GZIP (mod_deflate)

### Archivo: `.htaccess`

Control de acceso por contraseña usando autenticación HTTP Basic (detallado en el Paso 3.5 anterior).

---

## Resumen de puertos y URLs

| Servicio | URL / Puerto | Descripción |
|---|---|---|
| HTTP (redirige a HTTPS) | `http://inventario.local` (puerto 80) | Redirige automáticamente a HTTPS |
| HTTPS | `https://inventario.local` (puerto 443) | Acceso cifrado a la aplicación |
| phpMyAdmin | `http://localhost/phpmyadmin` | Administración de la base de datos |
| MySQL | `localhost:3306` | Servidor de base de datos |

---

## Resolución de problemas

| Problema | Causa probable | Solución detallada |
|---|---|---|
| **Apache no arranca** | Otro programa usa el puerto 80 o 443 | Abre CMD como Administrador y ejecuta `netstat -aon \| findstr :80` para ver qué programa usa el puerto. Cierra ese programa (frecuentemente IIS, Skype o W3SVC). Para detener IIS: `iisreset /stop` |
| **"No se puede acceder a inventario.local"** | El archivo `hosts` no se editó correctamente | Verifica que la línea `127.0.0.1 inventario.local` está en `C:\Windows\System32\drivers\etc\hosts`. Ejecuta `ping inventario.local` para confirmar |
| **Error de conexión a la base de datos** | MySQL no está corriendo o no se creó la BD | 1) Verifica que MySQL esté verde en XAMPP. 2) Abre phpMyAdmin y comprueba que existe `empresa_db` con la tabla `productos` |
| **Advertencia SSL en el navegador** | Certificado autofirmado | Es normal. Haz clic en Avanzado → Continuar. Solo es peligroso en sitios reales de Internet |
| **`.htaccess` no funciona (no pide contraseña)** | `AllowOverride` está en `None` | Abre `httpd.conf`, busca `<Directory "C:/xampp/htdocs">` y cambia `AllowOverride None` por `AllowOverride All`. Reinicia Apache |
| **Error 500 Internal Server Error** | Error en el código PHP o en la configuración | Revisa `C:\xampp\apache\logs\error.log` y `C:\xampp\apache\logs\inventario-ssl-error.log` para ver el error exacto |
| **"php no reconocido" en CMD** | PHP no está en el PATH | Usa la ruta completa: `C:\xampp\php\php.exe` en vez de solo `php` |
| **La tabla se ve vacía (sin productos)** | No se ejecutó el INSERT del SQL | Abre phpMyAdmin, selecciona `empresa_db` → `productos` → pestaña SQL, y ejecuta el bloque INSERT del archivo `empresa_db.sql` |
| **Los acentos (ñ, á, é) se ven mal** | Falta configuración UTF-8 | Verifica que `conexion.php` tiene `$conexion->set_charset("utf8")` y que la BD usa `utf8mb4` |

---

## Checklist final

Marca cada paso conforme lo vayas completando:

- [ ] **Paso 1.1**: Archivo `hosts` editado con `127.0.0.1 inventario.local`
- [ ] **Paso 1.1**: Verificado con `ping inventario.local` → responde `127.0.0.1`
- [ ] **Paso 1.2**: Archivos de la aplicación copiados a `C:\xampp\htdocs\inventario\`
- [ ] **Paso 1.3**: VirtualHost configurado en `httpd-vhosts.conf`
- [ ] **Paso 1.3**: Línea `Include conf/extra/httpd-vhosts.conf` descomentada
- [ ] **Paso 2.1**: Módulos habilitados: `cache`, `cache_disk`, `expires`, `deflate`, `rewrite`
- [ ] **Paso 2.2**: Configuración de rendimiento añadida al final de `httpd.conf`
- [ ] **Paso 2.3**: Carpeta de caché creada en `C:\xampp\tmp\cache`
- [ ] **Paso 3.1**: Módulos SSL habilitados: `ssl_module`, `socache_shmcb_module`
- [ ] **Paso 3.1**: Línea `Include conf/extra/httpd-ssl.conf` descomentada
- [ ] **Paso 3.2**: Certificado SSL generado con `generar-ssl.bat`
- [ ] **Paso 3.3**: Archivo `.htpasswd` generado con `generar-htpasswd.bat`
- [ ] **Paso 3.4**: `AllowOverride All` verificado en `httpd.conf`
- [ ] **Paso 4.1**: MySQL iniciado en XAMPP (verde, puerto 3306)
- [ ] **Paso 4.2**: Base de datos `empresa_db` creada con los 5 productos de ejemplo
- [ ] **Paso 5.1**: Apache reiniciado correctamente (verde, puertos 80 y 443)
- [ ] **Paso 5.2**: Aplicación accesible en `https://inventario.local`
- [ ] **Paso 5.2**: Autenticación con usuario y contraseña funciona
- [ ] **Paso 5.2**: Se pueden listar y registrar productos
- [ ] **Paso 6.1**: Prueba de carga ejecutada con `prueba-carga.bat`
- [ ] **Paso 6.2**: `Failed requests: 0` confirmado en los resultados
