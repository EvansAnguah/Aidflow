<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class HomeController extends Controller {
    /**
     * Renders the premium landing page for AidFlow
     */
    public function index() {
        // If the user is already logged in, redirect them to their dashboard
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }

        // Render the premium landing page directly bypassing standard panel header/footer
        $viewFile = dirname(__DIR__) . '/Views/home.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new \Exception("Landing page view not found.");
        }
    }
}
