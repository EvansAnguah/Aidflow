<?php
namespace App\Models;

use App\Core\Model;

class AuditLog extends Model {

    public function record($userId, $action, $details) {
        return $this->logAction($userId, $action, $details);
    }

    /**
     * Get system logs with user information
     */
    public function getAll($limit = 100) {
        return $this->db->fetchAll(
            "SELECT al.*, u.username, u.role 
             FROM audit_logs al 
             LEFT JOIN users u ON al.user_id = u.id 
             ORDER BY al.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }
}
