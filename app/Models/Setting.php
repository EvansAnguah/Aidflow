<?php
namespace App\Models;

use App\Core\Model;

class Setting extends Model {

    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM settings ORDER BY setting_key ASC");
    }

    public function get($key) {
        return $this->db->fetchColumn("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    }

    public function set($key, $value, $adminId) {
        $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
        $success = $this->db->query($sql, [$value, $key]);
        if ($success) {
            $this->logAction($adminId, 'Update Setting', "Updated setting '$key' to '$value'");
            return true;
        }
        return false;
    }

    /**
     * Get system virtual balances (contributions, disbursements, available balance)
     */
    public function getSystemFinancialSummary() {
        $welfareFundBalance = (float)$this->get('welfare_fund_balance') ?: 0.00;
        
        $totalContributions = (float)$this->db->fetchColumn(
            "SELECT SUM(amount) FROM contributions WHERE status = 'Verified'"
        ) ?: 0.00;
        
        $totalDisbursements = (float)$this->db->fetchColumn(
            "SELECT SUM(amount) FROM disbursements"
        ) ?: 0.00;

        // Virtual available balance is starting fund + contributions - disbursements
        $availableBalance = $welfareFundBalance + $totalContributions - $totalDisbursements;

        return [
            'starting_fund' => $welfareFundBalance,
            'total_contributions' => $totalContributions,
            'total_disbursements' => $totalDisbursements,
            'available_balance' => $availableBalance
        ];
    }
}
