<?php
use App\Core\View;
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-coins me-2"></i>Record Member Contribution</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/contribution/record" method="POST" enctype="multipart/form-data">
                    <?= View::csrfField() ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="member_id" class="form-label">Select Member</label>
                            <select class="form-select border-primary" id="member_id" name="member_id" required>
                                <option value="">-- Select Active Member --</option>
                                <?php foreach ($members as $m): ?>
                                    <option value="<?= $m['id'] ?>">
                                        <?= View::escape($m['member_number']) ?> - <?= View::escape($m['first_name'] . ' ' . $m['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="amount" class="form-label">Contribution Amount ($)</label>
                            <input type="number" class="form-control" id="amount" name="amount" required step="0.01" value="50.00">
                            <small class="text-muted">Default monthly rate is $50.00</small>
                        </div>

                        <div class="col-md-6">
                            <label for="contribution_month" class="form-label">Contribution Month</label>
                            <input type="month" class="form-control" id="contribution_month" name="contribution_month" required value="<?= date('Y-m') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Mobile Money">Mobile Money</option>
                                <option value="Cash">Cash</option>
                                <option value="Check">Check</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="reference_number" class="form-label">Transaction Reference Number</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" required placeholder="e.g. TXN9827361">
                        </div>

                        <div class="col-12">
                            <label for="receipt" class="form-label">Upload Proof of Payment / Invoice Receipt <span class="text-muted">(Optional)</span></label>
                            <input type="file" class="form-control" id="receipt" name="receipt" accept="image/*,application/pdf">
                            <small class="text-muted">Supported formats: JPG, PNG, PDF. Max size: 5MB</small>
                        </div>
                    </div>

                    <div class="mt-4 border-top pt-3 d-flex justify-content-end">
                        <a href="<?= BASE_URL ?>/contribution" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
