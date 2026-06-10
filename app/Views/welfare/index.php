<?php
use App\Core\View;
use App\Core\Session;

$role = Session::get('role');
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Welfare Assistance Requests</h5>
        <?php if ($role === 'Member'): ?>
            <a href="<?= BASE_URL ?>/welfare/create" class="btn btn-primary btn-sm">
                <i class="fas fa-paper-plane me-1"></i> Submit Request
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if ($role !== 'Member'): ?>
            <!-- Filter Form for Staff -->
            <form action="<?= BASE_URL ?>/welfare" method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" value="<?= View::escape($search) ?>" placeholder="Search by name, number, or title...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Under Review" <?= $status === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                        <option value="Approved" <?= $status === 'Approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="Rejected" <?= $status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (int)$category_filter === $cat['id'] ? 'selected' : '' ?>><?= View::escape($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="<?= BASE_URL ?>/welfare" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Date Submitted</th>
                        <?php if ($role !== 'Member'): ?>
                            <th>Member</th>
                        <?php endif; ?>
                        <th>Welfare Category</th>
                        <th>Request Title</th>
                        <th>Requested Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= View::formatDate($req['created_at']) ?></td>
                            <?php if ($role !== 'Member'): ?>
                                <td>
                                    <span class="fw-semibold"><?= View::escape($req['first_name'] . ' ' . $req['last_name']) ?></span>
                                    <small class="text-muted d-block"><?= View::escape($req['member_number']) ?></small>
                                </td>
                            <?php endif; ?>
                            <td class="fw-medium text-primary"><?= View::escape($req['category_name']) ?></td>
                            <td><?= View::escape($req['title']) ?></td>
                            <td class="fw-bold"><?= View::formatCurrency($req['requested_amount']) ?></td>
                            <td>
                                <?php 
                                    $badgeClass = 'bg-secondary';
                                    if ($req['status'] === 'Pending') $badgeClass = 'badge-pending';
                                    elseif ($req['status'] === 'Under Review') $badgeClass = 'badge-review';
                                    elseif ($req['status'] === 'Approved') $badgeClass = 'badge-approved';
                                    elseif ($req['status'] === 'Rejected') $badgeClass = 'badge-rejected';
                                    elseif ($req['status'] === 'Completed') $badgeClass = 'badge-completed';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= View::escape($req['status']) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/welfare/show/<?= $req['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-search-plus me-1"></i> Track & Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
