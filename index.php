<?php
/**
 * AidFlow - Front Controller
 */

// Show errors for development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start Session with security settings
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 3600,
        'cookie_secure' => false, // Set to true if using HTTPS
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Load configurations
require_once __DIR__ . '/config/config.php';

// Composer Autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Custom Autoloader for MVC Structure (App Namespace)
spl_autoload_register(function ($class) {
    // Convert namespace backslashes to directory separators
    $classPath = str_replace('\\', '/', $class);
    $file = __DIR__ . '/' . $classPath . '.php';
    
    // Fallback mapping if 'app/' is lowercase in folder but capitalized in namespaces
    if (!file_exists($file)) {
        // e.g. replace App/Controllers/HomeController -> app/Controllers/HomeController
        if (strpos($classPath, 'App/') === 0) {
            $classPath = 'app/' . substr($classPath, 4);
            $file = __DIR__ . '/' . $classPath . '.php';
        }
    }

    if (file_exists($file)) {
        require_once $file;
    }
});

// Run Core App Router
use App\Core\App;

try {
    $app = new App();
} catch (\Exception $e) {
    // General exception rendering
    echo "<h1>AidFlow System Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
