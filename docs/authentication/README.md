# Authentication System Documentation

## Overview
The authentication system provides secure user registration, login, and session management.

## Files Structure
```
authentication/
├── login.php           # User login
├── register.php        # New user registration
├── logout.php         # Session termination
├── auth.php           # Authentication helper
└── includes/
    ├── session.php    # Session management
    └── password_policy.php  # Password rules
```

## 1. User Registration (register.php)
📁 Location: [`register.php`](../../register.php)

### Features
- Email validation
- Password strength verification
- Duplicate user prevention
- Secure password hashing

### Implementation
```php
// Registration form processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    
    // Validate password strength
    if (!validatePassword($password)) {
        $error = "Password does not meet requirements";
    }
    
    // Check for existing user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Email already registered";
    }
    
    // Create new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);
}
```

## 2. Login System (login.php)
📁 Location: [`login.php`](../../login.php)

### Features
- Secure authentication
- Brute force protection
- Remember me functionality
- Failed attempt tracking

### Implementation
```php
// Login process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Get user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        
        // Set remember me cookie if requested
        if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + 30*24*60*60);
            
            // Store token in database
            $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
        }
        
        header("Location: index.php");
    }
}
```

## 3. Session Management (session.php)
📁 Location: [`includes/session.php`](../../includes/session.php)

### Features
- Secure session handling
- CSRF protection
- Session hijacking prevention
- Automatic session renewal

### Implementation
```php
// Start secure session
session_start();
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Regenerate session ID periodically
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 3600) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
```

## 4. Password Policy (password_policy.php)
📁 Location: [`includes/password_policy.php`](../../includes/password_policy.php)

### Requirements
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character

### Implementation
```php
function validatePassword($password) {
    // Length check
    if (strlen($password) < 8) {
        return false;
    }
    
    // Character type checks
    if (!preg_match('/[A-Z]/', $password)) return false;  // Uppercase
    if (!preg_match('/[a-z]/', $password)) return false;  // Lowercase
    if (!preg_match('/[0-9]/', $password)) return false;  // Numbers
    if (!preg_match('/[^A-Za-z0-9]/', $password)) return false;  // Special chars
    
    return true;
}
```

## 5. Authentication Helper (auth.php)
📁 Location: [`auth.php`](../../auth.php)

### Features
- Session verification
- Access control
- User role management
- Authentication utilities

### Implementation
```php
// Verify authenticated user
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Get current user
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Check user permissions
function hasPermission($permission) {
    $user = getCurrentUser();
    return in_array($permission, explode(',', $user['permissions']));
}
```

## Security Considerations

### Session Security
1. Cookie Security
```php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
```

2. Session Validation
```php
function validateSession() {
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
    
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_destroy();
        return false;
    }
    
    return true;
}
```

### Password Security
1. Password Hashing
```php
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}
```

2. Password Verification
```php
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
```

## Error Handling

### Login Errors
```php
function handleLoginError($error) {
    Logger::log("Login failed: $error", "WARNING");
    return "Invalid email or password";
}
```

### Registration Errors
```php
function handleRegistrationError($error) {
    Logger::log("Registration failed: $error", "WARNING");
    return "Registration failed: $error";
}
```
