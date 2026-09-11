<?php
/**
 * ci-datatables sample application entry point
 */
$uri = $_SERVER['REQUEST_URI'] ?? '';
$script = $_SERVER['SCRIPT_NAME'] ?? '';

// If accessed directly via browser subfolder, redirect to public/
if (php_sapi_name() !== 'cli' && !str_contains($uri, '/public')) {
    $target = rtrim(dirname($script), '/\\') . '/public/';
    header("Location: {$target}");
    exit;
}

require __DIR__ . '/public/index.php';
