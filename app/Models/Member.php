<?php
namespace App\Models;

use App\Core\Model;

class Member extends Model {
    
    public function findById($id) {
        return $this->db->fetch(
            "SELECT m.*, u.username, u.email, u.role, u.status as user_status 
             FROM members m 
             JOIN users u ON m.user_id = u.id 
             WHERE m.id = ?", 
            [$id]
        );
    }

    public function findByUserId($userId) {
        return $this->db->fetch(
            "SELECT m.*, u.username, u.email, u.role, u.status as user_status 
             FROM members m 
             JOIN users u ON m.user_id = u.id 
             WHERE m.user_id = ?", 
            [$userId]
        );
    }

    public function findByMemberNumber($num) {
        return $this->db->fetch("SELECT * FROM members WHERE member_number = ?", [$num]);
    }

    /**
     * Create member profile
     */
    public function create($data) {
        $memberNumber = $this->generateMemberNumber();
        $sql = "INSERT INTO members (user_id, member_number, first_name, last_name, phone, address, date_of_birth, join_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['user_id'],
            $memberNumber,
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['address'],
            $data['date_of_birth'],
            $data['join_date'] ?? date('Y-m-d'),
            $data['status'] ?? 'Active'
        ];

        $success = $this->db->query($sql, $params);
        if ($success) {
            $memberId = $this->db->lastInsertId();
            $this->logAction($data['user_id'], 'Create Profile', "Created member profile for $memberNumber");
            return $memberId;
        }
        return false;
    }

    /**
     * Update member profile
     */
    public function update($id, $data, $actionBy) {
        $sql = "UPDATE members SET first_name = ?, last_name = ?, phone = ?, address = ?, date_of_birth = ?, status = ? WHERE id = ?";
        $params = [
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['address'],
            $data['date_of_birth'],
            $data['status'],
            $id
        ];

        $success = $this->db->query($sql, $params);
        if ($success) {
            $this->logAction($actionBy, 'Update Profile', "Updated member profile ID $id");
            return true;
        }
        return false;
    }

    /**
     * Search and Filter members
     */
    public function searchAndFilter($searchQuery = '', $statusFilter = '') {
        $sql = "SELECT m.*, u.email, u.role FROM members m JOIN users u ON m.user_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND m.status = ?";
            $params[] = $statusFilter;
        }

        if (!empty($searchQuery)) {
            $sql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.member_number LIKE ? OR m.phone LIKE ?)";
            $wildcard = '%' . $searchQuery . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $sql .= " ORDER BY m.id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Generates a unique member number (e.g. MEM-2026-0001)
     */
    private function generateMemberNumber() {
        $year = date('Y');
        $prefix = "MEM-" . $year . "-";
        
        // Find maximum number for this year
        $maxNum = $this->db->fetchColumn(
            "SELECT member_number FROM members WHERE member_number LIKE ? ORDER BY id DESC LIMIT 1",
            [$prefix . '%']
        );

        if ($maxNum) {
            $sequence = (int)substr($maxNum, -4);
            $newSequence = $sequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get summary counts for dashboards
     */
    public function getCounts() {
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM members");
        $active = $this->db->fetchColumn("SELECT COUNT(*) FROM members WHERE status = 'Active'");
        $pendingUsers = $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE status = 'Pending'");
        return [
            'total' => $total,
            'active' => $active,
            'pending_approvals' => $pendingUsers
        ];
    }
}
