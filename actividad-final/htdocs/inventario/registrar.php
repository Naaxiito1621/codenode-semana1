<?php
// ============================================================
// Página de registro de productos (con Prepared Statements)
// ============================================================
// Esta página:
// 1. Muestra un formulario HTML para registrar un producto nuevo
// 2. Cuando se envía el formulario (POST), recoge los datos,
//    los valida y los inserta en la base de datos
// 3. Usa CONSULTAS PREPARADAS (Prepared Statements) para
//    prevenir inyecciones SQL
//
// Seguridad aplicada:
// - Consultas preparadas con prepare() + bind_param()
// - Validación del lado del servidor (nunca confiar solo en HTML)
// - htmlspecialchars() en toda salida para prevenir XSS
// - trim() para limpiar espacios en blanco
// - intval() y floatval() para forzar tipos numéricos
// ============================================================

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Variables para mostrar mensajes al usuario después de enviar el formulario
$mensaje = "";
$tipo_mensaje = "";  // "exito" o "error" (para aplicar estilos CSS diferentes)

// --- Procesar el formulario solo si se envió por POST ---
// $_SERVER['REQUEST_METHOD'] indica cómo se accedió a la página:
//   - 'GET' = se cargó normalmente (navegación, enlace, URL directa)
//   - 'POST' = se envió un formulario con method="POST"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Recoger y limpiar los datos del formulario ---
    // $_POST['nombre'] contiene lo que el usuario escribió en el campo "nombre"
    // trim() elimina espacios en blanco al inicio y al final
    // ?? '' es el operador "null coalescing": si el campo no existe, usa '' (cadena vacía)
    $nombre      = trim($_POST['nombre']      ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    // intval() convierte a entero: "25" → 25, "abc" → 0, "12.5" → 12
    $cantidad    = intval($_POST['cantidad']   ?? 0);

    // floatval() convierte a decimal: "49.99" → 49.99, "abc" → 0.0
    $precio      = floatval($_POST['precio']   ?? 0);

    // --- Validación del lado del servidor ---
    // IMPORTANTE: La validación HTML (required, min, max) del formulario solo
    // funciona en el navegador. Un atacante podría enviar datos directamente
    // al servidor saltándose la validación HTML. Por eso SIEMPRE validamos
    // también en el servidor.
    if ($nombre === '' || $cantidad < 0 || $precio < 0) {
        $mensaje = "Por favor, completa todos los campos correctamente.";
        $tipo_mensaje = "error";
    } else {
        // --- CONSULTA PREPARADA (Prepared Statement) ---
        // Esta es la técnica principal para prevenir INYECCIONES SQL.
        //
        // En vez de hacer:
        //   $sql = "INSERT INTO productos VALUES ('$nombre', '$descripcion', $cantidad, $precio)";
        //   (INSEGURO — un atacante podría poner SQL malicioso en $nombre)
        //
        // Usamos marcadores de posición (?):

        // Paso 1: PREPARAR — Enviar la estructura de la consulta al servidor MySQL
        // Los ? son marcadores que se reemplazarán por valores seguros
        $stmt = $conexion->prepare(
            "INSERT INTO productos (nombre, descripcion, cantidad, precio) VALUES (?, ?, ?, ?)"
        );

        // Paso 2: VINCULAR — Asociar variables PHP a los marcadores ?
        // El primer argumento "ssid" indica el TIPO de cada parámetro:
        //   s = string (cadena de texto)  → para $nombre
        //   s = string (cadena de texto)  → para $descripcion
        //   i = integer (número entero)   → para $cantidad
        //   d = double (número decimal)   → para $precio
        $stmt->bind_param("ssid", $nombre, $descripcion, $cantidad, $precio);

        // Paso 3: EJECUTAR — Enviar los valores y ejecutar la consulta
        // MySQL trata los valores SIEMPRE como datos, NUNCA como código SQL
        if ($stmt->execute()) {
            $mensaje = "Producto registrado correctamente.";
            $tipo_mensaje = "exito";
        } else {
            // htmlspecialchars() previene XSS también en los mensajes de error
            $mensaje = "Error al registrar: " . htmlspecialchars($stmt->error);
            $tipo_mensaje = "error";
        }

        // Paso 4: CERRAR — Liberar los recursos de la consulta preparada
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
            <!-- Mostrar mensaje de éxito o error después de enviar el formulario -->
            <!-- La clase CSS (exito/error) aplica estilos diferentes (verde/rojo) -->
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de registro -->
        <!-- method="POST": envía los datos en el cuerpo de la petición HTTP (no en la URL) -->
        <!-- action="registrar.php": envía el formulario a esta misma página -->
        <form method="POST" action="registrar.php">
            <div class="campo">
                <label for="nombre">Nombre del producto:</label>
                <!-- required: el navegador no permite enviar si está vacío -->
                <!-- maxlength="100": límite de 100 caracteres (coincide con VARCHAR(100) en la BD) -->
                <input type="text" id="nombre" name="nombre" required
                       maxlength="100" placeholder="Ej: Teclado mecánico">
            </div>

            <div class="campo">
                <label for="descripcion">Descripción:</label>
                <!-- textarea permite texto multilínea -->
                <textarea id="descripcion" name="descripcion" rows="3"
                          maxlength="500" placeholder="Descripción breve del producto"></textarea>
            </div>

            <div class="campo">
                <label for="cantidad">Cantidad en stock:</label>
                <!-- type="number": solo permite números -->
                <!-- min="0": no permite valores negativos -->
                <input type="number" id="cantidad" name="cantidad" required
                       min="0" value="0">
            </div>

            <div class="campo">
                <label for="precio">Precio unitario (EUR):</label>
                <!-- step="0.01": permite decimales con 2 posiciones (céntimos) -->
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
<?php
// Cerrar la conexión a la base de datos
$conexion->close();
?>
