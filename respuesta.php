<?php
require_once __DIR__ . '/includes/form_helpers.php';
$pageTitle = 'Formulario Enviado';
require __DIR__ . '/includes/header.php';
?>
    <p>Muchas gracias por rellenar el formulario</p>
    <?php echo render_nav_button('index.php', 'Volver'); ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
