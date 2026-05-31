<?php
namespace App\Core;

class Session {
    /**
     * Start user session
     */
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
    }

    /**
     * Log out user
     */
    public static function logout() {
        // Unset session variables
        $_SESSION = [];
        
        // Destroy session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user property
     */
    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Require authentication helper
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            $_SESSION['flash_error'] = 'Please log in to access this page.';
            header("Location: " . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Require specific roles
     */
    public static function requireRole($roles) {
        self::requireLogin();
        
        if (is_string($roles)) {
            $roles = [$roles];
        }

        $userRole = $_SESSION['role'] ?? '';
        if (!in_array($userRole, $roles)) {
            $_SESSION['flash_error'] = 'Access Denied: You do not have permission to view this page.';
            header("Location: " . BASE_URL . '/dashboard');
            exit;
        }
    }

    /**
     * Flash Messages (Alerts)
     */
    public static function setFlash($type, $message) {
        $_SESSION['flash_' . $type] = $message;
    }

    public static function getFlash($type) {
        $key = 'flash_' . $type;
        if (isset($_SESSION[$key])) {
            $msg = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $msg;
        }
        return null;
    }

    /**
     * CSRF Protection
     */
    public static function getCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token) {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if (empty($sessionToken) || empty($token)) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }
}
