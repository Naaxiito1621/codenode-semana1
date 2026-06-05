<?php
// ============================================================
// Pagina de registro de productos (con prepared statements)
// ============================================================
require_once 'conexion.php';

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = trim($_POST['nombre']      ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $cantidad    = intval($_POST['cantidad']   ?? 0);
    $precio      = floatval($_POST['precio']   ?? 0);

    // Validacion basica del servidor
    if ($nombre === '' || $cantidad < 0 || $precio < 0) {
        $mensaje = "Por favor, completa todos los campos correctamente.";
        $tipo_mensaje = "error";
    } else {
        // Consulta preparada para prevenir inyeccion SQL
        $stmt = $conexion->prepare(
            "INSERT INTO productos (nombre, descripcion, cantidad, precio) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssid", $nombre, $descripcion, $cantidad, $precio);

        if ($stmt->execute()) {
            $mensaje = "Producto registrado correctamente.";
            $tipo_mensaje = "exito";
        } else {
            $mensaje = "Error al registrar: " . htmlspecialchars($stmt->error);
            $tipo_mensaje = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar producto — TecnoSoluciones S.A.</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Gestor de Inventario</h1>
        <p class="subtitulo">TecnoSoluciones S.A.</p>
    </header>

    <nav>
        <a href="index.php">Listado</a>
        <a href="registrar.php" class="activo">Registrar producto</a>
    </nav>

    <main>
        <h2>Registrar nuevo producto</h2>

        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="registrar.php">
            <div class="campo">
                <label for="nombre">Nombre del producto:</label>
                <input type="text" id="nombre" name="nombre" required
                       maxlength="100" placeholder="Ej: Teclado mecanico">
            </div>

            <div class="campo">
                <label for="descripcion">Descripcion:</label>
                <textarea id="descripcion" name="descripcion" rows="3"
                          maxlength="500" placeholder="Descripcion breve del producto"></textarea>
            </div>

            <div class="campo">
                <label for="cantidad">Cantidad en stock:</label>
                <input type="number" id="cantidad" name="cantidad" required
                       min="0" value="0">
            </div>

            <div class="campo">
                <label for="precio">Precio unitario (EUR):</label>
                <input type="number" id="precio" name="precio" required
                       min="0" step="0.01" value="0.00">
            </div>

            <button type="submit">Registrar producto</button>
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TecnoSoluciones S.A. — Todos los derechos reservados</p>
    </footer>
</body>
</html>
<?php $conexion->close(); ?>
