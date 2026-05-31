<?php
namespace App\Models;

use App\Core\Model;

class Disbursement extends Model {

    public function findById($id) {
        return $this->db->fetch(
            "SELECT d.*, wr.title as request_title, wr.requested_amount, m.first_name, m.last_name, m.member_number, wc.name as category_name, u.username as treasurer_name
             FROM disbursements d
             JOIN welfare_requests wr ON d.request_id = wr.id
             JOIN members m ON wr.member_id = m.id
             JOIN welfare_categories wc ON wr.category_id = wc.id
             JOIN users u ON d.disbursed_by = u.id
             WHERE d.id = ?",
            [$id]
        );
    }

    /**
     * Record disbursement and update system virtual fund balance
     */
    public function create($data) {
        $this->db->beginTransaction();
        try {
            // 1. Record Disbursement
            $sql = "INSERT INTO disbursements (request_id, amount, payment_method, reference_number, disbursed_by, notes, receipt_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['request_id'],
                $data['amount'],
                $data['payment_method'],
                $data['reference_number'],
                $data['disbursed_by'],
                $data['notes'] ?? null,
                $data['receipt_path'] ?? null
            ];

            $this->db->query($sql, $params);
            $disbursementId = $this->db->lastInsertId();

            // 2. Mark request as Completed
            $sql2 = "UPDATE welfare_requests SET status = 'Completed' WHERE id = ?";
            $this->db->query($sql2, [$data['request_id']]);

            // Add complete approval action
            $sql3 = "INSERT INTO approvals (request_id, user_id, action, comments) VALUES (?, ?, 'Complete', 'Funds disbursed successfully')";
            $this->db->query($sql3, [$data['request_id'], $data['disbursed_by']]);

            // 3. Deduct from welfare fund balance in settings
            $currentBalance = (float)$this->db->fetchColumn("SELECT setting_value FROM settings WHERE setting_key = 'welfare_fund_balance'") ?: 0.00;
            $newBalance = $currentBalance - (float)$data['amount'];
            
            $sql4 = "UPDATE settings SET setting_value = ? WHERE setting_key = 'welfare_fund_balance'";
            $this->db->query($sql4, [$newBalance]);

            // 4. Log Action
            $this->logAction($data['disbursed_by'], 'Record Disbursement', "Disbursed {$data['amount']} for request ID {$data['request_id']}");

            $this->db->commit();
            return $disbursementId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get all disbursements
     */
    public function getAll($search = '') {
        $sql = "SELECT d.*, wr.title as request_title, m.first_name, m.last_name, m.member_number, u.username as treasurer_name
                FROM disbursements d
                JOIN welfare_requests wr ON d.request_id = wr.id
                JOIN members m ON wr.member_id = m.id
                JOIN users u ON d.disbursed_by = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.member_number LIKE ? OR d.reference_number LIKE ?)";
            $wildcard = '%' . $search . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $sql .= " ORDER BY d.disbursed_at DESC";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get sum of all disbursements
     */
    public function getTotalDisbursements() {
        return (float)$this->db->fetchColumn("SELECT SUM(amount) FROM disbursements") ?: 0.00;
    }
}
