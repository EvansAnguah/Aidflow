<?php
use App\Core\View;
?>
<div class="row justify-content-center align-items-center" style="min-height: 85vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg mt-5" style="border-radius: 24px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold font-heading"><span class="gradient-text"><i class="fa-solid fa-layer-group me-2"></i>AidFlow</span></h2>
                    <p class="text-muted small fw-semibold uppercase tracking-wider">Welfare Management Portal</p>
                </div>

                <form action="<?= BASE_URL ?>/auth/login" method="POST">
                    <?= View::csrfField() ?>

                    <div class="mb-3">
                        <label for="login" class="form-label">Username or Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="login" name="login" required placeholder="Enter username or email">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label">Password</label>
                            <a href="<?= BASE_URL ?>/auth/forgot_password" class="text-decoration-none small">Forgot Password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" required placeholder="Enter password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Sign In</button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Don't have a member account? <a href="<?= BASE_URL ?>/auth/register" class="text-decoration-none fw-semibold">Register here</a></p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted small">
            <p>Demo accounts (Password: <strong>Password123</strong>)<br>
            Admin: <code>admin</code> | Treasurer: <code>treasurer</code> | Officer: <code>welfare_officer</code></p>
        </div>
    </div>
</div>
