<?php
// Required PHP version
$required_version = '7.4.0';

// Check PHP version
if (version_compare(PHP_VERSION, $required_version, '<')) {
    die("Error: This application requires PHP $required_version or higher. Current version: " . PHP_VERSION);
}

// Check required PHP extensions
$required_extensions = [
    'pdo',
    'pdo_mysql',
    'session',
    'hash',
    'json',
    'mbstring'
];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        die("Error: Required PHP extension '$ext' is not loaded.");
    }
}

// Check PHP configuration
$recommended_settings = [
    'display_errors' => 'Off',
    'log_errors' => 'On',
    'error_reporting' => E_ALL,
    'max_execution_time' => 30,
    'post_max_size' => '8M',
    'upload_max_filesize' => '2M',
    'memory_limit' => '128M',
    'session.cookie_httponly' => 1,
    'session.use_only_cookies' => 1
];

$issues = [];
foreach ($recommended_settings as $key => $recommended) {
    $current = ini_get($key);
    if ($current != $recommended) {
        $issues[] = "PHP setting '$key' is set to '$current', recommended: '$recommended'";
    }
}

if (!empty($issues)) {
    echo "PHP Configuration Warnings:\n";
    foreach ($issues as $issue) {
        echo "- $issue\n";
    }
}
