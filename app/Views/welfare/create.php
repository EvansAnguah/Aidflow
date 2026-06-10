<?php
use App\Core\View;
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-paper-plane me-2"></i>Submit Welfare Assistance Request</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/welfare/create" method="POST" enctype="multipart/form-data">
                    <?= View::csrfField() ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Support Category</label>
                            <select class="form-select border-primary" id="category_id" name="category_id" required onchange="updateMaxLimit()">
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" data-max="<?= $cat['max_amount'] ?>">
                                        <?= View::escape($cat['name']) ?> (Max: <?= View::formatCurrency($cat['max_amount']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="requested_amount" class="form-label">Requested Amount ($)</label>
                            <input type="number" class="form-control" id="requested_amount" name="requested_amount" required step="0.01" min="1" placeholder="Enter amount">
                            <div class="form-text text-danger d-none" id="limitWarning">
                                <i class="fas fa-exclamation-triangle"></i> Amount exceeds the category maximum limit.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="title" class="form-label">Assistance Request Title</label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="e.g. Funeral expenses for father, Medical surgery aid, etc.">
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Detailed Explanation / Reason</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Please explain the situation and details of support needed..."></textarea>
                        </div>

                        <div class="col-12">
                            <label for="supporting_document" class="form-label">Upload Supporting Documents <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="supporting_document" name="supporting_document" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload hospital bills, death certificate, school invoice, or other proofs. Supported: PDF, DOC, DOCX, JPG, PNG. Max: 5MB</small>
                        </div>
                    </div>

                    <div class="mt-4 border-top pt-3 d-flex justify-content-end">
                        <a href="<?= BASE_URL ?>/welfare" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateMaxLimit() {
    const select = document.getElementById('category_id');
    const selectedOption = select.options[select.selectedIndex];
    const amountInput = document.getElementById('requested_amount');
    const warning = document.getElementById('limitWarning');
    const submitBtn = document.getElementById('submitBtn');
    
    if (selectedOption.value) {
        const maxVal = parseFloat(selectedOption.getAttribute('data-max'));
        amountInput.max = maxVal;
        
        // Dynamic check
        amountInput.addEventListener('input', checkLimit);
    }
}

function checkLimit() {
    const select = document.getElementById('category_id');
    const selectedOption = select.options[select.selectedIndex];
    const amountInput = document.getElementById('requested_amount');
    const warning = document.getElementById('limitWarning');
    const submitBtn = document.getElementById('submitBtn');

    if (selectedOption.value && amountInput.value) {
        const maxVal = parseFloat(selectedOption.getAttribute('data-max'));
        const inputVal = parseFloat(amountInput.value);

        if (inputVal > maxVal) {
            warning.classList.remove('d-none');
            submitBtn.disabled = true;
        } else {
            warning.classList.add('d-none');
            submitBtn.disabled = false;
        }
    }
}
</script>
