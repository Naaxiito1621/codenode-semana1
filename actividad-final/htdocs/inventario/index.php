<?php
/**
 * Gestor de Inventario — TecnoSoluciones S.A.
 *
 * Funcionalidades:
 *   - Listar elementos del inventario
 *   - Registrar nuevos elementos
 *   - Prevención de inyecciones SQL mediante consultas preparadas (PDO)
 *   - Escape de salida con htmlspecialchars() para prevenir XSS
 */

require_once 'db.php';

$mensaje = '';
$tipo_mensaje = '';

// ─── Procesar formulario de registro ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $cantidad    = (int)($_POST['cantidad'] ?? 0);
    $precio      = (float)($_POST['precio'] ?? 0);

    if ($nombre === '' || $cantidad < 0 || $precio < 0) {
        $mensaje = 'Por favor, completa todos los campos obligatorios con valores válidos.';
        $tipo_mensaje = 'error';
    } else {
        try {
            // Consulta preparada para prevenir inyección SQL
            $stmt = $pdo->prepare(
                "INSERT INTO inventario (nombre, descripcion, cantidad, precio)
                 VALUES (:nombre, :descripcion, :cantidad, :precio)"
            );
            $stmt->execute([
                ':nombre'      => $nombre,
                ':descripcion' => $descripcion,
                ':cantidad'    => $cantidad,
                ':precio'      => $precio,
            ]);
            $mensaje = "Elemento «{$nombre}» registrado correctamente.";
            $tipo_mensaje = 'exito';
        } catch (PDOException $e) {
            $mensaje = 'Error al registrar: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    }
}

// ─── Obtener todos los elementos ───
try {
    $stmt = $pdo->query("SELECT * FROM inventario ORDER BY fecha_registro DESC");
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al consultar: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Inventario — TecnoSoluciones S.A.</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }
        .container { max-width: 960px; margin: 0 auto; padding: 20px; }
        header {
            background: linear-gradient(135deg, #1a237e, #283593);
            color: white;
            padding: 20px 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin-bottom: 0;
        }
        header h1 { font-size: 1.8rem; }
        header p  { opacity: 0.85; font-size: 0.95rem; }

        .card {
            background: white;
            border-radius: 0 0 8px 8px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Mensajes */
        .msg {
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .msg.exito { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #43a047; }
        .msg.error { background: #ffebee; color: #c62828; border-left: 4px solid #e53935; }

        /* Formulario */
        h2 { margin-bottom: 15px; color: #1a237e; }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-grid .full { grid-column: 1 / -1; }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }
        input, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #1a237e;
        }
        textarea { resize: vertical; min-height: 60px; }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #1a237e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .btn:hover { background: #283593; }

        /* Tabla */
        .tabla-section { margin-top: 30px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background: #e8eaf6;
            color: #1a237e;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        tr:hover { background: #f5f5f5; }
        td { font-size: 0.95rem; }
        .empty {
            text-align: center;
            padding: 30px;
            color: #999;
        }
        footer {
            text-align: center;
            padding: 15px;
            color: #888;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestor de Inventario</h1>
            <p>TecnoSoluciones S.A. — Sistema interno de gestión</p>
        </header>

        <div class="card">
            <?php if ($mensaje): ?>
                <div class="msg <?= $tipo_mensaje ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <h2>Registrar nuevo elemento</h2>
            <form method="POST" action="">
                <div class="form-grid">
                    <div>
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required
                               placeholder="Ej: Monitor LED 24&quot;">
                    </div>
                    <div>
                        <label for="cantidad">Cantidad *</label>
                        <input type="number" id="cantidad" name="cantidad" min="0" required
                               placeholder="Ej: 10">
                    </div>
                    <div>
                        <label for="precio">Precio (ARS) *</label>
                        <input type="number" id="precio" name="precio" min="0" step="0.01" required
                               placeholder="Ej: 45999.99">
                    </div>
                    <div>
                        <label for="descripcion">Descripción</label>
                        <input type="text" id="descripcion" name="descripcion"
                               placeholder="Descripción breve del elemento">
                    </div>
                    <div class="full">
                        <button type="submit" class="btn">Registrar elemento</button>
                    </div>
                </div>
            </form>

            <div class="tabla-section">
                <h2>Inventario actual</h2>
                <?php if (count($items) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= (int)$item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                                    <td><?= htmlspecialchars($item['descripcion'] ?? '—') ?></td>
                                    <td><?= (int)$item['cantidad'] ?></td>
                                    <td>$<?= number_format((float)$item['precio'], 2, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($item['fecha_registro']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty">No hay elementos registrados en el inventario.</p>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            &copy; <?= date('Y') ?> TecnoSoluciones S.A. — Todos los derechos reservados
        </footer>
    </div>
</body>
</html>
