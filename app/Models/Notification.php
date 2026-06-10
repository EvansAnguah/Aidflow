<?php
namespace App\Models;

use App\Core\Model;

class Notification extends Model {

    /**
     * Create in-system notification
     */
    public function create($userId, $title, $message) {
        $sql = "INSERT INTO notifications (user_id, title, message, status) VALUES (?, ?, ?, 'Unread')";
        return $this->db->query($sql, [$userId, $title, $message]);
    }

    /**
     * Notify all users of a specific role (e.g. notify all Admins/Welfare Officers)
     */
    public function createForRole($role, $title, $message) {
        $users = $this->db->fetchAll("SELECT id FROM users WHERE role = ? AND status = 'Active'", [$role]);
        foreach ($users as $u) {
            $this->create($u['id'], $title, $message);
        }
    }

    /**
     * Get recent notifications for a user
     */
    public function getByUserId($userId, $limit = 50) {
        return $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount($userId) {
        return (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = 'Unread'",
            [$userId]
        );
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($id, $userId) {
        $sql = "UPDATE notifications SET status = 'Read' WHERE id = ? AND user_id = ?";
        return $this->db->query($sql, [$id, $userId]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications SET status = 'Read' WHERE user_id = ?";
        return $this->db->query($sql, [$userId]);
    }
}
