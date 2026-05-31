<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\EmailService;
use App\Models\WelfareRequest;
use App\Models\WelfareCategory;
use App\Models\Member;
use App\Models\Notification;
use App\Models\User;

class WelfareController extends Controller {
    private $requestModel;
    private $categoryModel;
    private $memberModel;
    private $notificationModel;
    private $userModel;

    public function __construct() {
        $this->requestModel = new WelfareRequest();
        $this->categoryModel = new WelfareCategory();
        $this->memberModel = new Member();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }

    /**
     * List all requests
     */
    public function index() {
        Session::requireLogin();
        
        $role = Session::get('role');
        $userId = Session::get('user_id');

        $status = $_GET['status'] ?? '';
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';

        if ($role === 'Member') {
            $member = $this->memberModel->findByUserId($userId);
            if (!$member) {
                Session::setFlash('error', 'Member profile not found.');
                $this->redirect('dashboard');
            }
            $requests = $this->requestModel->getByMemberId($member['id']);
        } else {
            $requests = $this->requestModel->getAll($status, $category, $search);
        }

        $categories = $this->categoryModel->getAll();

        $this->view('welfare/index', [
            'title' => 'Welfare Requests',
            'requests' => $requests,
            'categories' => $categories,
            'status' => $status,
            'category_filter' => $category,
            'search' => $search
        ]);
    }

    /**
     * Create Welfare Request (Member only)
     */
    public function create() {
        Session::requireRole('Member');
        
        $userId = Session::get('user_id');
        $member = $this->memberModel->findByUserId($userId);

        if (!$member) {
            Session::setFlash('error', 'Member profile not found.');
            $this->redirect('dashboard');
        }

        if ($member['status'] !== 'Active') {
            Session::setFlash('error', 'Only active members can submit welfare requests.');
            $this->redirect('welfare');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            // Validation
            if (empty($data['category_id']) || empty($data['title']) || empty($data['description']) || empty($data['requested_amount'])) {
                Session::setFlash('error', 'Please fill in all required fields.');
                $this->redirect('welfare/create');
            }

            // Verify requested amount is positive and does not exceed category max
            $category = $this->categoryModel->findById($data['category_id']);
            if (!$category) {
                Session::setFlash('error', 'Invalid category selected.');
                $this->redirect('welfare/create');
            }

            if ((float)$data['requested_amount'] <= 0) {
                Session::setFlash('error', 'Requested amount must be greater than zero.');
                $this->redirect('welfare/create');
            }

            if ((float)$data['requested_amount'] > (float)$category['max_amount']) {
                Session::setFlash('error', 'Requested amount exceeds the category maximum limit of $' . number_format($category['max_amount'], 2));
                $this->redirect('welfare/create');
            }

            // File upload handling
            $docPath = null;
            if (isset($_FILES['supporting_document']) && $_FILES['supporting_document']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->uploadFile('supporting_document', 'documents');
                if ($upload['success']) {
                    $docPath = $upload['relative_path'];
                } else {
                    Session::setFlash('error', 'Document upload failed: ' . $upload['error']);
                    $this->redirect('welfare/create');
                }
            }

            $requestData = [
                'member_id' => $member['id'],
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'requested_amount' => $data['requested_amount'],
                'supporting_document' => $docPath,
                'status' => 'Pending'
            ];

            $requestId = $this->requestModel->create($requestData);

            if ($requestId) {
                // Email notifications
                $fullName = $member['first_name'] . ' ' . $member['last_name'];
                
                // 1. Notify member
                EmailService::sendWelfareSubmissionEmail($userEmail = Session::get('email'), $fullName, $data['title'], $data['requested_amount']);
                
                // 2. Notify Welfare Officer
                $this->notificationModel->createForRole('Welfare Officer', 'New Welfare Request', "Member $fullName has submitted a welfare request: {$data['title']}");
                
                // 3. Notify Admin
                $this->notificationModel->createForRole('Admin', 'New Welfare Request', "Member $fullName has submitted a welfare request: {$data['title']}");

                Session::setFlash('success', 'Welfare request submitted successfully.');
                $this->redirect('welfare');
            } else {
                Session::setFlash('error', 'Failed to submit welfare request.');
                $this->redirect('welfare/create');
            }
        }

        $categories = $this->categoryModel->getAll();
        $this->view('welfare/create', [
            'title' => 'Submit Welfare Request',
            'categories' => $categories
        ]);
    }

    /**
     * View Detailed Welfare Request + Comments & Workflow Actions
     */
    public function view($id) {
        Session::requireLogin();
        
        $request = $this->requestModel->findById($id);

        if (!$request) {
            Session::setFlash('error', 'Welfare request not found.');
            $this->redirect('welfare');
        }

        $role = Session::get('role');
        $userId = Session::get('user_id');

        // Verify authorization for member
        if ($role === 'Member') {
            $member = $this->memberModel->findByUserId($userId);
            if ($member['id'] !== $request['member_id']) {
                Session::setFlash('error', 'Unauthorized to view this request.');
                $this->redirect('welfare');
            }
        }

        $history = $this->requestModel->getApprovalHistory($id);

        $this->view('welfare/view', [
            'title' => 'Request details: ' . $request['title'],
            'request' => $request,
            'history' => $history
        ]);
    }

