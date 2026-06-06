<?php
// ============================================================
// Página principal — Listado de productos del inventario
// ============================================================
// Esta página:
// 1. Se conecta a la base de datos (via conexion.php)
// 2. Ejecuta una consulta SELECT para obtener todos los productos
// 3. Muestra los resultados en una tabla HTML
//
// Seguridad aplicada:
// - htmlspecialchars() en toda salida para prevenir XSS
// - number_format() para formatear precios de forma segura
// ============================================================

// Incluir el archivo de conexión a la base de datos
// require_once garantiza que se incluya una sola vez (evita duplicados)
require_once 'conexion.php';

// --- Consulta SQL para obtener todos los productos ---
// ORDER BY id DESC: muestra los productos más recientes primero
// Esta consulta no tiene parámetros del usuario, así que no necesita
// ser preparada (no hay riesgo de inyección SQL aquí)
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

    <!-- Navegación entre las dos páginas de la app -->
    <nav>
        <a href="index.php" class="activo">Listado</a>
        <a href="registrar.php">Registrar producto</a>
    </nav>

    <main>
        <h2>Productos registrados</h2>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
        <!-- Si hay productos, mostrar la tabla -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio (EUR)</th>
                    <th>Fecha de registro</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                <!-- fetch_assoc() obtiene cada fila como array asociativo -->
                <!-- Ejemplo: $fila['nombre'] = "Teclado mecánico" -->
                <tr>
                    <!-- htmlspecialchars() previene ataques XSS al escapar
                         caracteres HTML especiales (<, >, &, ", ') -->
                    <td><?php echo htmlspecialchars($fila['id']); ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($fila['cantidad']); ?></td>
                    <!-- number_format(valor, decimales, separador_decimal, separador_miles) -->
                    <td><?php echo number_format($fila['precio'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($fila['fecha_registro']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <!-- Si no hay productos, mostrar mensaje informativo -->
            <p class="vacio">No hay productos registrados todavía.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TecnoSoluciones S.A. — Todos los derechos reservados</p>
    </footer>
</body>
</html>
<?php
// Cerrar la conexión a la base de datos para liberar recursos
$conexion->close();
?>
