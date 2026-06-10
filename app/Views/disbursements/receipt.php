<?php
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disbursement Voucher - <?= View::escape($disbursement['reference_number']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background-color: #f8f9fa; }
        .receipt-container { max-width: 650px; margin: 40px auto; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border-top: 6px solid #ef4444; }
        .receipt-header { border-bottom: 2px dashed #dee2e6; padding-bottom: 20px; margin-bottom: 25px; }
        .receipt-brand { font-size: 1.8rem; font-weight: 700; color: #ef4444; }
        .receipt-table th { font-weight: 600; color: #6c757d; }
        .receipt-footer { border-top: 1px solid #dee2e6; padding-top: 20px; margin-top: 30px; font-size: 0.85rem; color: #6c757d; }
        @media print {
            body { background-color: #ffffff; }
            .receipt-container { box-shadow: none; margin: 0 auto; padding: 15px; border-top: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="receipt-container">
        <!-- Print toolbar -->
        <div class="d-flex justify-content-between mb-4 no-print">
            <a href="#" class="btn btn-outline-secondary" onclick="window.close();"><i class="fas fa-times"></i> Close Window</a>
            <button class="btn btn-primary" onclick="window.print();"><i class="fas fa-print"></i> Print Voucher</button>
        </div>

        <div class="receipt-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <span class="receipt-brand"><i class="fas fa-hand-holding-heart me-2"></i>AidFlow</span>
                <p class="text-muted mb-0 small">Official Disbursement Voucher</p>
            </div>
            <div class="text-md-end mt-2 mt-md-0">
                <span class="text-uppercase fw-semibold d-block">Voucher Number</span>
                <strong class="text-danger"><?= View::escape($disbursement['reference_number']) ?></strong>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <span class="text-muted d-block small text-uppercase">Payment Disbursed To</span>
                <strong class="text-dark d-block"><?= View::escape($disbursement['first_name'] . ' ' . $disbursement['last_name']) ?></strong>
                <span class="small text-muted">Member ID: <?= View::escape($disbursement['member_number']) ?></span>
            </div>
            <div class="col-sm-6 text-sm-end">
                <span class="text-muted d-block small text-uppercase">Authorized Disbursement Agent</span>
                <strong class="text-dark d-block">AidFlow Treasury Office</strong>
                <span class="small text-muted">Disbursed by: <?= View::escape($disbursement['treasurer_name']) ?></span><br>
                <span class="small text-muted">Disbursement Date: <?= View::formatDateTime($disbursement['disbursed_at'], 'M d, Y H:i A') ?></span>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered receipt-table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Welfare Category</th>
                        <th class="text-end">Amount Released</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong class="text-dark"><?= View::escape($disbursement['request_title']) ?></strong>
                            <p class="mb-0 text-muted small">Approved welfare assistance disbursement fund.</p>
                        </td>
                        <td class="text-center fw-medium"><?= View::escape($disbursement['category_name']) ?></td>
                        <td class="text-end fw-bold text-danger"><?= View::formatCurrency($disbursement['amount']) ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end fw-semibold">Grand Total Released:</td>
                        <td class="text-end fw-bold text-danger" style="font-size: 1.2rem;"><?= View::formatCurrency($disbursement['amount']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-light p-3 rounded mb-3">
                    <span class="text-muted d-block small text-uppercase">Payment Release Channel</span>
                    <strong><?= View::escape($disbursement['payment_method']) ?></strong>
                </div>
                
                <?php if (!empty($disbursement['notes'])): ?>
                    <div class="bg-light p-3 rounded">
                        <span class="text-muted d-block small text-uppercase">Treasury Notes</span>
                        <p class="mb-0 small"><?= nl2br(View::escape($disbursement['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="receipt-footer text-center">
            <p class="mb-1">This voucher certifies that welfare funds were released to the designated beneficiary.</p>
            <p class="mb-0 small">This is a system-generated document. For verification queries, contact treasurer@aidflow.org</p>
        </div>
    </div>
</div>

<script>
window.onload = function() {
    // window.print();
}
</script>
</body>
</html>
