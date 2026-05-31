<?php
namespace App\Core;

class Controller {
    /**
     * Renders a layout view and passes variable data to it
     */
    protected function view($viewName, $data = []) {
        $viewFile = dirname(__DIR__) . '/Views/' . $viewName . '.php';
        
        if (file_exists($viewFile)) {
            // Extract data array to local variables
            extract($data);

            // Set up main content file to be included in layout
            $viewContentFile = $viewFile;
            
            // Core Layout wrappers
            $headerFile = dirname(__DIR__) . '/Views/layouts/header.php';
            $footerFile = dirname(__DIR__) . '/Views/layouts/footer.php';
            $sidebarFile = dirname(__DIR__) . '/Views/layouts/sidebar.php';

            if (file_exists($headerFile) && file_exists($footerFile)) {
                require_once $headerFile;
                if (Session::isLoggedIn() && file_exists($sidebarFile)) {
                    require_once $sidebarFile;
                }
                require_once $viewContentFile;
                require_once $footerFile;
            } else {
                // If layout header/footer don't exist, just require view
                require_once $viewContentFile;
            }
        } else {
            throw new \Exception("View template '$viewName' not found.");
        }
    }

    /**
     * Redirects to a different route
     */
    protected function redirect($url) {
        header("Location: " . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    /**
     * JSON Response Helper for AJAX
     */
    protected function json($data, $statusCode = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * CSRF Validation helper
     */
    protected function validateCSRF() {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Session::validateCSRFToken($token)) {
            $this->json(['success' => false, 'message' => 'Invalid or expired CSRF token.'], 403);
        }
    }

    /**
     * Request Sanitizer
     * Sanitizes strings and input fields recursively
     */
    protected function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeInput($value);
            }
            return $data;
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Standard File Uploader helper
     */
    protected function uploadFile($fileField, $targetSubDir = 'documents') {
        if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error or no file provided.'];
        }

        $file = $_FILES[$fileField];
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'File exceeds maximum upload size (5MB).'];
        }

        // Validate extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_FILE_TYPES)) {
            return ['success' => false, 'error' => 'File type not allowed. Supported types: ' . implode(', ', ALLOWED_FILE_TYPES)];
        }

        // Create secure name
        $newFileName = uniqid('doc_', true) . '.' . $ext;
        
        $targetDir = UPLOAD_DIR . rtrim($targetSubDir, '/') . '/';
        
        // Ensure folder exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'success' => true, 
                'file_name' => $newFileName,
                'relative_path' => 'public/uploads/' . $targetSubDir . '/' . $newFileName
            ];
        }

        return ['success' => false, 'error' => 'Failed to write upload to server.'];
    }
}
