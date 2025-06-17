# Task Manager PHP - Project Setup and Structure Guide

## Part 1: Project Setup and Implementation

### Initial Setup
```bash
mkdir task-manager
cd task-manager
mkdir includes css js docs screenshots
```

### Database Setup
1. Create a new MySQL database:
```sql
CREATE DATABASE task_manager;
USE task_manager;
```

2. Create the users table:
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

3. Create the tasks table:
```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Part 2: Project Structure Overview

### 1. Core System Files (`includes/`)
These files form the foundation of the application.

### 1.1 Database Connection (`includes/db.php`)
- Establishes secure database connection
- Contains database configuration
- Implements connection error handling

### 1.2 Configuration (`includes/config.php`)
- Stores application settings
- Contains database credentials
- Defines constants and global configurations

### 1.3 Session Management (`includes/session.php`)
- Handles secure session initialization
- Implements session security measures
- Manages session lifecycle

### 1.4 Logger (`includes/logger.php`)
- Manages application logging
- Records errors, warnings, and info messages
- Maintains activity logs

### 1.5 Email Service (`includes/email_service.php`)
- Handles email notifications
- Manages email templates
- Provides email validation

## 2. Authentication System

### 2.1 Registration System
1. `register.php`
   - New user registration form
   - Input validation
   - Password hashing
   - Account creation logic

### 2.2 Login System
1. `login.php`
   - User authentication form
   - Credential validation
   - Session initialization
   - Login attempt logging

2. `auth.php`
   - Authentication middleware
   - Session validation
   - Access control
   - Security checks

3. `logout.php`
   - Session termination
   - Cleanup procedures
   - Redirect handling

## 3. Task Management System

### 3.1 Task Overview
1. `index.php`
   - Dashboard/home page
   - Task listing
   - Task filtering
   - Status overview

### 3.2 Task Operations
1. `add_task.php`
   - Task creation form
   - Input validation
   - Task insertion logic

2. `edit_task.php`
   - Task modification
   - Update handling
   - Validation checks

3. `delete_task.php`
   - Task removal
   - Confirmation checks
   - Deletion logic

4. `toggle_task.php`
   - Status toggling
   - Quick updates
   - State management

## 4. Styling and UI
- `style.css`
  - Application styling
  - Layout definitions
  - Responsive design
  - Theme settings

## 5. Security Measures
- `.htaccess` files
  - Directory protection
  - Access control
  - Security headers
  - Error handling

## Project Flow

1. **Initial Setup**
   - Configure database (db.php)
   - Set up configuration (config.php)
   - Initialize session handling (session.php)

2. **User Journey**
   - User registers (register.php)
   - User logs in (login.php)
   - Authentication verified (auth.php)

3. **Task Operations**
   - View tasks (index.php)
   - Create tasks (add_task.php)
   - Modify tasks (edit_task.php)
   - Delete tasks (delete_task.php)
   - Toggle completion (toggle_task.php)

4. **Session End**
   - User logs out (logout.php)
   - Session cleanup
   - Redirect to login

## Directory Structure
```
task-manager/
├── includes/
│   ├── db.php                 # Database connection and functions
│   ├── config.php             # Application configuration
│   ├── config.sample.php      # Sample configuration template
│   ├── session.php           # Session management
│   ├── logger.php            # Logging functionality
│   ├── email_service.php     # Email handling
│   ├── password_policy.php   # Password validation rules
│   └── .htaccess            # Directory protection
├── docs/                     # Documentation files
├── logs/                     # Application logs
├── backup/                   # Backup storage
├── js/                      # JavaScript files
├── register.php             # User registration
├── login.php               # User login
├── auth.php                # Authentication handling
├── logout.php              # User logout
├── index.php              # Main dashboard
├── add_task.php           # Create new task
├── edit_task.php          # Modify existing task
├── delete_task.php        # Delete task
├── toggle_task.php        # Toggle task status
├── change_email.php       # Email change functionality
├── change_password.php    # Password change functionality
├── .htaccess             # Root directory protection
└── style.css             # Application styling
```

## Getting Started
1. Configure your database settings in `includes/config.php`
2. Ensure proper permissions on log and upload directories
3. Start with registration page to create a new account
4. Log in and begin managing tasks

## Security Notes
- All user inputs are validated and sanitized
- Passwords are securely hashed
- Sessions are managed securely
- SQL injection prevention implemented
- XSS protection in place
