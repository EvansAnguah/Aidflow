<?php
use App\Core\View;
?>
<div class="row">
    <!-- Stat Cards -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white bg-warning">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">New Pending Requests</h6>
                    <h3 class="mb-0 fw-bold"><?= $requests['pending'] ?></h3>
                </div>
                <i class="fas fa-clock fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white bg-primary">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Requests Under Review</h6>
                    <h3 class="mb-0 fw-bold"><?= $requests['review'] ?></h3>
                </div>
                <i class="fas fa-search fa-3x opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white bg-success">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Total Active Members</h6>
                    <h3 class="mb-0 fw-bold"><?= $member_counts['active'] ?></h3>
                </div>
                <i class="fas fa-user-check fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Pending Requests List -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-warning"><i class="fas fa-exclamation-circle me-2"></i>New Requests Awaiting Action</h5>
                <a href="<?= BASE_URL ?>/welfare?status=Pending" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Member</th>
                                <th>Welfare request</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_requests)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No new pending requests.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($pending_requests as $req): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?= View::escape($req['first_name'] . ' ' . $req['last_name']) ?></span>
                                        <small class="text-muted d-block"><?= View::escape($req['member_number']) ?></small>
                                    </td>
                                    <td><?= View::escape($req['title']) ?></td>
                                    <td class="fw-semibold text-primary"><?= View::formatCurrency($req['requested_amount']) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/welfare/view/<?= $req['id'] ?>" class="btn btn-sm btn-primary">
                                            Verify
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Under Review Requests List -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-primary"><i class="fas fa-search me-2"></i>Requests Under Review</h5>
                <a href="<?= BASE_URL ?>/welfare?status=Under+Review" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Member</th>
                                <th>Welfare request</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($review_requests)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No requests currently in review.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($review_requests as $req): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?= View::escape($req['first_name'] . ' ' . $req['last_name']) ?></span>
                                        <small class="text-muted d-block"><?= View::escape($req['member_number']) ?></small>
                                    </td>
                                    <td><?= View::escape($req['title']) ?></td>
                                    <td class="fw-semibold text-primary"><?= View::formatCurrency($req['requested_amount']) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/welfare/view/<?= $req['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            Track
                                        </a>
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
