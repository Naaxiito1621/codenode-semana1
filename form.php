<?php
require_once __DIR__ . '/includes/form_helpers.php';

$fields = [
    ['field' => 'name',      'label' => 'Este mensaje fue enviado por'],
    ['field' => 'Apellidos', 'label' => 'Sus Apellidos'],
    ['field' => 'email',     'label' => 'Su correo es'],
    ['field' => 'telefono',  'label' => 'Numero de telefono'],
    ['field' => 'Dni',       'label' => 'Dni'],
];

$formatted = [];
foreach ($fields as $entry) {
    $formatted[] = format_post_field($entry['field'], $entry['label']);
}

header('Location: respuesta.php');
exit;
?>
