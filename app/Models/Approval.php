<?php
namespace App\Models;

use App\Core\Model;

class Approval extends Model {
    
    public function getByRequestId($requestId) {
        return $this->db->fetchAll(
            "SELECT a.*, u.username, u.role 
             FROM approvals a 
             JOIN users u ON a.user_id = u.id 
             WHERE a.request_id = ? 
             ORDER BY a.created_at DESC",
            [$requestId]
        );
    }

    public function record($requestId, $userId, $action, $comments) {
        $sql = "INSERT INTO approvals (request_id, user_id, action, comments) VALUES (?, ?, ?, ?)";
        return $this->db->query($sql, [$requestId, $userId, $action, $comments]);
    }
}
