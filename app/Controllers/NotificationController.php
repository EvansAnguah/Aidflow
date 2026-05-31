<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Notification;

class NotificationController extends Controller {
    private $notificationModel;

    public function __construct() {
        $this->notificationModel = new Notification();
    }

    /**
     * View notification history page
     */
    public function index() {
        Session::requireLogin();
        
        $userId = Session::get('user_id');
        $notifications = $this->notificationModel->getByUserId($userId);

        $this->view('notifications/index', [
            'title' => 'Notifications Centre',
            'notifications' => $notifications
        ]);
    }

    /**
     * AJAX Endpoint: Get unread count + latest 5 notifications
     */
    public function getUnread() {
        Session::requireLogin();
        
        $userId = Session::get('user_id');
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        
        // Fetch last 5 notifications
        $all = $this->notificationModel->getByUserId($userId, 5);
        
        $this->json([
            'unread_count' => $unreadCount,
            'notifications' => $all
        ]);
    }

    /**
     * AJAX Endpoint: Mark single notification as read
     */
    public function read($id) {
        Session::requireLogin();
        
        $userId = Session::get('user_id');
        $success = $this->notificationModel->markAsRead((int)$id, $userId);
        
        $this->json(['success' => $success]);
    }

    /**
     * AJAX Endpoint: Mark all as read
     */
    public function readAll() {
        Session::requireLogin();
        
        $userId = Session::get('user_id');
        $success = $this->notificationModel->markAllAsRead($userId);

        Session::setFlash('success', 'All notifications marked as read.');
        $this->redirect('notification');
    }
}
