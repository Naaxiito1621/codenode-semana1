# Actividad Final — Despliegue Optimizado y Seguro de un Sistema de Gestión Corporativo

## Requisitos previos

- **Sistema operativo:** Windows 10 / 11
- **Software:** [XAMPP 8.2.x](https://www.apachefriends.org/es/download.html) (incluye Apache, MySQL/MariaDB, PHP y herramientas como `openssl`, `htpasswd` y `ab`)
- **Permisos:** ejecutar XAMPP y editar el archivo `hosts` como **Administrador**

> **Convención de rutas:** esta guía asume la instalación por defecto en `C:\xampp`. Si instalaste XAMPP en otra ruta, ajusta todas las referencias.

---

## 1. Infraestructura y dominio personalizado (`inventario.local`)

### 1.1 Crear la estructura de directorios

Abre una terminal (CMD o PowerShell) **como Administrador** y ejecuta:

```cmd
mkdir C:\xampp\htdocs\inventario
```

Aquí vivirán todos los archivos PHP de la aplicación.

### 1.2 Registrar el dominio local en el archivo `hosts`

El archivo `hosts` le dice a Windows que el nombre `inventario.local` apunta a tu propia máquina (`127.0.0.1`).

1. Abre el **Bloc de notas como Administrador** (clic derecho → *Ejecutar como administrador*).
2. Abre el archivo:

```
C:\Windows\System32\drivers\etc\hosts
```

3. Agrega al final:

```
127.0.0.1   inventario.local
```

4. Guarda y cierra.

**Verificación:** abre CMD y ejecuta:

```cmd
ping inventario.local
```

Deberías ver respuestas desde `127.0.0.1`.

### 1.3 Habilitar los Virtual Hosts en Apache

1. Abre el archivo de configuración principal de Apache:

```
C:\xampp\apache\conf\httpd.conf
```

2. Busca la línea (usa Ctrl+F):

```
# Include conf/extra/httpd-vhosts.conf
```

3. **Quita el `#`** para que quede:

```
Include conf/extra/httpd-vhosts.conf
```

4. Guarda el archivo.

### 1.4 Configurar el Virtual Host

1. Abre el archivo:

```
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

2. Agrega al final del archivo el siguiente bloque (puedes copiar del archivo `apache-config/httpd-vhosts.conf` incluido en este proyecto):

```apache
# ─── Host por defecto (localhost) ───
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "C:/xampp/htdocs"
</VirtualHost>

# ─── Host virtual: inventario.local ───
<VirtualHost *:80>
    ServerName inventario.local
    DocumentRoot "C:/xampp/htdocs/inventario"

    <Directory "C:/xampp/htdocs/inventario">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog  "logs/inventario-error.log"
    CustomLog "logs/inventario-access.log" common
</VirtualHost>
```

> **Importante:** `AllowOverride All` permite que el archivo `.htaccess` dentro de `inventario` funcione correctamente.

3. Reinicia Apache desde el panel de XAMPP (botón **Stop** y luego **Start**).

**Verificación:** abre tu navegador y visita `http://inventario.local`. Debería mostrarte el contenido de `C:\xampp\htdocs\inventario\` (por ahora vacío o con un index de prueba).

---

## 2. Optimización del rendimiento

### 2.1 Entender el MPM de Windows

Apache en Windows usa el módulo **mpm_winnt** (multi-hilo). Los parámetros clave son:

| Directiva                | Qué controla                                      | Valor sugerido |
|--------------------------|---------------------------------------------------|----------------|
| `ThreadsPerChild`        | Nº de hilos por proceso hijo (conexiones simultáneas) | `250`         |
| `MaxConnectionsPerChild` | Conexiones totales antes de reciclar el proceso   | `10000`        |

### 2.2 Ajustar el MPM

1. Abre `C:\xampp\apache\conf\extra\httpd-mpm.conf`.
2. Busca la sección `<IfModule mpm_winnt_module>` y modifica:

```apache
<IfModule mpm_winnt_module>
    ThreadsPerChild        250
    MaxConnectionsPerChild 10000
</IfModule>
```

3. Guarda.

### 2.3 Ajustar tiempos de conexión (Keep-Alive)

1. Abre `C:\xampp\apache\conf\httpd.conf`.
2. Busca y ajusta estas directivas (si no existen, agrégalas al final):

```apache
# ─── Tiempos de conexión ───
Timeout 30
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 5
```

**¿Qué hacen?**
- `Timeout 30`: tiempo máximo (en segundos) que Apache espera una solicitud antes de cerrar la conexión.
- `KeepAlive On`: permite reutilizar una misma conexión TCP para múltiples solicitudes (más rápido).
- `MaxKeepAliveRequests 100`: máximo de solicitudes por conexión Keep-Alive.
- `KeepAliveTimeout 5`: tiempo de espera entre solicitudes en la misma conexión.

### 2.4 Habilitar módulos de caché y compresión

En `C:\xampp\apache\conf\httpd.conf`, busca y **descomenta** (quita el `#`) las siguientes líneas:

```apache
LoadModule deflate_module modules/mod_deflate.so
LoadModule expires_module modules/mod_expires.so
LoadModule headers_module modules/mod_headers.so
LoadModule cache_module modules/mod_cache.so
LoadModule cache_disk_module modules/mod_cache_disk.so
```

> **Nota:** Algunas de estas líneas pueden ya estar descomentadas. Solo asegúrate de que **ninguna** tenga `#` al inicio.

### 2.5 Configurar la caché de disco y compresión

Agrega al final de `httpd.conf` (o crea un archivo aparte e inclúyelo):

```apache
# ═══════════════════════════════════════════
# OPTIMIZACIÓN DE RENDIMIENTO
# ═══════════════════════════════════════════

# ─── Compresión GZIP (mod_deflate) ───
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css
    AddOutputFilterByType DEFLATE text/javascript application/javascript
    AddOutputFilterByType DEFLATE application/json application/xml
</IfModule>

# ─── Caché de expiración en navegador (mod_expires) ───
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/png  "access plus 1 month"
    ExpiresByType image/gif  "access plus 1 month"
    ExpiresByType text/css   "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
</IfModule>

# ─── Caché de disco (mod_cache_disk) ───
<IfModule mod_cache_disk.c>
    CacheRoot "C:/xampp/tmp/cache"
    CacheEnable disk "/"
    CacheDirLevels 2
    CacheDirLength 1
    CacheDefaultExpire 3600
    CacheMaxExpire 86400
</IfModule>
```

Crea el directorio de caché:

```cmd
mkdir C:\xampp\tmp\cache
```

### 2.6 Reiniciar Apache y verificar

1. Reinicia Apache desde el panel de XAMPP.
2. Si Apache no arranca, revisa el log de errores en `C:\xampp\apache\logs\error.log`.

---

## 3. Seguridad y cifrado (HTTPS + Control de acceso)

### 3.1 Generar un certificado SSL autofirmado

1. Abre **CMD como Administrador**.
2. Crea los directorios para las claves (si no existen):

```cmd
mkdir C:\xampp\apache\conf\ssl.key
mkdir C:\xampp\apache\conf\ssl.crt
```

3. Genera el certificado con `openssl` (incluido en XAMPP):

```cmd
C:\xampp\apache\bin\openssl.exe req -x509 -nodes -days 365 -newkey rsa:2048 ^
  -keyout C:\xampp\apache\conf\ssl.key\inventario.key ^
  -out C:\xampp\apache\conf\ssl.crt\inventario.crt ^
  -subj "/C=AR/ST=Buenos Aires/L=CABA/O=TecnoSoluciones SA/CN=inventario.local"
```

**Explicación de los parámetros:**
| Parámetro | Significado |
|-----------|-------------|
| `-x509`   | Genera un certificado autofirmado (no una solicitud CSR) |
| `-nodes`  | No cifra la clave privada con contraseña |
| `-days 365` | Validez de 1 año |
| `-newkey rsa:2048` | Crea una clave RSA de 2048 bits |
| `-keyout` | Ruta donde se guarda la clave privada |
| `-out`    | Ruta donde se guarda el certificado |
| `-subj`   | Datos del certificado (país, provincia, ciudad, organización, dominio) |

### 3.2 Habilitar SSL en Apache

1. Abre `C:\xampp\apache\conf\httpd.conf`.
2. Descomenta estas líneas:

```apache
LoadModule ssl_module modules/mod_ssl.so
LoadModule socache_shmcb_module modules/mod_socache_shmcb.so
Include conf/extra/httpd-ssl.conf
```

### 3.3 Configurar el Virtual Host HTTPS (puerto 443)

1. Abre `C:\xampp\apache\conf\extra\httpd-ssl.conf`.
2. Busca la sección `<VirtualHost _default_:443>` y **agrega un nuevo bloque** al final del archivo (o modifica el existente):

```apache
# ─── HTTPS para inventario.local ───
<VirtualHost *:443>
    ServerName inventario.local
    DocumentRoot "C:/xampp/htdocs/inventario"

    SSLEngine on
    SSLCertificateFile    "conf/ssl.crt/inventario.crt"
    SSLCertificateKeyFile "conf/ssl.key/inventario.key"

    <Directory "C:/xampp/htdocs/inventario">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog  "logs/inventario-ssl-error.log"
    CustomLog "logs/inventario-ssl-access.log" common
</VirtualHost>
```

3. Reinicia Apache.

**Verificación:** visita `https://inventario.local` en tu navegador. Verás una advertencia de seguridad (porque el certificado es autofirmado) — haz clic en **"Avanzado" → "Continuar al sitio"**. Esto es normal para desarrollo.

### 3.4 Crear el archivo de contraseñas (`.htpasswd`)

1. Abre CMD como Administrador.
2. Ejecuta:

```cmd
C:\xampp\apache\bin\htpasswd.exe -c C:\xampp\apache\conf\.htpasswd admin
```

3. El sistema te pedirá ingresar y confirmar la contraseña para el usuario `admin`.
4. Para **agregar más usuarios** (sin el `-c`, que crearía el archivo de nuevo):

```cmd
C:\xampp\apache\bin\htpasswd.exe C:\xampp\apache\conf\.htpasswd usuario2
```

**¿Qué pasó?** Se creó el archivo `C:\xampp\apache\conf\.htpasswd` con el usuario y su contraseña cifrada (hash).

### 3.5 Crear el archivo `.htaccess`

Crea el archivo `C:\xampp\htdocs\inventario\.htaccess` con este contenido (también incluido en `htdocs/inventario/.htaccess` de este proyecto):

```apache
# Redirigir todo el tráfico HTTP a HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]

# Control de acceso con contraseña
AuthType Basic
AuthName "Acceso Restringido - TecnoSoluciones S.A."
AuthUserFile "C:/xampp/apache/conf/.htpasswd"
Require valid-user
```

> Para que `RewriteEngine` funcione, asegúrate de que `mod_rewrite` esté habilitado en `httpd.conf`:
> ```
> LoadModule rewrite_module modules/mod_rewrite.so
> ```

**Verificación:** visita `https://inventario.local`. Debería aparecer un cuadro de diálogo pidiendo usuario y contraseña. Ingresa `admin` y la contraseña que configuraste.

---

## 4. Desarrollo del backend y persistencia de datos

### 4.1 Crear la base de datos `empresa_db`

#### Opción A — Desde phpMyAdmin (interfaz gráfica)

1. Asegúrate de que MySQL está corriendo en XAMPP.
2. Abre `http://localhost/phpmyadmin`.
3. Haz clic en **"Nueva"** (panel izquierdo).
4. Nombre de la base de datos: `empresa_db`, cotejamiento: `utf8mb4_general_ci`.
5. Clic en **Crear**.
6. Selecciona la base `empresa_db` y ve a la pestaña **SQL**.
7. Pega y ejecuta el siguiente SQL (también disponible en `sql/empresa_db.sql`):

```sql
CREATE TABLE IF NOT EXISTS inventario (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)    NOT NULL,
    descripcion TEXT,
    cantidad    INT             NOT NULL DEFAULT 0,
    precio      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    fecha_registro DATETIME    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo
INSERT INTO inventario (nombre, descripcion, cantidad, precio) VALUES
('Monitor LED 24"',   'Monitor Full HD IPS de 24 pulgadas',  15, 45999.99),
('Teclado mecánico',  'Teclado mecánico RGB switch blue',    30, 18500.00),
('Mouse inalámbrico', 'Mouse ergonómico 2.4GHz',             50,  8900.50);
```

#### Opción B — Desde la terminal MySQL

1. Abre CMD y ejecuta:

```cmd
C:\xampp\mysql\bin\mysql.exe -u root
```

2. Dentro de MySQL:

```sql
CREATE DATABASE IF NOT EXISTS empresa_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE empresa_db;

CREATE TABLE IF NOT EXISTS inventario (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)    NOT NULL,
    descripcion TEXT,
    cantidad    INT             NOT NULL DEFAULT 0,
    precio      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    fecha_registro DATETIME    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO inventario (nombre, descripcion, cantidad, precio) VALUES
('Monitor LED 24"',   'Monitor Full HD IPS de 24 pulgadas',  15, 45999.99),
('Teclado mecánico',  'Teclado mecánico RGB switch blue',    30, 18500.00),
('Mouse inalámbrico', 'Mouse ergonómico 2.4GHz',             50,  8900.50);

EXIT;
```

### 4.2 Crear los archivos PHP

Copia los archivos de la carpeta `htdocs/inventario/` de este proyecto a `C:\xampp\htdocs\inventario\`.

#### `db.php` — Conexión a la base de datos (PDO)

```php
<?php
$host     = 'localhost';
$dbname   = 'empresa_db';
$user     = 'root';
$password = '';          // XAMPP usa contraseña vacía por defecto

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,   // consultas preparadas reales
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
```

**Puntos clave de seguridad:**
- `PDO::ATTR_EMULATE_PREPARES => false` → Usa consultas preparadas **nativas** del motor MySQL (protección real contra inyecciones SQL).
- `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` → Lanza excepciones en vez de errores silenciosos.

#### `index.php` — Aplicación principal (listar + registrar)

Este archivo:
1. Muestra un **formulario** para registrar un nuevo elemento.
2. **Lista** todos los elementos del inventario en una tabla.
3. Usa **consultas preparadas** (`prepare` + `execute`) para prevenir inyección SQL.

El código completo está en `htdocs/inventario/index.php` de este proyecto.

---

## 5. Monitoreo — Prueba de carga con Apache Bench

### 5.1 ¿Qué es Apache Bench (`ab`)?

Es una herramienta incluida en XAMPP que envía muchas solicitudes al servidor para medir:
- **Tiempo total** de la prueba
- **Solicitudes por segundo** (rendimiento)
- **Tiempo promedio por solicitud**
- **Tasa de errores**

### 5.2 Ejecutar la prueba

Abre CMD como Administrador y ejecuta:

```cmd
C:\xampp\apache\bin\ab.exe -n 100 -c 10 http://inventario.local/
```

| Parámetro | Significado |
|-----------|-------------|
| `-n 100`  | Total de solicitudes a enviar |
| `-c 10`   | Solicitudes concurrentes (simultáneas) |
| URL       | Dirección a probar |

> **Nota:** Si usas HTTPS necesitas la flag `-f SSL3` o probar con HTTP primero:
> ```cmd
> C:\xampp\apache\bin\ab.exe -n 100 -c 10 https://inventario.local/
> ```

### 5.3 Interpretar los resultados

Ejemplo de salida:

```
Server Software:        Apache/2.4.58
Concurrency Level:      10
Time taken for tests:   1.234 seconds
Complete requests:      100
Failed requests:        0
Requests per second:    81.04 [#/sec] (mean)
Time per request:       123.4 [ms] (mean)
Time per request:       12.34 [ms] (mean, across all concurrent requests)
```

**Métricas clave:**

| Métrica | Qué significa | Valores aceptables |
|---------|---------------|--------------------|
| `Failed requests` | Solicitudes que fallaron | Debe ser **0** |
| `Requests per second` | Rendimiento del servidor | > 50 req/s es bueno para un servidor local |
| `Time per request` (mean) | Tiempo promedio por solicitud | < 200 ms es aceptable |

### 5.4 Comparar antes y después

Para demostrar que la optimización funciona, puedes:

1. **Antes:** Deshabilitar temporalmente la caché y compresión, correr `ab`.
2. **Después:** Habilitar todo, reiniciar Apache, correr `ab` de nuevo.
3. Comparar los valores de `Requests per second` y `Time per request`.

---

## Resumen de archivos del proyecto

```
actividad-final/
├── GUIA.md                          ← Esta guía
├── htdocs/inventario/
│   ├── db.php                       ← Conexión PDO a MySQL
│   ├── index.php                    ← App principal (listar + registrar)
│   └── .htaccess                    ← Redirección HTTPS + autenticación
├── sql/
│   └── empresa_db.sql               ← Script para crear la BD y tabla
└── apache-config/
    ├── httpd-vhosts.conf            ← Configuración del Virtual Host
    ├── httpd-ssl-vhosts.conf        ← Virtual Host HTTPS (SSL)
    └── httpd-performance.conf       ← Directivas de optimización
```

## Checklist de verificación final

- [ ] `ping inventario.local` responde desde `127.0.0.1`
- [ ] `http://inventario.local` redirige a `https://inventario.local`
- [ ] El navegador pide usuario y contraseña (`.htaccess` funcionando)
- [ ] Tras autenticarse, se ve la tabla con el inventario
- [ ] Se puede agregar un nuevo elemento desde el formulario
- [ ] El nuevo elemento aparece en la tabla sin recargar manualmente
- [ ] `ab.exe -n 100 -c 10` muestra 0 solicitudes fallidas
- [ ] Los logs de Apache se generan en `C:\xampp\apache\logs\`
