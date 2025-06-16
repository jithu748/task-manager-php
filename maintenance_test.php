<?php
echo "=== Task Manager Maintenance Test ===\n\n";

// 1. Test Database Connection
echo "1. Testing Database Connection:\n";
try {
    require_once 'includes/db.php';
    echo "✅ Database connection successful!\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";
}

// 2. Test Password Policy
echo "2. Testing Password Policy:\n";
require_once 'includes/password_policy.php';
$test_passwords = [
    'weak' => 'abc123',
    'medium' => 'Password123',
    'strong' => 'Password123!@#'
];

foreach ($test_passwords as $strength => $password) {
    $errors = PasswordPolicy::validatePassword($password);
    echo "Testing $strength password: " . 
         (empty($errors) ? "✅ Valid" : "❌ Invalid: " . implode(", ", $errors)) . "\n";
}
echo "\n";

// 3. Test Logger
echo "3. Testing Logger:\n";
require_once 'includes/logger.php';
try {
    Logger::init();
    Logger::info("Test log entry");
    echo "✅ Logger working - Check logs/app.log\n\n";
} catch (Exception $e) {
    echo "❌ Logger failed: " . $e->getMessage() . "\n\n";
}

// 4. Test Backup Directory
echo "4. Testing Backup Directory:\n";
$backup_dir = __DIR__ . '/database_backups';
if (!file_exists($backup_dir)) {
    if (mkdir($backup_dir, 0755, true)) {
        echo "✅ Backup directory created\n";
    } else {
        echo "❌ Could not create backup directory\n";
    }
} else {
    echo "✅ Backup directory exists\n";
}
echo "\n";

// 5. Test Browser Compatibility Script
echo "5. Testing Browser Compatibility Script:\n";
if (file_exists(__DIR__ . '/js/compatibility.js')) {
    echo "✅ Browser compatibility script found\n";
} else {
    echo "❌ Browser compatibility script missing\n";
}
echo "\n";

// 6. Test Version Requirements
echo "6. Testing PHP Version Requirements:\n";
$required_version = '7.4.0';
if (version_compare(PHP_VERSION, $required_version, '>=')) {
    echo "✅ PHP version OK: " . PHP_VERSION . "\n";
} else {
    echo "❌ PHP version too old. Current: " . PHP_VERSION . ", Required: $required_version\n";
}

$required_extensions = ['pdo', 'pdo_mysql', 'session', 'hash', 'json', 'mbstring'];
foreach ($required_extensions as $ext) {
    echo extension_loaded($ext) 
        ? "✅ Extension '$ext' loaded\n" 
        : "❌ Extension '$ext' missing\n";
}
echo "\n";

echo "=== Test Complete ===\n";
