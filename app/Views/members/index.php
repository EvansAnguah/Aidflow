<?php
use App\Core\View;
use App\Core\Session;

$role = Session::get('role');
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">All Members</h5>
    </div>
    
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form action="<?= BASE_URL ?>/member" method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" name="search" value="<?= View::escape($search) ?>" placeholder="Search by name, member number, or phone...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?= BASE_URL ?>/member" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Member Number</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Role / Account</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $m): ?>
                        <tr>
                            <td class="fw-semibold"><?= View::escape($m['member_number']) ?></td>
                            <td>
                                <span class="fw-medium"><?= View::escape($m['first_name'] . ' ' . $m['last_name']) ?></span>
                                <small class="text-muted d-block"><?= View::escape($m['email']) ?></small>
                            </td>
                            <td><?= View::escape($m['phone']) ?></td>
                            <td><?= View::formatDate($m['join_date']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= View::escape($m['role']) ?></span>
                            </td>
                            <td>
                                <?php if ($m['user_status'] === 'Pending'): ?>
                                    <span class="badge badge-pending">Pending Approval</span>
                                <?php else: ?>
                                    <span class="badge <?= $m['status'] === 'Active' ? 'bg-success' : 'bg-danger' ?>"><?= View::escape($m['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="<?= BASE_URL ?>/member/view/<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary" title="View Profile">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($role === 'Admin'): ?>
                                        <a href="<?= BASE_URL ?>/member/edit/<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit Profile">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <?php if ($m['user_status'] === 'Pending'): ?>
                                            <button type="button" class="btn btn-sm btn-success" onclick="updateUserStatus(<?= $m['user_id'] ?>, 'Active')" title="Approve Member">
                                                <i class="fas fa-check-circle"></i> Approve
                                            </button>
                                        <?php elseif ($m['status'] === 'Active'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="updateUserStatus(<?= $m['user_id'] ?>, 'Inactive')" title="Deactivate Member">
                                                <i class="fas fa-user-slash"></i> Disable
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="updateUserStatus(<?= $m['user_id'] ?>, 'Active')" title="Activate Member">
                                                <i class="fas fa-user-check"></i> Enable
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function updateUserStatus(userId, status) {
    if (confirm(`Are you sure you want to change this user status to ${status}?`)) {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('status', status);
        formData.append('csrf_token', '<?= Session::getCSRFToken() ?>');

        fetch('<?= BASE_URL ?>/member/updateStatus', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error("Error: ", err));
    }
}
</script>
