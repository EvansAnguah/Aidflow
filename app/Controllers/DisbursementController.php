<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\EmailService;
use App\Models\Disbursement;
use App\Models\WelfareRequest;
use App\Models\Member;
use App\Models\User;
use App\Models\Notification;

class DisbursementController extends Controller {
    private $disbursementModel;
    private $requestModel;
    private $memberModel;
    private $userModel;
    private $notificationModel;

    public function __construct() {
        $this->disbursementModel = new Disbursement();
        $this->requestModel = new WelfareRequest();
        $this->memberModel = new Member();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }

    /**
     * List disbursements
     */
    public function index() {
        Session::requireRole(['Admin', 'Treasurer']);
        
        $search = $_GET['search'] ?? '';
        $disbursements = $this->disbursementModel->getAll($search);

        $this->view('disbursements/index', [
            'title' => 'Disbursement Records',
            'disbursements' => $disbursements,
            'search' => $search
        ]);
    }

    /**
     * Process disbursement (Treasurer only)
     */
    public function record() {
        Session::requireRole('Treasurer');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            $requestId = (int)$data['request_id'];
            $amount = (float)$data['amount'];
            $paymentMethod = $data['payment_method'];
            $referenceNumber = $data['reference_number'];
            $notes = $data['notes'] ?? '';

            if (empty($requestId) || empty($amount) || empty($paymentMethod) || empty($referenceNumber)) {
                Session::setFlash('error', 'Please fill in all fields.');
                $this->redirect('disbursement/record');
            }

            // Verify request status is Approved
            $request = $this->requestModel->findById($requestId);
            if (!$request || $request['status'] !== 'Approved') {
                Session::setFlash('error', 'Invalid request or request is not approved for disbursement.');
                $this->redirect('disbursement/record');
            }

            // Verify fund balance has enough cash
            $availableBalance = $this->disbursementModel->db->fetchColumn(
                "SELECT setting_value FROM settings WHERE setting_key = 'welfare_fund_balance'"
            );
            
            // Wait, we need to compare available balance!
            // Actually, let's allow it but warn if it exceeds available balance, or let's enforce it
            if ($amount > (float)$availableBalance) {
                Session::setFlash('error', 'Insufficient welfare funds! Current available balance is $' . number_format($availableBalance, 2));
                $this->redirect('disbursement/record');
            }

            // Process optional receipt upload
            $receiptPath = null;
            if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->uploadFile('receipt', 'disbursements');
                if ($upload['success']) {
                    $receiptPath = $upload['relative_path'];
                }
            }

            $disbursementData = [
                'request_id' => $requestId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'disbursed_by' => Session::get('user_id'),
                'notes' => $notes,
                'receipt_path' => $receiptPath
            ];

            $disbursementId = $this->disbursementModel->create($disbursementData);

            if ($disbursementId) {
                // Email member notification
                $memberUser = $this->userModel->findById($request['user_id']);
                $memberName = $request['first_name'] . ' ' . $request['last_name'];
                
                EmailService::sendDisbursementEmail($memberUser['email'], $memberName, $request['title'], $amount, $referenceNumber);
                
                // Internal system notifications
                $this->notificationModel->create(
                    $request['user_id'],
                    'Funds Disbursed',
                    "Funds ($" . number_format($amount, 2) . ") for your request '{$request['title']}' have been released. Reference: $referenceNumber"
                );

                Session::setFlash('success', 'Disbursement recorded successfully.');
                $this->redirect('disbursement/receipt/' . $disbursementId);
            } else {
                Session::setFlash('error', 'Failed to record disbursement.');
                $this->redirect('disbursement/record');
            }
        }

        // Fetch all approved welfare requests that are not yet disbursed (status = 'Approved')
        $approvedRequests = $this->requestModel->getAll('Approved');

        $this->view('disbursements/record', [
            'title' => 'Disburse Funds',
            'approved_requests' => $approvedRequests
        ]);
    }

    /**
     * Show / Print Disbursement Receipt
     */
    public function receipt($id) {
        Session::requireLogin();
        
        $disbursement = $this->disbursementModel->findById($id);

        if (!$disbursement) {
            Session::setFlash('error', 'Disbursement record not found.');
            $this->redirect('dashboard');
        }

        // Members can only view their own receipts
        if (Session::get('role') === 'Member') {
            $member = $this->memberModel->findByUserId(Session::get('user_id'));
            
            // Check request owner
            $request = $this->requestModel->findById($disbursement['request_id']);
            if ($member['id'] !== $request['member_id']) {
                Session::setFlash('error', 'Unauthorized to view this receipt.');
                $this->redirect('dashboard');
            }
        }

        // Load printer-friendly layout directly
        $viewFile = dirname(__DIR__) . '/Views/disbursements/receipt.php';
        if (file_exists($viewFile)) {
            extract(['disbursement' => $disbursement]);
            require_once $viewFile;
        } else {
            Session::setFlash('error', 'Disbursement receipt view template not found.');
            $this->redirect('dashboard');
        }
    }
}
