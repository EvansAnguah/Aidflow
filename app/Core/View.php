<?php
namespace App\Core;

class View {
    /**
     * Escape output HTML
     */
    public static function escape($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format currency value
     */
    public static function formatCurrency($amount, $currency = '$') {
        return $currency . number_format((float)$amount, 2);
    }

    /**
     * Format date to standard format
     */
    public static function formatDate($dateString, $format = 'M d, Y') {
        if (empty($dateString)) return 'N/A';
        return date($format, strtotime($dateString));
    }

    /**
     * Format datetime to standard format
     */
    public static function formatDateTime($dateTimeString, $format = 'M d, Y H:i A') {
        if (empty($dateTimeString)) return 'N/A';
        return date($format, strtotime($dateTimeString));
    }

    /**
     * Generate HTML input for CSRF Token
     */
    public static function csrfField() {
        $token = Session::getCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Returns class "active" if current URI contains search string
     */
    public static function activeClass($search) {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($uri, $search) !== false ? 'active' : '';
    }
}
