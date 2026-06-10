<?php
use App\Core\View;
?>
<div class="row mb-4">
    <!-- Stat Cards -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white bg-primary">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Available Welfare Fund</h6>
                    <h3 class="mb-0 fw-bold"><?= View::formatCurrency($financial['available_balance']) ?></h3>
                </div>
                <i class="fas fa-wallet fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white bg-success">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Total Contributions</h6>
                    <h3 class="mb-0 fw-bold"><?= View::formatCurrency($financial['total_contributions']) ?></h3>
                </div>
                <i class="fas fa-coins fa-3x opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white bg-danger">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Total Disbursements</h6>
                    <h3 class="mb-0 fw-bold"><?= View::formatCurrency($financial['total_disbursements']) ?></h3>
                </div>
                <i class="fas fa-hand-holding-usd fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Action Panel & Links -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="<?= BASE_URL ?>/contribution/record" class="btn btn-primary py-2 text-start">
                        <i class="fas fa-plus-circle me-2"></i> Record Member Payment
                    </a>
                    <a href="<?= BASE_URL ?>/disbursement/record" class="btn btn-danger py-2 text-start">
                        <i class="fas fa-wallet me-2"></i> Process Disbursement
                    </a>
                    <a href="<?= BASE_URL ?>/report" class="btn btn-outline-secondary py-2 text-start">
                        <i class="fas fa-print me-2"></i> Generate Financial Reports
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-dark">
                <h5 class="mb-0 fw-semibold">Welfare Request Pipeline</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <span class="text-muted"><i class="fas fa-clock text-warning me-2"></i>Pending Officer Review</span>
                    <span class="badge bg-warning"><?= $requests['pending'] + $requests['review'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <span class="text-muted"><i class="fas fa-check text-success me-2"></i>Approved (Awaiting Pay)</span>
                    <span class="badge bg-success"><?= $requests['approved'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted"><i class="fas fa-check-double text-primary me-2"></i>Disbursed & Closed</span>
                    <span class="badge bg-primary"><?= $requests['completed'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Contributions recorded -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Recent Contributions Verified</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Ref</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_contributions)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No payments logged recently.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($recent_contributions as $c): ?>
                                <tr>
                                    <td><?= View::formatDate($c['payment_date'], 'M d, Y') ?></td>
                                    <td>
                                        <span class="fw-semibold"><?= View::escape($c['first_name'] . ' ' . $c['last_name']) ?></span>
                                        <small class="text-muted d-block"><?= View::escape($c['member_number']) ?></small>
                                    </td>
                                    <td><?= date('F Y', strtotime($c['contribution_month'])) ?></td>
                                    <td class="fw-semibold text-success"><?= View::formatCurrency($c['amount']) ?></td>
                                    <td><code class="small"><?= View::escape($c['reference_number']) ?></code></td>
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
</div>
