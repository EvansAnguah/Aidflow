<?php
/**
 * AidFlow - Welfare Management System Configuration Template
 * Copy this file to config.php and fill in your details.
 */

// Define Application Constants
define('APP_NAME', 'AidFlow');
define('APP_VERSION', '1.0.0');

// Base URL definition (adjust for production, e.g. '' if in root)
define('BASE_URL', '/AidFlow');

// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aidflow_db');

// SMTP Email Configuration (PHPMailer settings)
define('SMTP_HOST', 'smtp.mailtrap.io'); // Replace with actual SMTP host
define('SMTP_PORT', 2525);               // Replace with actual SMTP port
define('SMTP_USER', 'your_username');     // Replace with actual SMTP user
define('SMTP_PASS', 'your_password');     // Replace with actual SMTP password
define('SMTP_SECURE', 'tls');             // tls or ssl
define('SMTP_FROM_EMAIL', 'noreply@aidflow.org');
define('SMTP_FROM_NAME', 'AidFlow Support');

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour

// File Upload Settings
define('UPLOAD_DIR', dirname(__DIR__) . '/public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Set timezone
date_default_timezone_set('UTC');
