<?php
// ============================================================
// Pagina principal — Listado de productos del inventario
// ============================================================
require_once 'conexion.php';

// Consulta preparada para obtener todos los productos
$sql = "SELECT id, nombre, descripcion, cantidad, precio, fecha_registro FROM productos ORDER BY id DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Inventario — TecnoSoluciones S.A.</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Gestor de Inventario</h1>
        <p class="subtitulo">TecnoSoluciones S.A.</p>
    </header>

    <nav>
        <a href="index.php" class="activo">Listado</a>
        <a href="registrar.php">Registrar producto</a>
    </nav>

    <main>
        <h2>Productos registrados</h2>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Cantidad</th>
                    <th>Precio (EUR)</th>
                    <th>Fecha de registro</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['id']); ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($fila['cantidad']); ?></td>
                    <td><?php echo number_format($fila['precio'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($fila['fecha_registro']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="vacio">No hay productos registrados todavia.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TecnoSoluciones S.A. — Todos los derechos reservados</p>
    </footer>
</body>
</html>
<?php $conexion->close(); ?>
