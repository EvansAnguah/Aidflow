<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\EmailService;
use App\Models\User;
use App\Models\Member;
use App\Models\Notification;

class AuthController extends Controller {
    private $userModel;
    private $memberModel;
    private $notificationModel;

    public function __construct() {
        $this->userModel = new User();
        $this->memberModel = new Member();
        $this->notificationModel = new Notification();
    }

    /**
     * Show/Handle Login Page
     */
    public function login() {
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            $user = $this->userModel->authenticate($data['login'], $data['password']);

            if ($user) {
                if ($user['status'] === 'Pending') {
                    Session::setFlash('error', 'Your account is pending administrator approval.');
                } elseif ($user['status'] === 'Inactive') {
                    Session::setFlash('error', 'Your account has been deactivated. Please contact support.');
                } else {
                    Session::login($user);
                    
                    // If Member, load member profile into session
                    if ($user['role'] === 'Member') {
                        $member = $this->memberModel->findByUserId($user['id']);
                        if ($member) {
                            $_SESSION['member_id'] = $member['id'];
                            $_SESSION['member_number'] = $member['member_number'];
                            $_SESSION['full_name'] = $member['first_name'] . ' ' . $member['last_name'];
                        }
                    }
                    
                    Session::setFlash('success', 'Logged in successfully. Welcome, ' . $user['username'] . '!');
                    $this->redirect('dashboard');
                }
            } else {
                Session::setFlash('error', 'Invalid username/email or password.');
            }
        }

        $this->view('auth/login');
    }

    /**
     * Show/Handle Registration Page
     */
    public function register() {
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            // Validation
            if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['confirm_password'])) {
                Session::setFlash('error', 'Please fill in all required fields.');
                $this->view('auth/register', ['post' => $data]);
                return;
            }

            if ($data['password'] !== $data['confirm_password']) {
                Session::setFlash('error', 'Passwords do not match.');
                $this->view('auth/register', ['post' => $data]);
                return;
            }

            if ($this->userModel->findByUsername($data['username'])) {
                Session::setFlash('error', 'Username is already taken.');
                $this->view('auth/register', ['post' => $data]);
                return;
            }

            if ($this->userModel->findByEmail($data['email'])) {
                Session::setFlash('error', 'Email is already registered.');
                $this->view('auth/register', ['post' => $data]);
                return;
            }

            // Begin transaction to create user & profile
            $userId = $this->userModel->register($data['username'], $data['email'], $data['password'], 'Member');

            if ($userId) {
                $memberData = [
                    'user_id' => $userId,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'date_of_birth' => $data['date_of_birth'],
                    'join_date' => date('Y-m-d'),
                    'status' => 'Inactive' // Inactive/Pending until Admin approves
                ];

                $memberId = $this->memberModel->create($memberData);

                if ($memberId) {
                    // Send Welcome Email
                    $fullName = $data['first_name'] . ' ' . $data['last_name'];
                    EmailService::sendWelcomeEmail($data['email'], $fullName, $data['username']);

                    // Create in-system notifications for Admins
                    $this->notificationModel->createForRole(
                        'Admin', 
                        'New Registration Request', 
                        "Member $fullName ({$data['username']}) has registered and is pending approval."
                    );

                    Session::setFlash('success', 'Registration successful! Your account is pending admin approval. You will receive an email confirmation.');
                    $this->redirect('auth/login');
                } else {
                    Session::setFlash('error', 'Failed to create membership profile.');
                }
            } else {
                Session::setFlash('error', 'Registration failed. Please try again.');
            }
        }

        $this->view('auth/register');
    }

    /**
     * Logout
     */
    public function logout() {
        Session::logout();
        header("Location: " . BASE_URL . '/auth/login');
        exit;
    }

    /**
     * Forgot Password
     */
    public function forgot_password() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if (empty($email)) {
                Session::setFlash('error', 'Please enter your email address.');
            } else {
                $user = $this->userModel->findByEmail($email);
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $this->userModel->setResetToken($email, $token);
                    
                    // Fetch full name if Member
                    $name = $user['username'];
                    if ($user['role'] === 'Member') {
                        $member = $this->memberModel->findByUserId($user['id']);
                        if ($member) {
                            $name = $member['first_name'] . ' ' . $member['last_name'];
                        }
                    }

                    // Send email
                    EmailService::sendPasswordReset($email, $name, $token);
                }
                
                // For security, show success even if email is not found
                Session::setFlash('success', 'If the email matches an account, password reset instructions have been sent.');
                $this->redirect('auth/login');
            }
        }

        $this->view('auth/forgot_password');
    }

    /**
     * Reset Password
     */
    public function reset_password($token = '') {
        if (empty($token)) {
            $this->redirect('auth/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            if (empty($data['password']) || empty($data['confirm_password'])) {
                Session::setFlash('error', 'Please fill in all fields.');
            } elseif ($data['password'] !== $data['confirm_password']) {
                Session::setFlash('error', 'Passwords do not match.');
            } else {
                $success = $this->userModel->resetPassword($token, $data['password']);
                if ($success) {
                    Session::setFlash('success', 'Password reset successful! You can now log in.');
                    $this->redirect('auth/login');
                } else {
                    Session::setFlash('error', 'Invalid or expired token.');
                }
            }
        }

        $this->view('auth/reset_password', ['token' => $token]);
    }
}
