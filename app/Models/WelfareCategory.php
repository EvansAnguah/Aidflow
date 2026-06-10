<?php
namespace App\Models;

use App\Core\Model;

class WelfareCategory extends Model {
    
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM welfare_categories WHERE id = ?", [$id]);
    }

    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM welfare_categories ORDER BY name ASC");
    }

    public function create($name, $description, $maxAmount, $adminId) {
        $sql = "INSERT INTO welfare_categories (name, description, max_amount) VALUES (?, ?, ?)";
        $success = $this->db->query($sql, [$name, $description, $maxAmount]);
        if ($success) {
            $catId = $this->db->lastInsertId();
            $this->logAction($adminId, 'Create Category', "Created welfare category: $name");
            return $catId;
        }
        return false;
    }

    public function update($id, $name, $description, $maxAmount, $adminId) {
        $sql = "UPDATE welfare_categories SET name = ?, description = ?, max_amount = ? WHERE id = ?";
        $success = $this->db->query($sql, [$name, $description, $maxAmount, $id]);
        if ($success) {
            $this->logAction($adminId, 'Update Category', "Updated welfare category ID $id");
            return true;
        }
        return false;
    }

    public function delete($id, $adminId) {
        // Check if there are requests associated with this category
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM welfare_requests WHERE category_id = ?", [$id]);
        if ($count > 0) {
            return false; // Prevent deletion due to relational constraint
        }

        $sql = "DELETE FROM welfare_categories WHERE id = ?";
        $success = $this->db->query($sql, [$id]);
        if ($success) {
            $this->logAction($adminId, 'Delete Category', "Deleted welfare category ID $id");
            return true;
        }
        return false;
    }
}
