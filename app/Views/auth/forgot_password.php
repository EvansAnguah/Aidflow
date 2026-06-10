<?php
use App\Core\View;
?>
<div class="row justify-content-center align-items-center" style="min-height: 85vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg mt-5" style="border-radius: 24px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold font-heading"><span class="gradient-text"><i class="fa-solid fa-key me-2"></i>Forgot Password</span></h2>
                    <p class="text-muted small fw-semibold uppercase tracking-wider">Enter your email for reset instructions</p>
                </div>

                <form action="<?= BASE_URL ?>/auth/forgot_password" method="POST">
                    <?= View::csrfField() ?>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" required placeholder="name@domain.com">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Send Reset Link</button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0"><a href="<?= BASE_URL ?>/auth/login" class="text-decoration-none fw-semibold"><i class="fas fa-arrow-left me-2"></i>Back to Sign In</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
