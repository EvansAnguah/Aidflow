<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Database;
use App\Models\Setting;
use App\Models\AuditLog;

class SettingsController extends Controller {
    private $settingModel;
    private $auditModel;

    public function __construct() {
        $this->settingModel = new Setting();
        $this->auditModel = new AuditLog();
    }

    /**
     * Settings Panel View (Admin Only)
     */
    public function index() {
        Session::requireRole('Admin');
        
        $settings = $this->settingModel->getAll();
        $logs = $this->auditModel->getAll(50);

        $this->view('settings/index', [
            'title' => 'System Settings',
            'settings' => $settings,
            'audit_logs' => $logs
        ]);
    }

    /**
     * Save System configurations (Admin Only)
     */
    public function save() {
        Session::requireRole('Admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            $adminId = Session::get('user_id');
            $errors = 0;

            foreach ($data as $key => $value) {
                // Ignore CSRF token field
                if ($key === 'csrf_token') continue;
                
                if (!$this->settingModel->set($key, $value, $adminId)) {
                    $errors++;
                }
            }

            if ($errors === 0) {
                Session::setFlash('success', 'System configurations updated successfully.');
            } else {
                Session::setFlash('error', 'Failed to update some configurations.');
            }

            $this->redirect('settings');
        }
    }

    /**
     * System Backup Action (Generates SQL dump output in text download)
     */
    public function backup() {
        Session::requireRole('Admin');

        $db = Database::getInstance()->getConnection();
        $tables = [];
        $result = $db->query("SHOW TABLES");
        while ($row = $result->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sqlDump = "-- AidFlow Database Backup\n";
        $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Drop Table Syntax
            $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
            
            // Create Table Syntax
            $createTableStmt = $db->query("SHOW CREATE TABLE `$table`")->fetch(\PDO::FETCH_ASSOC);
            $sqlDump .= $createTableStmt['Create Table'] . ";\n\n";

            // Insert Row syntax
            $rows = $db->query("SELECT * FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $sqlDump .= "INSERT INTO `$table` VALUES \n";
                $insertRows = [];
                foreach ($rows as $row) {
                    $values = array_map(function($val) use ($db) {
                        if ($val === null) return 'NULL';
                        return $db->quote($val);
                    }, $row);
                    $insertRows[] = "(" . implode(', ', $values) . ")";
                }
                $sqlDump .= implode(",\n", $insertRows) . ";\n\n";
            }
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Download headers
        $fileName = 'aidflow_backup_' . date('Y-m-d_His') . '.sql';
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($sqlDump));
        
        // Log backup action
        $this->auditModel->record(Session::get('user_id'), 'Database Backup', 'System database backup downloaded.');

        echo $sqlDump;
        exit;
    }

    /**
     * System Restore Action (Executes SQL file uploads)
     */
    public function restore() {
        Session::requireRole('Admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();

            if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
                Session::setFlash('error', 'Please upload a valid backup SQL file.');
                $this->redirect('settings');
            }

            $file = $_FILES['backup_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($ext !== 'sql') {
                Session::setFlash('error', 'Restorations only support .sql backup files.');
                $this->redirect('settings');
            }

            $sqlContent = file_get_contents($file['tmp_name']);
            $db = Database::getInstance()->getConnection();

            try {
                // Disable foreign keys during restores
                $db->exec("SET FOREIGN_KEY_CHECKS=0");
                
                // Execute multi-statement SQL
                $db->exec($sqlContent);
                
                $db->exec("SET FOREIGN_KEY_CHECKS=1");

                $this->auditModel->record(Session::get('user_id'), 'Database Restore', 'Database successfully restored from backup.');
                Session::setFlash('success', 'Database restored successfully.');
            } catch (\PDOException $e) {
                $db->exec("SET FOREIGN_KEY_CHECKS=1");
                Session::setFlash('error', 'Database restore failed: ' . $e->getMessage());
            }

            $this->redirect('settings');
        }
    }
}
