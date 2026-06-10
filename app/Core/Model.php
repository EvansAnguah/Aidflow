<?php
namespace App\Core;

class Model {
    public $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Start audit logging inside DB transactions or queries
     */
    protected function logAction($userId, $action, $details) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $sql = "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
        return $this->db->query($sql, [$userId, $action, $details, $ip]);
    }
}
