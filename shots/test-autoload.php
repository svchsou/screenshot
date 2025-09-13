<?php
// test-autoload.php (place in your project root, e.g., shots/)
declare(strict_types=1);

// Show plain text output with errors visible for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

echo "== Composer Autoload Sanity Check ==\n";
echo "CWD: ".__DIR__."\n\n";

$tried = [];
$autoload = null;

// Common locations relative to this file
$paths = [
    __DIR__ . '/vendor/autoload.php',        // when this file is in shots/
    __DIR__ . '/../vendor/autoload.php',     // when this file is in shots/public/
];

foreach ($paths as $p) {
    $tried[] = $p;
    if (is_file($p)) { $autoload = $p; break; }
}

if (!$autoload) {
    echo "ERROR: Could not find vendor/autoload.php\n";
    echo "Tried:\n - ".implode("\n - ", $tried)."\n";
    exit(1);
}

echo "Including: {$autoload}\n";
require $autoload;

$hasComposer = class_exists('Composer\\Autoload\\ClassLoader');
$hasIlluminate = class_exists('Illuminate\\Support\\Str');

echo "Composer loader: ".($hasComposer ? 'YES' : 'NO')."\n";
echo "Illuminate\\Support\\Str: ".($hasIlluminate ? 'YES' : 'NO')."\n";
if ($hasIlluminate) {
    echo "Sample UUID: ".(string) Illuminate\Support\Str::uuid()."\n";
}

echo "\nPHP ".PHP_VERSION."\n";
$exts = ['pdo_mysql','fileinfo','mbstring','curl','openssl','json','zip','gd'];
foreach ($exts as $ext) {
    echo sprintf("ext %-9s: %s\n", $ext, extension_loaded($ext) ? 'ON' : 'OFF');
}

echo "\nDone.\n";

