<?php
use App\Core\View;
?>
<div class="row">
    <!-- Outstanding contributions details -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white text-danger">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-exclamation-triangle me-2"></i>Outstanding Fees</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3">
                    <span class="text-muted small d-block">Total Unpaid Balance</span>
                    <h2 class="fw-bold text-danger mt-1"><?= View::formatCurrency($outstanding['total_amount']) ?></h2>
                    <span class="badge bg-secondary">Monthly Rate: <?= View::formatCurrency($outstanding['monthly_fee']) ?></span>
                </div>

                <hr>

                <h6 class="fw-semibold text-dark mb-3">Unpaid Month Breakdown:</h6>
                <?php if (empty($outstanding['list'])): ?>
                    <p class="text-success small mb-0"><i class="fas fa-check-circle me-1"></i>You are fully paid! No outstanding balances.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush border-top border-bottom small">
                        <?php foreach ($outstanding['list'] as $o): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><?= date('F Y', strtotime($o['month'])) ?></span>
                                <span class="fw-semibold text-danger"><?= View::formatCurrency($o['amount']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="alert alert-light border mt-3 mb-0 small text-muted">
                        <i class="fas fa-info-circle me-1"></i>Please submit your payments to the Treasurer office to avoid membership suspension.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Payments Ledger -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">My Contribution History</h5>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Date Paid</th>
                                <th>Contribution Month</th>
                                <th>Amount Paid</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th class="text-end">Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contributions as $c): ?>
                                <tr>
                                    <td><?= View::formatDate($c['payment_date'], 'M d, Y') ?></td>
                                    <td class="fw-semibold text-primary"><?= date('F Y', strtotime($c['contribution_month'])) ?></td>
                                    <td class="fw-bold text-success"><?= View::formatCurrency($c['amount']) ?></td>
                                    <td><?= View::escape($c['payment_method']) ?></td>
                                    <td><code class="small"><?= View::escape($c['reference_number']) ?></code></td>
                                    <td class="text-end">
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
