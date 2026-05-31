<?php
use App\Core\View;
use App\Core\Session;

$role = Session::get('role');
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Disbursement Ledger</h5>
        <?php if ($role === 'Treasurer'): ?>
            <a href="<?= BASE_URL ?>/disbursement/record" class="btn btn-primary btn-sm">
                <i class="fas fa-wallet me-1"></i> Disburse Funds
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <!-- Filter Form -->
        <form action="<?= BASE_URL ?>/disbursement" method="GET" class="row g-3 mb-4">
            <div class="col-md-8">
                <input type="text" class="form-control" name="search" value="<?= View::escape($search) ?>" placeholder="Search by member name, number, or reference...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?= BASE_URL ?>/disbursement" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Disbursed Date</th>
                        <th>Member Number</th>
                        <th>Member Name</th>
                        <th>Welfare Request</th>
                        <th>Amount Disbursed</th>
                        <th>Payment Method</th>
                        <th>Reference Code</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($disbursements as $d): ?>
                        <tr>
                            <td><?= View::formatDate($d['disbursed_at']) ?></td>
                            <td class="fw-semibold"><?= View::escape($d['member_number']) ?></td>
                            <td><?= View::escape($d['first_name'] . ' ' . $d['last_name']) ?></td>
                            <td><?= View::escape($d['request_title']) ?></td>
                            <td class="fw-bold text-danger"><?= View::formatCurrency($d['amount']) ?></td>
                            <td><?= View::escape($d['payment_method']) ?></td>
                            <td><code class="small"><?= View::escape($d['reference_number']) ?></code></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/disbursement/receipt/<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank" title="Print Receipt">
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
