-- ============================================================
-- Base de datos: empresa_db
-- Gestor de Inventario — TecnoSoluciones S.A.
-- ============================================================

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS empresa_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_spanish_ci;

-- Seleccionar la base de datos
USE empresa_db;

-- Tabla de productos del inventario
CREATE TABLE IF NOT EXISTS productos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100)   NOT NULL,
    descripcion     VARCHAR(500)   DEFAULT '',
    cantidad        INT            NOT NULL DEFAULT 0,
    precio          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    fecha_registro  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos de ejemplo para verificar que todo funciona
INSERT INTO productos (nombre, descripcion, cantidad, precio) VALUES
    ('Teclado mecanico',     'Teclado RGB switches Cherry MX Blue',  25,  49.99),
    ('Monitor 27 pulgadas',  'Monitor IPS 4K 60Hz',                  10, 329.00),
    ('Raton inalambrico',    'Raton ergonomico Bluetooth 5.0',       50,  24.50),
    ('Cable HDMI 2m',        'Cable HDMI 2.1 alta velocidad',       100,   8.99),
    ('Disco SSD 1TB',        'SSD NVMe M.2 lectura 3500MB/s',       15, 89.90);
