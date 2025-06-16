<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'task_manager');
define('DB_USER', 'root');
define('DB_PASS', '');

// Security Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 minutes
define('CSRF_TIMEOUT', 3600); // 1 hour

// Application Configuration
define('APP_NAME', 'Task Manager');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', false);

// Time zone
date_default_timezone_set('UTC');
