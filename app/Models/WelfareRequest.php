<?php
namespace App\Models;

use App\Core\Model;

class WelfareRequest extends Model {

    public function findById($id) {
        return $this->db->fetch(
            "SELECT wr.*, m.first_name, m.last_name, m.member_number, m.phone, m.address, u.email as member_email, wc.name as category_name, wc.max_amount as category_max
             FROM welfare_requests wr
             JOIN members m ON wr.member_id = m.id
             JOIN users u ON m.user_id = u.id
             JOIN welfare_categories wc ON wr.category_id = wc.id
             WHERE wr.id = ?",
            [$id]
        );
    }

    /**
     * Submit a welfare request
     */
    public function create($data) {
        $sql = "INSERT INTO welfare_requests (member_id, category_id, title, description, requested_amount, supporting_document, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['member_id'],
            $data['category_id'],
            $data['title'],
            $data['description'],
            $data['requested_amount'],
            $data['supporting_document'] ?? null,
            $data['status'] ?? 'Pending'
        ];

        $success = $this->db->query($sql, $params);
        if ($success) {
            $insertId = $this->db->lastInsertId();
            
            // Log initial request event in approvals
            $member = $this->db->fetch("SELECT user_id FROM members WHERE id = ?", [$data['member_id']]);
            $this->logAction($member['user_id'], 'Submit Welfare Request', "Submitted welfare request ID $insertId for {$data['title']}");
            
            return $insertId;
        }
        return false;
    }

    /**
     * Get requests for a specific member
     */
    public function getByMemberId($memberId) {
        return $this->db->fetchAll(
            "SELECT wr.*, wc.name as category_name 
             FROM welfare_requests wr 
             JOIN welfare_categories wc ON wr.category_id = wc.id 
             WHERE wr.member_id = ? 
             ORDER BY wr.created_at DESC",
            [$memberId]
        );
    }

    /**
     * Get all requests with filter options
     */
    public function getAll($status = '', $category = '', $search = '') {
        $sql = "SELECT wr.*, m.first_name, m.last_name, m.member_number, wc.name as category_name 
                FROM welfare_requests wr 
                JOIN members m ON wr.member_id = m.id 
                JOIN welfare_categories wc ON wr.category_id = wc.id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($status)) {
            $sql .= " AND wr.status = ?";
            $params[] = $status;
        }

        if (!empty($category)) {
            $sql .= " AND wr.category_id = ?";
            $params[] = (int)$category;
        }

        if (!empty($search)) {
            $sql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.member_number LIKE ? OR wr.title LIKE ?)";
            $wildcard = '%' . $search . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $sql .= " ORDER BY wr.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Update request status & register approval history record
     */
    public function updateStatus($requestId, $status, $userId, $action, $comments = '') {
        $this->db->beginTransaction();
        try {
            // 1. Update request status
            $sql = "UPDATE welfare_requests SET status = ? WHERE id = ?";
            $this->db->query($sql, [$status, $requestId]);

            // 2. Insert approval log
            $sql2 = "INSERT INTO approvals (request_id, user_id, action, comments) VALUES (?, ?, ?, ?)";
            $this->db->query($sql2, [$requestId, $userId, $action, $comments]);

            // 3. System audit log
            $this->logAction($userId, 'Welfare Workflow Action', "Request ID $requestId status updated to $status (Action: $action)");

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get approval history list for a request
     */
    public function getApprovalHistory($requestId) {
        return $this->db->fetchAll(
            "SELECT a.*, u.username, u.role 
             FROM approvals a 
             JOIN users u ON a.user_id = u.id 
             WHERE a.request_id = ? 
             ORDER BY a.created_at ASC",
            [$requestId]
        );
    }

    /**
     * Count summary by status
     */
    public function getStatusCounts() {
        return [
            'pending' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM welfare_requests WHERE status = 'Pending'"),
            'review' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM welfare_requests WHERE status = 'Under Review'"),
            'approved' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM welfare_requests WHERE status = 'Approved'"),
            'rejected' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM welfare_requests WHERE status = 'Rejected'"),
            'completed' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM welfare_requests WHERE status = 'Completed'")
        ];
    }
}
