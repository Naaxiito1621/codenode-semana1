# Actividad Final — Gestor de Inventario

**Despliegue optimizado y seguro de un sistema de gestion corporativo**
**Empresa: TecnoSoluciones S.A.**

---

## Requisitos previos

- **Windows 10/11**
- **XAMPP** instalado (incluye Apache, MySQL y PHP) → [Descargar XAMPP](https://www.apachefriends.org/es/index.html)
- Ejecutar XAMPP como **Administrador**

---

## Estructura del proyecto

```
actividad-final/
├── conf/
│   ├── httpd-vhosts-inventario.conf   ← VirtualHost HTTP + HTTPS
│   └── httpd-performance.conf         ← Optimizacion de rendimiento
├── htdocs/
│   └── inventario/
│       ├── .htaccess                  ← Control de acceso
│       ├── index.php                  ← Listado de productos
│       ├── registrar.php              ← Formulario de registro
│       ├── conexion.php               ← Conexion a la base de datos
│       └── css/
│           └── style.css              ← Estilos visuales
├── sql/
│   └── empresa_db.sql                 ← Creacion de la base de datos
├── scripts/
│   ├── generar-ssl.bat                ← Generar certificado SSL
│   ├── generar-htpasswd.bat           ← Generar archivo de contrasenas
│   └── prueba-carga.bat               ← Prueba de rendimiento
└── README.md                          ← Esta guia
```

---

## Paso 1 — Infraestructura y dominio personalizado

### 1.1 Configurar el dominio `inventario.local`

1. Abre **Bloc de notas como Administrador**:
   - Clic derecho en Bloc de notas → "Ejecutar como administrador"

2. Abre el archivo:
   ```
   C:\Windows\System32\drivers\etc\hosts
   ```

3. Anade esta linea al final del archivo y guarda:
   ```
   127.0.0.1    inventario.local
   ```

### 1.2 Copiar los archivos de la aplicacion

1. Copia la carpeta `htdocs/inventario/` de este proyecto a:
   ```
   C:\xampp\htdocs\inventario\
   ```
   Asegurate de que la estructura quede asi:
   ```
   C:\xampp\htdocs\inventario\
       ├── .htaccess
       ├── index.php
       ├── registrar.php
       ├── conexion.php
       └── css\
           └── style.css
   ```

### 1.3 Configurar el VirtualHost

1. Abre el archivo de VirtualHosts de Apache:
   ```
   C:\xampp\apache\conf\extra\httpd-vhosts.conf
   ```

2. Copia el contenido completo de `conf/httpd-vhosts-inventario.conf` al final del archivo.

3. Verifica que el modulo de vhosts este habilitado. Abre:
   ```
   C:\xampp\apache\conf\httpd.conf
   ```
   Busca esta linea y asegurate de que **NO** tenga un `#` delante:
   ```
   Include conf/extra/httpd-vhosts.conf
   ```

---

## Paso 2 — Optimizacion del rendimiento

### 2.1 Habilitar modulos necesarios

1. Abre `C:\xampp\apache\conf\httpd.conf`

2. Busca estas lineas y quita el `#` del principio si lo tienen (para activarlas):
   ```
   LoadModule cache_module modules/mod_cache.so
   LoadModule cache_disk_module modules/mod_cache_disk.so
   LoadModule expires_module modules/mod_expires.so
   LoadModule deflate_module modules/mod_deflate.so
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

### 2.2 Aplicar la configuracion de rendimiento

1. Copia el contenido completo de `conf/httpd-performance.conf` y pegalo al final del archivo:
   ```
   C:\xampp\apache\conf\httpd.conf
   ```

### 2.3 Crear la carpeta de cache

1. Crea manualmente la carpeta:
   ```
   C:\xampp\tmp\cache
   ```

### 2.4 Que hace cada optimizacion

| Optimizacion | Descripcion |
|---|---|
| `ThreadsPerChild 150` | Permite hasta 150 conexiones simultaneas |
| `MaxConnectionsPerChild 10000` | Reinicia el proceso despues de 10.000 peticiones para liberar memoria |
| `KeepAlive On` | Reutiliza conexiones TCP (mas rapido) |
| `KeepAliveTimeout 5` | Cierra conexiones inactivas despues de 5 segundos |
| `Timeout 60` | Tiempo maximo de espera por peticion: 60 segundos |
| `mod_cache_disk` | Cachea archivos estaticos en disco |
| `mod_expires` | Establece tiempos de expiracion para imagenes, CSS, JS |
| `mod_deflate` | Comprime las respuestas (GZIP) para ahorrar ancho de banda |

---

## Paso 3 — Seguridad y cifrado (HTTPS + SSL)

### 3.1 Habilitar SSL en Apache

1. Abre `C:\xampp\apache\conf\httpd.conf`

2. Busca estas lineas y quita el `#` del principio:
   ```
   LoadModule ssl_module modules/mod_ssl.so
   LoadModule socache_shmcb_module modules/mod_socache_shmcb.so
   Include conf/extra/httpd-ssl.conf
   ```

### 3.2 Generar el certificado SSL autofirmado

1. Haz doble clic en el script (o clic derecho → Ejecutar como administrador):
   ```
   scripts\generar-ssl.bat
   ```

2. Esto generara dos archivos:
   - `C:\xampp\apache\conf\ssl\inventario.local.crt` (certificado)
   - `C:\xampp\apache\conf\ssl\inventario.local.key` (clave privada)

> **Nota:** Como el certificado es autofirmado, el navegador mostrara una advertencia
> de seguridad. Esto es normal en entorno de desarrollo. Haz clic en "Avanzado" →
> "Continuar al sitio".

### 3.3 Generar el archivo de contrasenas (.htpasswd)

1. Haz doble clic en (como Administrador):
   ```
   scripts\generar-htpasswd.bat
   ```

2. Cuando te pida la contrasena, escribe la que quieras para el usuario `admin` y pulsa Enter.

3. El script crea:
   ```
   C:\xampp\passwords\.htpasswd
   ```

### 3.4 Verificar que AllowOverride esta activo

1. Abre `C:\xampp\apache\conf\httpd.conf`

2. Busca el bloque `<Directory "C:/xampp/htdocs">` y asegurate de que diga:
   ```
   AllowOverride All
   ```
   Si pone `AllowOverride None`, cambialo a `All`.

---

## Paso 4 — Base de datos y aplicacion PHP

### 4.1 Iniciar MySQL

1. Abre el **Panel de Control de XAMPP**
2. Haz clic en **Start** junto a **MySQL**

### 4.2 Crear la base de datos

**Opcion A — Desde phpMyAdmin (interfaz web):**

1. Abre en el navegador: `http://localhost/phpmyadmin`
2. Haz clic en la pestana **SQL**
3. Copia el contenido completo del archivo `sql/empresa_db.sql` y pegalo
4. Haz clic en **Continuar/Ejecutar**

**Opcion B — Desde la linea de comandos:**

1. Abre una terminal (CMD) y ejecuta:
   ```cmd
   C:\xampp\mysql\bin\mysql.exe -u root < "RUTA\actividad-final\sql\empresa_db.sql"
   ```
   Sustituye `RUTA` por la ubicacion donde tengas el proyecto.

### 4.3 Verificar la base de datos

En phpMyAdmin, en el panel izquierdo deberias ver:
- Base de datos: `empresa_db`
- Tabla: `productos` (con 5 productos de ejemplo)

### 4.4 Detalles tecnicos de la aplicacion PHP

| Aspecto | Implementacion |
|---|---|
| Conexion a BD | MySQLi con `new mysqli()` y charset UTF-8 |
| Prevencion de inyeccion SQL | Consultas preparadas con `prepare()` + `bind_param()` |
| Prevencion de XSS | `htmlspecialchars()` en toda salida |
| Validacion | Validacion en servidor: campos obligatorios, valores numericos >= 0 |

---

## Paso 5 — Arrancar y verificar

### 5.1 Reiniciar Apache

1. En el Panel de Control de XAMPP:
   - Si Apache esta corriendo, haz clic en **Stop** y luego **Start**
   - Si no esta corriendo, haz clic en **Start**

2. Si Apache no arranca, revisa el log de errores:
   ```
   C:\xampp\apache\logs\error.log
   ```

### 5.2 Probar la aplicacion

1. Abre en el navegador:
   ```
   https://inventario.local
   ```

2. El navegador mostrara una advertencia SSL (certificado autofirmado):
   - Chrome: Haz clic en "Avanzado" → "Acceder a inventario.local (no seguro)"
   - Firefox: Haz clic en "Avanzado" → "Aceptar el riesgo y continuar"

3. Te pedira **usuario y contrasena** (los que creaste con el script .htpasswd):
   - Usuario: `admin`
   - Contrasena: la que elegiste

4. Deberias ver el listado de productos con los 5 productos de ejemplo.

5. Haz clic en "Registrar producto" y agrega uno nuevo para probar.

---

## Paso 6 — Prueba de carga (Monitoreo)

### 6.1 Ejecutar la prueba de rendimiento

1. Haz doble clic en:
   ```
   scripts\prueba-carga.bat
   ```

2. El script usa **Apache Bench (ab)** que viene incluido con XAMPP.

3. Envia 100 peticiones con 10 conexiones simultaneas.

### 6.2 Interpretar los resultados

Busca estos valores en la salida:

| Indicador | Significado | Valor recomendado |
|---|---|---|
| `Requests per second` | Peticiones atendidas por segundo | Cuanto mayor, mejor |
| `Time per request` | Tiempo medio por peticion | < 100 ms es bueno |
| `Failed requests` | Peticiones fallidas | Debe ser **0** |
| `Transfer rate` | Velocidad de transferencia | Cuanto mayor, mejor |

> **Nota:** La prueba con HTTPS puede dar error de certificado con `ab`.
> Esto es normal con certificados autofirmados. Los resultados de HTTP son
> suficientes para validar el rendimiento.

---

## Resumen de puertos y URLs

| Servicio | URL / Puerto |
|---|---|
| HTTP (redirige a HTTPS) | `http://inventario.local` (puerto 80) |
| HTTPS | `https://inventario.local` (puerto 443) |
| phpMyAdmin | `http://localhost/phpmyadmin` |
| MySQL | `localhost:3306` |

---

## Resolucion de problemas

| Problema | Solucion |
|---|---|
| Apache no arranca | Revisa `C:\xampp\apache\logs\error.log`. Puede ser que otro programa (Skype, IIS) use el puerto 80 o 443 |
| "No se puede acceder a inventario.local" | Verifica que editaste el archivo `hosts` correctamente |
| Error de conexion a la base de datos | Asegurate de que MySQL este corriendo en XAMPP y de que ejecutaste el SQL |
| Aviso SSL en el navegador | Es normal con certificados autofirmados, acepta la excepcion |
| .htaccess no funciona | Verifica que `AllowOverride All` esta configurado en httpd.conf |
| Error 500 Internal Server Error | Revisa los logs de Apache y verifica que PHP esta activo |

---

## Checklist final

- [ ] Archivo `hosts` editado con `127.0.0.1 inventario.local`
- [ ] Archivos de la app copiados a `C:\xampp\htdocs\inventario\`
- [ ] VirtualHost configurado en `httpd-vhosts.conf`
- [ ] Modulos habilitados (ssl, cache, expires, deflate, rewrite)
- [ ] Optimizaciones de rendimiento anadidas a `httpd.conf`
- [ ] Carpeta de cache creada en `C:\xampp\tmp\cache`
- [ ] Certificado SSL generado con `generar-ssl.bat`
- [ ] Archivo `.htpasswd` generado con `generar-htpasswd.bat`
- [ ] `AllowOverride All` verificado en `httpd.conf`
- [ ] Base de datos `empresa_db` creada desde `empresa_db.sql`
- [ ] Apache y MySQL iniciados en XAMPP
- [ ] Aplicacion accesible en `https://inventario.local`
- [ ] Prueba de carga ejecutada con `prueba-carga.bat`
