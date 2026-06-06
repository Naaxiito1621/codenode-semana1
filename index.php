<?php
require_once __DIR__ . '/includes/form_helpers.php';
$pageTitle = 'Formulario';
require __DIR__ . '/includes/header.php';
?>
    <h1>Formulario de contacto</h1>
    <form action="form.php" method="post">
        <?php
        echo render_field('name', 'name', 'Nombre:');
        echo render_field('Apellidos', 'Apellidos', 'Apellidos:');
        echo render_field('email', 'email', 'Correo electrónico:', 'email');
        echo render_field('telefono', 'telefono', 'Numero de teléfono:', 'tel', ['placeholder' => 'Ej: 664256891']);
        echo render_field('Dni', 'Dni', 'Dni:', 'text', ['maxlength' => '9', 'placeholder' => 'Ej: 12345678K']);
        echo render_nav_button('respuesta.html', 'Enviar');
        ?>
    </form>
<?php require __DIR__ . '/includes/footer.php'; ?>
