<?php
use App\Core\View;
?>
<div class="row">
    <!-- Category setup form -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold" id="formTitle"><i class="fas fa-plus me-1"></i>Add Category</h5>
            </div>
            
            <div class="card-body">
                <form id="categoryForm" action="<?= BASE_URL ?>/category/create" method="POST">
                    <?= View::csrfField() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Medical Support">
                    </div>

                    <div class="mb-3">
                        <label for="max_amount" class="form-label">Maximum Disbursable Amount ($)</label>
                        <input type="number" class="form-control" id="max_amount" name="max_amount" required step="0.01" value="500.00">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Category Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Describe what support items this category handles..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">Save Category</button>
                    <button type="button" class="btn btn-outline-secondary w-100 py-2 mt-2 d-none" id="cancelBtn" onclick="resetForm()">Cancel Edit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Category listings directory -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Welfare Categories List</h5>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Max Amount</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="fw-semibold text-primary"><?= View::escape($cat['name']) ?></td>
                                    <td><small class="text-muted"><?= View::escape($cat['description']) ?></small></td>
                                    <td class="fw-bold"><?= View::formatCurrency($cat['max_amount']) ?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editCategory(<?= $cat['id'] ?>, '<?= View::escape($cat['name']) ?>', '<?= View::escape($cat['description']) ?>', <?= $cat['max_amount'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>/category/delete/<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category? Ensure no requests are associated.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, description, maxAmount) {
    const form = document.getElementById('categoryForm');
    const formTitle = document.getElementById('formTitle');
    const cancelBtn = document.getElementById('cancelBtn');
    
    // Change form action to edit
    form.action = `<?= BASE_URL ?>/category/edit/${id}`;
    formTitle.innerHTML = '<i class="fas fa-edit me-1"></i>Edit Category';
    
    // Fill fields
    document.getElementById('name').value = name;
    document.getElementById('description').value = description;
    document.getElementById('max_amount').value = maxAmount;
    
    cancelBtn.classList.remove('d-none');
}

function resetForm() {
    const form = document.getElementById('categoryForm');
    const formTitle = document.getElementById('formTitle');
    const cancelBtn = document.getElementById('cancelBtn');
    
    form.action = `<?= BASE_URL ?>/category/create`;
    formTitle.innerHTML = '<i class="fas fa-plus me-1"></i>Add Category';
    
    form.reset();
    cancelBtn.classList.add('d-none');
}
</script>
