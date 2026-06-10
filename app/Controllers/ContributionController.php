<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Contribution;
use App\Models\Member;
use App\Models\Notification;

class ContributionController extends Controller {
    private $contributionModel;
    private $memberModel;
    private $notificationModel;

    public function __construct() {
        $this->contributionModel = new Contribution();
        $this->memberModel = new Member();
        $this->notificationModel = new Notification();
    }

    /**
     * List all contributions (Admin/Treasurer)
     */
    public function index() {
        Session::requireRole(['Admin', 'Treasurer']);

        $search = $_GET['search'] ?? '';
        $month = $_GET['month'] ?? ''; // Format: YYYY-MM
        
        $contributions = $this->contributionModel->getAll($search, $month);
        
        $this->view('contributions/index', [
            'title' => 'Contribution Records',
            'contributions' => $contributions,
            'search' => $search,
            'month' => $month
        ]);
    }

    /**
     * List member's personal contributions
     */
    public function my_contributions() {
        Session::requireRole('Member');
        
        $userId = Session::get('user_id');
        $member = $this->memberModel->findByUserId($userId);
        
        if (!$member) {
            Session::setFlash('error', 'Member profile not found.');
            $this->redirect('dashboard');
        }

        $contributions = $this->contributionModel->getByMemberId($member['id']);
        $outstanding = $this->contributionModel->getOutstandingContributions($member);

        $this->view('contributions/my_contributions', [
            'title' => 'My Contributions',
            'contributions' => $contributions,
            'outstanding' => $outstanding
        ]);
    }

    /**
     * Record contribution (Treasurer only)
     */
    public function record() {
        Session::requireRole(['Admin', 'Treasurer']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            // Validation
            if (empty($data['member_id']) || empty($data['amount']) || empty($data['contribution_month']) || empty($data['payment_method']) || empty($data['reference_number'])) {
                Session::setFlash('error', 'Please fill in all required fields.');
                $this->redirect('contribution/record');
            }

            // Check if contribution for this month already exists
            $existing = $this->contributionModel->db->fetchColumn(
                "SELECT COUNT(*) FROM contributions WHERE member_id = ? AND contribution_month = ?",
                [$data['member_id'], $data['contribution_month'] . '-01']
            );

            if ($existing > 0) {
                Session::setFlash('error', 'Contribution for this month has already been recorded for this member.');
                $this->redirect('contribution/record');
            }

            // Check for upload (optional payment receipt)
            $receiptPath = null;
            if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->uploadFile('receipt', 'receipts');
                if ($upload['success']) {
                    $receiptPath = $upload['relative_path'];
                } else {
                    Session::setFlash('error', 'Receipt upload failed: ' . $upload['error']);
                    $this->redirect('contribution/record');
                }
            }

            $contributionData = [
                'member_id' => $data['member_id'],
                'amount' => $data['amount'],
                'contribution_month' => $data['contribution_month'] . '-01',
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'],
                'recorded_by' => Session::get('user_id'),
                'receipt_path' => $receiptPath,
                'status' => 'Verified'
            ];

            $contributionId = $this->contributionModel->create($contributionData);

            if ($contributionId) {
                // Send system notification to member
                $member = $this->memberModel->findById($data['member_id']);
                if ($member) {
                    $monthName = date('F Y', strtotime($data['contribution_month'] . '-01'));
                    $this->notificationModel->create(
                        $member['user_id'],
                        'Contribution Recorded',
                        "Your contribution of $" . number_format($data['amount'], 2) . " for $monthName has been recorded."
                    );
                }

                Session::setFlash('success', 'Contribution recorded successfully.');
                $this->redirect('contribution/receipt/' . $contributionId);
            } else {
                Session::setFlash('error', 'Failed to record contribution.');
                $this->redirect('contribution/record');
            }
        }

        $members = $this->memberModel->searchAndFilter('', 'Active');
        $this->view('contributions/record', [
            'title' => 'Record Contribution',
            'members' => $members
        ]);
    }

    /**
     * Show / Print Receipt
     */
    public function receipt($id) {
        Session::requireLogin();
        
        $contribution = $this->contributionModel->findById($id);

        if (!$contribution) {
            Session::setFlash('error', 'Contribution record not found.');
            $this->redirect('dashboard');
        }

        // Members can only view their own receipts
        if (Session::get('role') === 'Member') {
            $member = $this->memberModel->findByUserId(Session::get('user_id'));
            if ($member['id'] !== $contribution['member_id']) {
                Session::setFlash('error', 'Unauthorized to view this receipt.');
                $this->redirect('dashboard');
            }
        }

        // Load printer-friendly layout directly (no sidebar/header wrappers)
        $viewFile = dirname(__DIR__) . '/Views/contributions/receipt.php';
        if (file_exists($viewFile)) {
            extract(['contribution' => $contribution]);
            require_once $viewFile;
        } else {
            Session::setFlash('error', 'Receipt view template not found.');
            $this->redirect('dashboard');
        }
    }
}
