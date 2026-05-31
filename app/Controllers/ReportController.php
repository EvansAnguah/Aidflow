<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Member;
use App\Models\Contribution;
use App\Models\WelfareRequest;
use App\Models\Disbursement;
use App\Models\Setting;

class ReportController extends Controller {
    private $memberModel;
    private $contributionModel;
    private $requestModel;
    private $disbursementModel;
    private $settingModel;

    public function __construct() {
        $this->memberModel = new Member();
        $this->contributionModel = new Contribution();
        $this->requestModel = new WelfareRequest();
        $this->disbursementModel = new Disbursement();
        $this->settingModel = new Setting();
    }

    /**
     * Reports Panel Page
     */
    public function index() {
        Session::requireRole(['Admin', 'Treasurer', 'Welfare Officer']);
        
        $this->view('reports/index', [
            'title' => 'Reports & Analytics'
        ]);
    }

    /**
     * Generate HTML view or CSV export for reports
     */
    public function generate() {
        Session::requireRole(['Admin', 'Treasurer', 'Welfare Officer']);

        $type = $_GET['type'] ?? ''; // 'members', 'contributions', 'requests', 'financial'
        $format = $_GET['format'] ?? 'html'; // 'html' (print-friendly) or 'excel' (csv export)
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';

        $data = [];
        $title = 'Report';

        switch ($type) {
            case 'members':
                $title = 'Member Directory Report';
                $data = $this->memberModel->db->fetchAll(
                    "SELECT m.*, u.email, u.role FROM members m JOIN users u ON m.user_id = u.id ORDER BY m.member_number ASC"
                );
                break;

            case 'contributions':
                $title = 'Contribution History Report';
                $sql = "SELECT c.*, m.first_name, m.last_name, m.member_number FROM contributions c JOIN members m ON c.member_id = m.id WHERE 1=1";
                $params = [];
                if (!empty($startDate)) {
                    $sql .= " AND DATE(c.payment_date) >= ?";
                    $params[] = $startDate;
                }
                if (!empty($endDate)) {
                    $sql .= " AND DATE(c.payment_date) <= ?";
                    $params[] = $endDate;
                }
                $sql .= " ORDER BY c.payment_date DESC";
                $data = $this->contributionModel->db->fetchAll($sql, $params);
                break;

            case 'requests':
                $title = 'Welfare Request Workflow Report';
                $sql = "SELECT wr.*, m.first_name, m.last_name, m.member_number, wc.name as category_name 
                        FROM welfare_requests wr 
                        JOIN members m ON wr.member_id = m.id 
                        JOIN welfare_categories wc ON wr.category_id = wc.id 
                        WHERE 1=1";
                $params = [];
                if (!empty($startDate)) {
                    $sql .= " AND DATE(wr.created_at) >= ?";
                    $params[] = $startDate;
                }
                if (!empty($endDate)) {
                    $sql .= " AND DATE(wr.created_at) <= ?";
                    $params[] = $endDate;
                }
                $sql .= " ORDER BY wr.created_at DESC";
                $data = $this->requestModel->db->fetchAll($sql, $params);
                break;

            case 'financial':
                $title = 'Financial Balance Statement';
                $summary = $this->settingModel->getSystemFinancialSummary();
                $recentContributions = $this->contributionModel->db->fetchAll("SELECT amount, payment_date as date, 'Contribution' as type, reference_number as ref FROM contributions WHERE status='Verified' ORDER BY payment_date DESC LIMIT 50");
                $recentDisbursements = $this->disbursementModel->db->fetchAll("SELECT amount, disbursed_at as date, 'Disbursement' as type, reference_number as ref FROM disbursements ORDER BY disbursed_at DESC LIMIT 50");
                
                $transactions = array_merge($recentContributions, $recentDisbursements);
                usort($transactions, function($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });

                $data = [
                    'summary' => $summary,
                    'transactions' => $transactions
                ];
                break;

            default:
                Session::setFlash('error', 'Invalid report type selected.');
                $this->redirect('report');
        }

        if ($format === 'excel') {
            $this->exportExcel($title, $type, $data);
        } else {
            // HTML Print view
            $viewFile = dirname(__DIR__) . '/Views/reports/print.php';
            if (file_exists($viewFile)) {
                extract([
                    'title' => $title,
                    'type' => $type,
                    'data' => $data,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);
                require_once $viewFile;
            } else {
                echo "Print template not found.";
            }
        }
    }

    /**
     * CSV formatting function for Microsoft Excel
     */
    private function exportExcel($title, $type, $data) {
        $filename = strtolower(str_replace(' ', '_', $title)) . '_' . date('Ymd_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add title and headers
        fputcsv($output, [$title]);
        fputcsv($output, ['Generated on:', date('Y-m-d H:i:s')]);
        fputcsv($output, []); // blank line

        if ($type === 'members') {
            fputcsv($output, ['Member Number', 'First Name', 'Last Name', 'Email', 'Phone', 'Join Date', 'Status']);
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['member_number'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['email'],
                    $row['phone'],
                    $row['join_date'],
                    $row['status']
                ]);
            }
        } elseif ($type === 'contributions') {
            fputcsv($output, ['Member Number', 'Member Name', 'Amount', 'Contribution Month', 'Payment Date', 'Method', 'Reference']);
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['member_number'],
                    $row['first_name'] . ' ' . $row['last_name'],
                    $row['amount'],
                    date('M Y', strtotime($row['contribution_month'])),
                    $row['payment_date'],
                    $row['payment_method'],
                    $row['reference_number']
                ]);
            }
        } elseif ($type === 'requests') {
            fputcsv($output, ['Member Number', 'Member Name', 'Category', 'Request Title', 'Requested Amount', 'Status', 'Submitted At']);
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['member_number'],
                    $row['first_name'] . ' ' . $row['last_name'],
                    $row['category_name'],
                    $row['title'],
                    $row['requested_amount'],
                    $row['status'],
                    $row['created_at']
                ]);
            }
        } elseif ($type === 'financial') {
            fputcsv($output, ['Summary Metrics']);
            fputcsv($output, ['Starting Virtual Balance', $data['summary']['starting_fund']]);
            fputcsv($output, ['Total Contributions Recorded', $data['summary']['total_contributions']]);
            fputcsv($output, ['Total Disbursements Released', $data['summary']['total_disbursements']]);
            fputcsv($output, ['Available Net Balance', $data['summary']['available_balance']]);
            fputcsv($output, []);
            fputcsv($output, ['Transaction Ledger']);
            fputcsv($output, ['Date', 'Type', 'Reference Number', 'Amount']);
            foreach ($data['transactions'] as $tx) {
                fputcsv($output, [
                    $tx['date'],
                    $tx['type'],
                    $tx['ref'],
                    $tx['type'] === 'Contribution' ? '+' . $tx['amount'] : '-' . $tx['amount']
                ]);
            }
        }

        fclose($output);
        exit;
    }
}
