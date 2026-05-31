<?php
use App\Core\View;
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Contribution Payment Ledger</h5>
        <a href="<?= BASE_URL ?>/contribution/record" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Record Payment
        </a>
    </div>

    <div class="card-body">
        <!-- Filter Form -->
        <form action="<?= BASE_URL ?>/contribution" method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <input type="text" class="form-control" name="search" value="<?= View::escape($search) ?>" placeholder="Search by name, number, or reference...">
            </div>
            <div class="col-md-3">
                <input type="month" class="form-control" name="month" value="<?= View::escape($month) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?= BASE_URL ?>/contribution" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Date Paid</th>
                        <th>Member Number</th>
                        <th>Member Name</th>
                        <th>Contribution Month</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference Code</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contributions as $c): ?>
                        <tr>
                            <td><?= View::formatDate($c['payment_date'], 'M d, Y') ?></td>
                            <td class="fw-semibold"><?= View::escape($c['member_number']) ?></td>
                            <td><?= View::escape($c['first_name'] . ' ' . $c['last_name']) ?></td>
                            <td class="fw-medium text-primary"><?= date('F Y', strtotime($c['contribution_month'])) ?></td>
                            <td class="fw-semibold text-success"><?= View::formatCurrency($c['amount']) ?></td>
                            <td><?= View::escape($c['payment_method']) ?></td>
                            <td><code class="small"><?= View::escape($c['reference_number']) ?></code></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/contribution/receipt/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank" title="Print Receipt">
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
