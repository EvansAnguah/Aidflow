<?php
use App\Core\View;
?>
<div class="row justify-content-center align-items-center" style="min-height: 85vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg mt-5" style="border-radius: 24px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold font-heading"><span class="gradient-text"><i class="fa-solid fa-lock-open me-2"></i>Reset Password</span></h2>
                    <p class="text-muted small fw-semibold uppercase tracking-wider">Enter a new secure password</p>
                </div>

                <form action="<?= BASE_URL ?>/auth/reset_password/<?= View::escape($token) ?>" method="POST">
                    <?= View::csrfField() ?>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Min 8 characters">
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Repeat password">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Save New Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
