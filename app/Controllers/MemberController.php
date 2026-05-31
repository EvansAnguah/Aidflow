<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\EmailService;
use App\Models\Member;
use App\Models\User;
use App\Models\Notification;

class MemberController extends Controller {
    private $memberModel;
    private $userModel;
    private $notificationModel;

    public function __construct() {
        $this->memberModel = new Member();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }

    /**
     * Directory Listing
     */
    public function index() {
        Session::requireRole(['Admin', 'Treasurer', 'Welfare Officer']);
        
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $members = $this->memberModel->searchAndFilter($search, $status);
        
        $this->view('members/index', [
            'title' => 'Member Management',
            'members' => $members,
            'search' => $search,
            'status' => $status
        ]);
    }

    /**
     * View specific member profile
     */
    public function view($id = '') {
        Session::requireLogin();
        
        $role = Session::get('role');
        $userId = Session::get('user_id');

        if (empty($id)) {
            // If ID is empty, member wants to view their own profile
            if ($role === 'Member') {
                $member = $this->memberModel->findByUserId($userId);
            } else {
                Session::setFlash('error', 'Please specify a member ID.');
                $this->redirect('dashboard');
            }
        } else {
            $member = $this->memberModel->findById($id);
        }

        if (!$member) {
            Session::setFlash('error', 'Member profile not found.');
            $this->redirect('dashboard');
        }

        // Restrict members from viewing other profiles
        if ($role === 'Member' && $member['user_id'] !== $userId) {
            Session::setFlash('error', 'Access Denied: Unauthorized to view this profile.');
            $this->redirect('dashboard');
        }

        $this->view('members/view', [
            'title' => 'Member Profile: ' . $member['first_name'] . ' ' . $member['last_name'],
            'member' => $member
        ]);
    }

    /**
     * Edit member profile
     */
    public function edit($id = '') {
        Session::requireLogin();
        
        $role = Session::get('role');
        $userId = Session::get('user_id');

        if (empty($id)) {
            if ($role === 'Member') {
                $member = $this->memberModel->findByUserId($userId);
            } else {
                Session::setFlash('error', 'Please specify a member ID.');
                $this->redirect('dashboard');
            }
        } else {
            $member = $this->memberModel->findById($id);
        }

        if (!$member) {
            Session::setFlash('error', 'Member profile not found.');
            $this->redirect('dashboard');
        }

        // Authorization checks
        if ($role !== 'Admin' && $member['user_id'] !== $userId) {
            Session::setFlash('error', 'Access Denied: You cannot edit this profile.');
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            // Validation
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['phone']) || empty($data['address']) || empty($data['date_of_birth'])) {
                Session::setFlash('error', 'Please fill in all required fields.');
            } else {
                // If member edits, they cannot change their active status
                $status = ($role === 'Admin') ? ($data['status'] ?? $member['status']) : $member['status'];
                
                $updateData = [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'date_of_birth' => $data['date_of_birth'],
                    'status' => $status
                ];

                if ($this->memberModel->update($member['id'], $updateData, $userId)) {
                    Session::setFlash('success', 'Profile updated successfully.');
                    $this->redirect('member/view/' . $member['id']);
                } else {
                    Session::setFlash('error', 'Failed to update profile.');
                }
            }
        }

        $this->view('members/edit', [
            'title' => 'Edit Member: ' . $member['first_name'] . ' ' . $member['last_name'],
            'member' => $member
        ]);
    }

    /**
     * Admin workflow: Toggle User status (Approve registration, activate, deactivate)
     */
    public function updateStatus() {
        Session::requireRole('Admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            $targetUserId = (int)$data['user_id'];
            $newStatus = $data['status']; // 'Active', 'Inactive', 'Pending'
            $adminId = Session::get('user_id');

            $user = $this->userModel->findById($targetUserId);
            if (!$user) {
                $this->json(['success' => false, 'message' => 'User not found.']);
            }

            if ($this->userModel->updateStatus($targetUserId, $newStatus, $adminId)) {
                // Keep member status in sync with user status
                $member = $this->memberModel->findByUserId($targetUserId);
                if ($member) {
                    $mStatus = ($newStatus === 'Active') ? 'Active' : 'Inactive';
                    $this->memberModel->db->query("UPDATE members SET status = ? WHERE id = ?", [$mStatus, $member['id']]);
                }

                // If approved (Pending -> Active), send Email notification
                if ($user['status'] === 'Pending' && $newStatus === 'Active') {
                    $fullName = $member ? ($member['first_name'] . ' ' . $member['last_name']) : $user['username'];
                    EmailService::sendAccountApprovalEmail($user['email'], $fullName);
                    
                    // Add system notification for user
                    $this->notificationModel->create(
                        $targetUserId,
                        'Account Approved',
                        'Welcome to AidFlow! Your registration has been approved by the administrator.'
                    );
                }

                $this->json(['success' => true, 'message' => 'User status updated successfully.']);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update status.']);
            }
        }
    }
}
