<?php
require_once 'config.php';

// Start session with secure settings
function secure_session_start() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    
    session_start();
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['last_regeneration'])) {
        regenerate_session();
    } else {
        $regeneration_time = 60 * 30; // 30 minutes
        if (time() - $_SESSION['last_regeneration'] >= $regeneration_time) {
            regenerate_session();
        }
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit();
    }
    
    $_SESSION['last_activity'] = time();
}

function regenerate_session() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $old_session_id = session_id();
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// CSRF Protection
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    if (time() - $_SESSION['csrf_token_time'] > CSRF_TIMEOUT) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) ||
        !hash_equals($_SESSION['csrf_token'], $token) ||
        time() - $_SESSION['csrf_token_time'] > CSRF_TIMEOUT) {
        return false;
    }
    return true;
}
