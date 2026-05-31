<?php
use App\Core\View;
use App\Core\Session;

$role = Session::get('role');
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-user-edit me-2"></i>Edit Member Details</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/member/edit/<?= $member['id'] ?>" method="POST">
                    <?= View::csrfField() ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required value="<?= View::escape($member['first_name']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required value="<?= View::escape($member['last_name']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required value="<?= View::escape($member['phone']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required value="<?= View::escape($member['date_of_birth']) ?>">
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Residential Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?= View::escape($member['address']) ?></textarea>
                        </div>

                        <?php if ($role === 'Admin'): ?>
                            <div class="col-md-6 border-top pt-3">
                                <label for="status" class="form-label">Membership Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Active" <?= $member['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= $member['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 border-top pt-3 d-flex justify-content-end">
                        <a href="<?= BASE_URL ?>/member/view/<?= $member['id'] ?>" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
