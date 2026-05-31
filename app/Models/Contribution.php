<?php
namespace App\Models;

use App\Core\Model;

class Contribution extends Model {

    public function findById($id) {
        return $this->db->fetch(
            "SELECT c.*, m.first_name, m.last_name, m.member_number, m.phone, u.username as recorder_name 
             FROM contributions c
             JOIN members m ON c.member_id = m.id
             JOIN users u ON c.recorded_by = u.id
             WHERE c.id = ?",
            [$id]
        );
    }

    /**
     * Record contribution payment
     */
    public function create($data) {
        $sql = "INSERT INTO contributions (member_id, amount, contribution_month, payment_date, payment_method, reference_number, recorded_by, receipt_path, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['member_id'],
            $data['amount'],
            $data['contribution_month'], // Expects YYYY-MM-01
            $data['payment_date'] ?? date('Y-m-d H:i:s'),
            $data['payment_method'],
            $data['reference_number'],
            $data['recorded_by'],
            $data['receipt_path'] ?? null,
            $data['status'] ?? 'Verified'
        ];

        $success = $this->db->query($sql, $params);
        if ($success) {
            $insertId = $this->db->lastInsertId();
            $this->logAction($data['recorded_by'], 'Record Contribution', "Recorded contribution of {$data['amount']} for member ID {$data['member_id']}");
            return $insertId;
        }
        return false;
    }

    /**
     * Get contribution history for a member
     */
    public function getByMemberId($memberId) {
        return $this->db->fetchAll(
            "SELECT c.*, u.username as recorder_name 
             FROM contributions c 
             JOIN users u ON c.recorded_by = u.id 
             WHERE c.member_id = ? 
             ORDER BY c.contribution_month DESC", 
            [$memberId]
        );
    }

    /**
     * Get all contributions with search filters
     */
    public function getAll($search = '', $month = '') {
        $sql = "SELECT c.*, m.first_name, m.last_name, m.member_number 
                FROM contributions c 
                JOIN members m ON c.member_id = m.id 
                WHERE 1=1";
        $params = [];

        if (!empty($month)) {
            $sql .= " AND c.contribution_month = ?";
            $params[] = $month . '-01'; // Ensure standard format
        }

        if (!empty($search)) {
            $sql .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.member_number LIKE ? OR c.reference_number LIKE ?)";
            $wildcard = '%' . $search . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $sql .= " ORDER BY c.payment_date DESC";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Calculate outstanding contributions for a member
     * Computes months from join date to current month, subtracting paid months.
     */
    public function getOutstandingContributions($member) {
        $joinDate = new \DateTime($member['join_date']);
        $currentDate = new \DateTime();
        
        // Let's cap the start date to not go back infinitely (e.g. max 2 years back for practicality, or from join date)
        $interval = new \DateInterval('P1M');
        $realCurrent = new \DateTime($currentDate->format('Y-m-d'));
        $realCurrent->modify('first day of this month');

        $tempDate = new \DateTime($joinDate->format('Y-m-d'));
        $tempDate->modify('first day of this month');

        $expectedMonths = [];
        while ($tempDate <= $realCurrent) {
            $expectedMonths[] = $tempDate->format('Y-m-01');
            $tempDate->add($interval);
        }

        // Fetch paid months
        $paidRows = $this->db->fetchAll(
            "SELECT contribution_month FROM contributions WHERE member_id = ? AND status = 'Verified'",
            [$member['id']]
        );
        
        $paidMonths = array_column($paidRows, 'contribution_month');

        // Diff the months
        $outstandingMonths = array_diff($expectedMonths, $paidMonths);
        
        // Fetch monthly fee
        $monthlyFee = (float)$this->db->fetchColumn("SELECT setting_value FROM settings WHERE setting_key = 'monthly_contribution_fee'") ?: 50.00;

        $result = [];
        $totalOutstandingAmount = 0.00;
        foreach ($outstandingMonths as $m) {
            $result[] = [
                'month' => $m,
                'amount' => $monthlyFee
            ];
            $totalOutstandingAmount += $monthlyFee;
        }

        return [
            'list' => $result,
            'total_amount' => $totalOutstandingAmount,
            'monthly_fee' => $monthlyFee
        ];
    }

    /**
     * Gets summary contribution amounts
     */
    public function getTotalContributions() {
        return (float)$this->db->fetchColumn("SELECT SUM(amount) FROM contributions WHERE status = 'Verified'") ?: 0.00;
    }

    /**
     * Gets contributions formatted for Chart.js (monthly totals for last 6 months)
     */
    public function getMonthlyStatistics() {
        $sql = "SELECT DATE_FORMAT(payment_date, '%b %Y') as month_label, SUM(amount) as total 
                FROM contributions 
                WHERE status = 'Verified' AND payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
                GROUP BY DATE_FORMAT(payment_date, '%Y-%m') 
                ORDER BY payment_date ASC";
        return $this->db->fetchAll($sql);
    }
}
