-- ============================================================
-- Base de datos: empresa_db
-- Gestor de Inventario — TecnoSoluciones S.A.
-- ============================================================
--
-- Este script:
-- 1. Crea la base de datos empresa_db (si no existe)
-- 2. Crea la tabla "productos" con los campos necesarios
-- 3. Inserta 5 productos de ejemplo para verificar que todo funciona
--
-- Cómo ejecutar:
--   Opción A) Copiar y pegar en phpMyAdmin → pestaña SQL → Ejecutar
--   Opción B) Desde CMD: C:\xampp\mysql\bin\mysql.exe -u root < empresa_db.sql
-- ============================================================

-- -------------------------------------------------------
-- 1. CREAR LA BASE DE DATOS
-- -------------------------------------------------------
-- IF NOT EXISTS: solo la crea si no existe ya (seguro de ejecutar varias veces)
-- CHARACTER SET utf8mb4: codificación UTF-8 completa (soporta ñ, acentos, emojis)
-- COLLATE utf8mb4_spanish_ci: reglas de ordenación del español
--   (ci = case insensitive → "A" y "a" se consideran iguales al ordenar)
CREATE DATABASE IF NOT EXISTS empresa_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_spanish_ci;

-- -------------------------------------------------------
-- 2. SELECCIONAR LA BASE DE DATOS
-- -------------------------------------------------------
-- A partir de aquí, todos los comandos se ejecutan dentro de empresa_db
USE empresa_db;

-- -------------------------------------------------------
-- 3. CREAR LA TABLA DE PRODUCTOS
-- -------------------------------------------------------
-- IF NOT EXISTS: solo la crea si no existe ya
CREATE TABLE IF NOT EXISTS productos (
    -- id: identificador único de cada producto
    -- INT: número entero
    -- AUTO_INCREMENT: se genera automáticamente (1, 2, 3, 4...)
    -- PRIMARY KEY: es la clave primaria (identifica cada fila de forma única)
    id              INT AUTO_INCREMENT PRIMARY KEY,

    -- nombre: nombre del producto (obligatorio)
    -- VARCHAR(100): texto de hasta 100 caracteres
    -- NOT NULL: no puede estar vacío
    nombre          VARCHAR(100)   NOT NULL,

    -- descripcion: descripción del producto (opcional)
    -- VARCHAR(500): texto de hasta 500 caracteres
    -- DEFAULT '': si no se proporciona, queda como cadena vacía
    descripcion     VARCHAR(500)   DEFAULT '',

    -- cantidad: unidades en stock (obligatorio)
    -- DEFAULT 0: si no se indica, empieza en 0
    cantidad        INT            NOT NULL DEFAULT 0,

    -- precio: precio unitario en euros
    -- DECIMAL(10,2): número con hasta 10 dígitos totales y 2 decimales
    --   Ejemplo: 99999999.99 es el máximo
    -- NOT NULL DEFAULT 0.00: obligatorio, por defecto 0.00
    precio          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,

    -- fecha_registro: fecha y hora de creación del registro
    -- TIMESTAMP: tipo de dato fecha/hora
    -- DEFAULT CURRENT_TIMESTAMP: se rellena automáticamente con la fecha actual
    fecha_registro  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP

-- ENGINE=InnoDB: motor de almacenamiento recomendado
--   Soporta transacciones, claves foráneas y bloqueo a nivel de fila
-- DEFAULT CHARSET=utf8mb4: caracteres UTF-8 para la tabla
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- 4. INSERTAR DATOS DE EJEMPLO
-- -------------------------------------------------------
-- Estos 5 productos sirven para verificar que la base de datos
-- y la aplicación funcionan correctamente tras la configuración
INSERT INTO productos (nombre, descripcion, cantidad, precio) VALUES
    ('Teclado mecánico',     'Teclado RGB switches Cherry MX Blue',  25,  49.99),
    ('Monitor 27 pulgadas',  'Monitor IPS 4K 60Hz',                  10, 329.00),
    ('Ratón inalámbrico',    'Ratón ergonómico Bluetooth 5.0',       50,  24.50),
    ('Cable HDMI 2m',        'Cable HDMI 2.1 alta velocidad',       100,   8.99),
    ('Disco SSD 1TB',        'SSD NVMe M.2 lectura 3500MB/s',       15,  89.90);
