<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\FormProcessor;

$processor = new FormProcessor();
$result = $processor->process($_POST);

if ($result['success']) {
    header('Location: ' . $processor->getRedirectUrl());
    exit;
}

// If validation fails, redirect back (in a real app, show errors)
header('Location: index.html');
exit;
