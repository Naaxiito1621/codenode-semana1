<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Formulario de contacto</h1>
    <form action="form.php" method="post">
       <?php
           session_start();
           $csrf_token = bin2hex(random_bytes(32));
           $_SESSION['csrf_token'] = $csrf_token;
       ?>
       <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

       <div>
         <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" required><br><br>
       </div>
       <div>
        <label for="Apellidos">Apellidos:</label>
       <input type="text" id="Apellidos" name="Apellidos" required><br><br>
      </div>
        <div>
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" required><br><br>
        </div>
        <div>
            <label for="telefono">Numero de teléfono:</label>
            <input type="tel" id="telefono" name="telefono" pattern="[0-9]{6,15}" placeholder="Ej: 664256891" required><br><br>
        </div>
        <div>
            <label for="Dni">Dni:</label>
            <input type="text" id="Dni" name="Dni" maxlength="9" pattern="[0-9]{8}[A-Za-z]" placeholder="Ej: 12345678K" required><br><br>
        </div>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
