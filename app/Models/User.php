<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findByUsername($username) {
        return $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
    }

    /**
     * Authenticates a user credentials
     */
    public function authenticate($login, $password) {
        // Allow login via email or username
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE username = ? OR email = ?", 
            [$login, $login]
        );

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Creates a new user record (pending by default)
     */
    public function register($username, $email, $password, $role = 'Member') {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        // Members start as Pending. Staff/Admins start as Active
        $status = ($role === 'Member') ? 'Pending' : 'Active';

        $sql = "INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
        $success = $this->db->query($sql, [$username, $email, $hashed, $role, $status]);

        if ($success) {
            $userId = $this->db->lastInsertId();
            $this->logAction($userId, 'Register', "User registered: $username as $role");
            return $userId;
        }
        return false;
    }

    /**
     * Set password reset token
     */
    public function setResetToken($email, $token) {
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        return $this->db->query($sql, [$token, $expiry, $email]);
    }

    /**
     * Verify token and reset password
     */
    public function resetPassword($token, $newPassword) {
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()", 
            [$token]
        );

        if (!$user) {
            return false;
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?";
        $success = $this->db->query($sql, [$hashed, $user['id']]);

        if ($success) {
            $this->logAction($user['id'], 'Reset Password', "Password reset via email token");
            return true;
        }
        return false;
    }

    /**
     * Update user status (Approve, Deactivate, etc.)
     */
    public function updateStatus($userId, $status, $adminId) {
        $sql = "UPDATE users SET status = ? WHERE id = ?";
        $success = $this->db->query($sql, [$status, $userId]);
        if ($success) {
            $this->logAction($adminId, 'Update User Status', "Updated user ID $userId status to $status");
            return true;
        }
        return false;
    }

    /**
     * Gets all users (with optional role filters)
     */
    public function getAll($roleFilter = null) {
        if ($roleFilter) {
            return $this->db->fetchAll("SELECT * FROM users WHERE role = ? ORDER BY id DESC", [$roleFilter]);
        }
        return $this->db->fetchAll("SELECT * FROM users ORDER BY id DESC");
    }
}