    /**
     * Post comment on a welfare request (Staff & Admin and requesting Member)
     */
    public function comment($id) {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $comments = $this->sanitizeInput($_POST['comments'] ?? '');

            if (empty($comments)) {
                Session::setFlash('error', 'Comment content cannot be empty.');
                $this->redirect('welfare/view/' . $id);
            }

            $request = $this->requestModel->findById($id);
            if (!$request) {
                Session::setFlash('error', 'Request not found.');
                $this->redirect('welfare');
            }

            $role = Session::get('role');
            $userId = Session::get('user_id');

            // Member validation
            if ($role === 'Member') {
                $member = $this->memberModel->findByUserId($userId);
                if ($member['id'] !== $request['member_id']) {
                    Session::setFlash('error', 'Unauthorized.');
                    $this->redirect('welfare');
                }
            }

            // Record standard comment in approvals table
            $action = 'Review'; // Generic action for commenting
            $sql = "INSERT INTO approvals (request_id, user_id, action, comments) VALUES (?, ?, ?, ?)";
            $this->requestModel->db->query($sql, [$id, $userId, $action, $comments]);

            // Create notification for other side
            if ($role === 'Member') {
                $this->notificationModel->createForRole('Welfare Officer', 'New Request Comment', "Member has commented on Request #$id: " . substr($comments, 0, 40) . "...");
            } else {
                $this->notificationModel->create($request['user_id'], 'New Comment on Request', "Staff has commented on your request: " . substr($comments, 0, 40) . "...");
            }

            Session::setFlash('success', 'Comment added successfully.');
            $this->redirect('welfare/view/' . $id);
        }
    }

    /**
     * Handle Workflow updates (Review, Recommend, Approve, Reject)
     */
    public function workflow($id) {
        Session::requireRole(['Admin', 'Welfare Officer']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            $action = $data['action']; // 'Review', 'Recommend', 'Approve', 'Reject'
            $comments = $data['comments'] ?? '';
            $userId = Session::get('user_id');

            $request = $this->requestModel->findById($id);
            if (!$request) {
                Session::setFlash('error', 'Request not found.');
                $this->redirect('welfare');
            }

            $newStatus = $request['status'];

            if ($action === 'Review') {
                Session::requireRole('Welfare Officer');
                $newStatus = 'Under Review';
                $actionName = 'Review';
            } elseif ($action === 'Recommend') {
                Session::requireRole('Welfare Officer');
                // Status remains Under Review but log recommendation
                $newStatus = 'Under Review';
                $actionName = 'Recommend';
            } elseif ($action === 'Approve') {
                Session::requireRole('Admin');
                $newStatus = 'Approved';
                $actionName = 'Approve';
            } elseif ($action === 'Reject') {
                Session::requireRole('Admin');
                if (empty($comments)) {
                    Session::setFlash('error', 'Rejection reason (comments) is required when rejecting.');
                    $this->redirect('welfare/view/' . $id);
                }
                $newStatus = 'Rejected';
                $actionName = 'Reject';
            } else {
                Session::setFlash('error', 'Invalid action.');
                $this->redirect('welfare/view/' . $id);
            }

            if ($this->requestModel->updateStatus($id, $newStatus, $userId, $actionName, $comments)) {
                // Email member notification
                $memberUser = $this->userModel->findById($request['user_id']);
                $memberName = $request['first_name'] . ' ' . $request['last_name'];
                
                if ($newStatus === 'Approved') {
                    EmailService::sendWelfareApprovalEmail($memberUser['email'], $memberName, $request['title'], $request['requested_amount']);
                    $this->notificationModel->create($request['user_id'], 'Welfare Request Approved', "Your request '{$request['title']}' has been approved. Awaiting disbursement.");
                    
                    // Notify Treasurer
                    $this->notificationModel->createForRole('Treasurer', 'Welfare Request Approved', "Welfare Request #$id is approved and ready for disbursement.");
                } elseif ($newStatus === 'Rejected') {
                    EmailService::sendWelfareRejectionEmail($memberUser['email'], $memberName, $request['title'], $comments);
                    $this->notificationModel->create($request['user_id'], 'Welfare Request Rejected', "Your request '{$request['title']}' has been rejected. Reason: $comments");
                } else {
                    $this->notificationModel->create($request['user_id'], 'Welfare Request Status Updated', "Your request status is now: $newStatus");
                }

                Session::setFlash('success', 'Workflow action ' . $actionName . ' completed.');
            } else {
                Session::setFlash('error', 'Failed to update workflow.');
            }

            $this->redirect('welfare/view/' . $id);
        }
    }
}
