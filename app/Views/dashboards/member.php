<?php
use App\Core\View;
?>
<div class="row">
    <!-- Member Info Profile Banner -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #0d6efd, #023e8a) !important;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center flex-wrap">
                    <div class="avatar-circle bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0" style="width: 80px; height: 80px; font-size: 2.5rem; font-weight: 700;">
                        <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">Welcome back, <?= View::escape($member['first_name'] . ' ' . $member['last_name']) ?>!</h2>
                        <p class="mb-0 opacity-75">Member Number: <strong><?= View::escape($member['member_number']) ?></strong> | Joined on <?= View::formatDate($member['join_date']) ?></p>
                        <span class="badge bg-light text-primary mt-2">Status: <?= View::escape($member['status']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Financial Stat Cards -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-success"><i class="fas fa-hand-holding-usd me-2"></i>My Financial Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6 border-end">
                        <span class="text-muted small">Total Paid Contributions</span>
                        <h3 class="fw-bold text-success mt-1"><?= View::formatCurrency($total_paid) ?></h3>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small">Outstanding Contributions</span>
                        <h3 class="fw-bold text-danger mt-1"><?= View::formatCurrency($outstanding['total_amount']) ?></h3>
                    </div>
                </div>
                
                <?php if ($outstanding['total_amount'] > 0): ?>
                    <div class="alert alert-warning border-0 mb-0 small" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>You have <strong><?= count($outstanding['list']) ?></strong> months of unpaid contributions. Please submit your payment of <?= View::formatCurrency($outstanding['total_amount']) ?> to the Treasurer.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success border-0 mb-0 small" role="alert">
                        <i class="fas fa-check-circle me-2"></i>Your monthly contributions are completely up-to-date. Thank you!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Links and Submissions -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold text-primary"><i class="fas fa-rocket me-2"></i>Quick Tasks</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-around">
                <a href="<?= BASE_URL ?>/welfare/create" class="btn btn-primary py-3 text-start mb-3">
                    <i class="fas fa-paper-plane me-2"></i> Submit Welfare Assistance Request
                </a>
                <a href="<?= BASE_URL ?>/contribution/my_contributions" class="btn btn-outline-success py-3 text-start">
                    <i class="fas fa-receipt me-2"></i> View Contribution Receipts
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Contribution payments -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-dark">Recent Contribution History</h5>
                <a href="<?= BASE_URL ?>/contribution/my_contributions" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Contribution Month</th>
                                <th>Amount</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contributions)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No contribution records yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($contributions as $c): ?>
                                <tr>
                                    <td><?= View::formatDate($c['payment_date'], 'M d, Y') ?></td>
                                    <td><?= date('F Y', strtotime($c['contribution_month'])) ?></td>
                                    <td class="fw-semibold text-success"><?= View::formatCurrency($c['amount']) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/contribution/receipt/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-print"></i>
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

    <!-- Recent Welfare Requests status -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-dark">My Welfare Request History</h5>
                <a href="<?= BASE_URL ?>/welfare" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Track</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No welfare requests submitted yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td class="small"><?= View::formatDate($r['created_at']) ?></td>
                                    <td><span class="fw-medium text-truncate d-inline-block" style="max-width: 150px;"><?= View::escape($r['title']) ?></span></td>
                                    <td><?= View::formatCurrency($r['requested_amount']) ?></td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'bg-secondary';
                                            if ($r['status'] === 'Pending') $badgeClass = 'badge-pending';
                                            elseif ($r['status'] === 'Under Review') $badgeClass = 'badge-review';
                                            elseif ($r['status'] === 'Approved') $badgeClass = 'badge-approved';
                                            elseif ($r['status'] === 'Rejected') $badgeClass = 'badge-rejected';
                                            elseif ($r['status'] === 'Completed') $badgeClass = 'badge-completed';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= View::escape($r['status']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/welfare/show/<?= $r['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i>
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
