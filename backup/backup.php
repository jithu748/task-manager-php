<?php
require_once '../includes/config.php';

// Set backup directory
$backup_dir = __DIR__ . '/database_backups/';
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Create backup filename with date
$backup_file = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';

// Backup command
$command = sprintf(
    'mysqldump --host=%s --user=%s --password=%s %s > %s',
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    $backup_file
);

// Execute backup
system($command, $return_var);

// Check if backup was successful
if ($return_var === 0) {
    echo "Backup created successfully: " . basename($backup_file) . "\n";
    
    // Delete backups older than 30 days
    $files = glob($backup_dir . '*.sql');
    foreach ($files as $file) {
        if (filemtime($file) < time() - 30 * 24 * 60 * 60) {
            unlink($file);
        }
    }
} else {
    echo "Backup failed!\n";
}
