<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\WelfareCategory;

class CategoryController extends Controller {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new WelfareCategory();
    }

    /**
     * Admin view categories
     */
    public function index() {
        Session::requireRole('Admin');
        
        $categories = $this->categoryModel->getAll();
        
        $this->view('welfare/categories', [
            'title' => 'Welfare Categories',
            'categories' => $categories
        ]);
    }

    /**
     * Create Category
     */
    public function create() {
        Session::requireRole('Admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            if (empty($data['name']) || empty($data['description']) || !isset($data['max_amount'])) {
                Session::setFlash('error', 'Please fill in all fields.');
                $this->redirect('category');
            }

            $success = $this->categoryModel->create(
                $data['name'],
                $data['description'],
                (float)$data['max_amount'],
                Session::get('user_id')
            );

            if ($success) {
                Session::setFlash('success', 'Welfare category created successfully.');
            } else {
                Session::setFlash('error', 'Failed to create category. It might already exist.');
            }
            $this->redirect('category');
        }
    }

    /**
     * Update Category
     */
    public function edit($id) {
        Session::requireRole('Admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);

            if (empty($data['name']) || empty($data['description']) || !isset($data['max_amount'])) {
                Session::setFlash('error', 'Please fill in all fields.');
                $this->redirect('category');
            }

            $success = $this->categoryModel->update(
                $id,
                $data['name'],
                $data['description'],
                (float)$data['max_amount'],
                Session::get('user_id')
            );

            if ($success) {
                Session::setFlash('success', 'Welfare category updated successfully.');
            } else {
                Session::setFlash('error', 'Failed to update category.');
            }
            $this->redirect('category');
        }
    }

    /**
     * Delete Category
     */
    public function delete($id) {
        Session::requireRole('Admin');

        $success = $this->categoryModel->delete($id, Session::get('user_id'));
        if ($success) {
            Session::setFlash('success', 'Category deleted successfully.');
        } else {
            Session::setFlash('error', 'Failed to delete category. Ensure no welfare requests are using it.');
        }
        $this->redirect('category');
    }
}
