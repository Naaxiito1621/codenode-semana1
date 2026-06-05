-- ============================================
-- Base de datos: empresa_db
-- Gestor de Inventario — TecnoSoluciones S.A.
-- ============================================

CREATE DATABASE IF NOT EXISTS empresa_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE empresa_db;

-- Tabla principal de inventario
CREATE TABLE IF NOT EXISTS inventario (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100)    NOT NULL,
    descripcion     TEXT,
    cantidad        INT             NOT NULL DEFAULT 0,
    precio          DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    fecha_registro  DATETIME        DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo
INSERT INTO inventario (nombre, descripcion, cantidad, precio) VALUES
('Monitor LED 24"',    'Monitor Full HD IPS de 24 pulgadas',       15, 45999.99),
('Teclado mecánico',   'Teclado mecánico RGB switch blue',         30, 18500.00),
('Mouse inalámbrico',  'Mouse ergonómico 2.4GHz',                  50,  8900.50),
('Notebook 15.6"',     'Notebook i5 8GB RAM 256GB SSD',            10, 289000.00),
('Cable HDMI 2m',      'Cable HDMI 2.0 alta velocidad',          100,  2500.00);
