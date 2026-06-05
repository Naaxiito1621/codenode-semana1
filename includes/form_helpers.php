<?php

/**
 * Render a form field group (label + input wrapped in a div).
 */
function render_field(string $id, string $name, string $label, string $type = 'text', array $attrs = []): string
{
    $attrStr = '';
    foreach ($attrs as $key => $value) {
        $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    return '<div>'
        . '<label for="' . htmlspecialchars($id) . '">' . htmlspecialchars($label) . '</label>'
        . '<input type="' . htmlspecialchars($type) . '" id="' . htmlspecialchars($id)
        . '" name="' . htmlspecialchars($name) . '" required' . $attrStr . '>'
        . '</div>';
}

/**
 * Render a navigation button linking to another page.
 */
function render_nav_button(string $href, string $text): string
{
    return '<a class="nav-button" href="' . htmlspecialchars($href) . '">'
        . htmlspecialchars($text) . '</a>';
}

/**
 * Read a POST field, sanitize it, and return a formatted label string.
 */
function format_post_field(string $field, string $label): string
{
    $value = isset($_POST[$field]) ? htmlspecialchars(trim($_POST[$field])) : '';
    return $label . ': ' . $value;
}
