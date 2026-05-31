<?php
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::escape($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background-color: #ffffff; color: #000000; padding: 20px; }
        .report-header { border-bottom: 3px double #dee2e6; padding-bottom: 15px; margin-bottom: 25px; }
        .report-brand { font-size: 1.8rem; font-weight: 700; color: #0d6efd; }
        .table { font-size: 0.9rem; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <!-- Toolbar -->
    <div class="d-flex justify-content-between mb-4 no-print bg-light p-3 rounded">
        <a href="#" class="btn btn-outline-secondary" onclick="window.close();">Close Window</a>
        <button class="btn btn-primary" onclick="window.print();">Print Report (Save PDF)</button>
    </div>

    <!-- Header -->
    <div class="report-header d-flex justify-content-between align-items-center">
        <div>
            <span class="report-brand"><i class="fas fa-hand-holding-heart me-2"></i>AidFlow</span>
            <p class="text-muted mb-0 small">Official System Report Logs</p>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-1"><?= View::escape($title) ?></h5>
            <span class="text-muted small">Generated on: <?= date('Y-m-d H:i:s') ?></span>
            <?php if (!empty($start_date) || !empty($end_date)): ?>
                <br><span class="text-muted small">Range: <?= View::escape($start_date ?: 'Start') ?> to <?= View::escape($end_date ?: 'End') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Content -->
    <?php if ($type === 'members'): ?>
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Member Number</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Join Date</th>
                    <th>Account Role</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="fw-bold"><?= View::escape($row['member_number']) ?></td>
                        <td><?= View::escape($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= View::escape($row['email']) ?></td>
                        <td><?= View::escape($row['phone']) ?></td>
                        <td><?= View::formatDate($row['join_date']) ?></td>
                        <td><?= View::escape($row['role']) ?></td>
                        <td><?= View::escape($row['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif ($type === 'contributions'): ?>
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date Paid</th>
                    <th>Member Number</th>
                    <th>Member Name</th>
                    <th>Contribution Month</th>
                    <th>Amount Paid</th>
                    <th>Payment Method</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($data as $row): ?>
                    <?php $total += (float)$row['amount']; ?>
                    <tr>
                        <td><?= View::formatDate($row['payment_date']) ?></td>
                        <td class="fw-bold"><?= View::escape($row['member_number']) ?></td>
                        <td><?= View::escape($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= date('F Y', strtotime($row['contribution_month'])) ?></td>
                        <td class="fw-bold text-success"><?= View::formatCurrency($row['amount']) ?></td>
                        <td><?= View::escape($row['payment_method']) ?></td>
                        <td><code><?= View::escape($row['reference_number']) ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                    <td colspan="3" class="fw-bold text-success" style="font-size: 1.1rem;"><?= View::formatCurrency($total) ?></td>
                </tr>
            </tfoot>
        </table>

    <?php elseif ($type === 'requests'): ?>
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Submitted Date</th>
                    <th>Member Number</th>
                    <th>Member Name</th>
                    <th>Category</th>
                    <th>Request Title</th>
                    <th>Requested Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= View::formatDate($row['created_at']) ?></td>
                        <td class="fw-bold"><?= View::escape($row['member_number']) ?></td>
                        <td><?= View::escape($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= View::escape($row['category_name']) ?></td>
                        <td><?= View::escape($row['title']) ?></td>
                        <td class="fw-bold"><?= View::formatCurrency($row['requested_amount']) ?></td>
                        <td><?= View::escape($row['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif ($type === 'financial'): ?>
        <div class="row mb-5">
            <div class="col-md-6 mx-auto">
                <div class="card p-4 border border-2">
                    <h5 class="fw-bold text-center border-bottom pb-2 mb-3">Financial Balance Sheet Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Starting System Fund Balance:</span>
                        <strong class="text-dark"><?= View::formatCurrency($data['summary']['starting_fund']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Members Contributions (+):</span>
                        <strong class="text-success"><?= View::formatCurrency($data['summary']['total_contributions']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Welfare Disbursements (-):</span>
                        <strong class="text-danger"><?= View::formatCurrency($data['summary']['total_disbursements']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-3 fs-5">
                        <strong>Available Net Fund Balance:</strong>
                        <strong class="text-primary"><?= View::formatCurrency($data['summary']['available_balance']) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-list me-2"></i>Financial Ledger Transactions</h5>
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>Ledger Type</th>
                    <th>Reference Number</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['transactions'] as $tx): ?>
                    <tr>
                        <td><?= View::formatDateTime($tx['date']) ?></td>
                        <td>
                            <span class="badge <?= $tx['type'] === 'Contribution' ? 'bg-success' : 'bg-danger' ?>"><?= View::escape($tx['type']) ?></span>
                        </td>
                        <td><code><?= View::escape($tx['ref']) ?></code></td>
                        <td class="text-end fw-bold <?= $tx['type'] === 'Contribution' ? 'text-success' : 'text-danger' ?>">
                            <?= $tx['type'] === 'Contribution' ? '+' : '-' ?><?= View::formatCurrency($tx['amount']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
window.onload = function() {
    // window.print();
}
</script>
</body>
</html>
