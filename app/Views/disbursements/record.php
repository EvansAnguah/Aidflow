<?php
use App\Core\View;
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-wallet me-2"></i>Execute Welfare Disbursement</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/disbursement/record" method="POST" enctype="multipart/form-data">
                    <?= View::csrfField() ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="request_id" class="form-label">Select Approved Welfare Request</label>
                            <select class="form-select border-primary" id="request_id" name="request_id" required onchange="updateAmount()">
                                <option value="">-- Select Approved Request --</option>
                                <?php foreach ($approved_requests as $req): ?>
                                    <option value="<?= $req['id'] ?>" data-amount="<?= $req['requested_amount'] ?>">
                                        #<?= $req['id'] ?> - <?= View::escape($req['first_name'] . ' ' . $req['last_name']) ?>: <?= View::escape($req['title']) ?> (<?= View::formatCurrency($req['requested_amount']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="amount" class="form-label">Disbursed Amount ($)</label>
                            <input type="number" class="form-control" id="amount" name="amount" required step="0.01" readonly placeholder="Select a request first">
                        </div>

                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Mobile Money">Mobile Money</option>
                                <option value="Check">Check</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label for="reference_number" class="form-label">Transaction Reference Code</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" required placeholder="e.g. REF-DISB-783261">
                        </div>

                        <div class="col-12">
                            <label for="receipt" class="form-label">Attach Transaction Voucher / Receipt <span class="text-muted">(Optional)</span></label>
                            <input type="file" class="form-control" id="receipt" name="receipt" accept="image/*,application/pdf">
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">Disbursement Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter banking coordinates or other disbursement details..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4 border-top pt-3 d-flex justify-content-end">
                        <a href="<?= BASE_URL ?>/disbursement" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-danger px-4">Confirm Disbursement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateAmount() {
    const select = document.getElementById('request_id');
    const selectedOption = select.options[select.selectedIndex];
    const amountInput = document.getElementById('amount');
    
    if (selectedOption.value) {
        amountInput.value = parseFloat(selectedOption.getAttribute('data-amount'));
    } else {
        amountInput.value = '';
    }
}
</script>
