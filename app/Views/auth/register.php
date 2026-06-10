<?php
use App\Core\View;
?>
<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-8">
        <div class="card border-0 shadow-lg" style="border-radius: 24px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold font-heading"><span class="gradient-text"><i class="fa-solid fa-layer-group me-2"></i>Member Registration</span></h2>
                    <p class="text-muted small fw-semibold uppercase tracking-wider">Create your membership account on AidFlow</p>
                </div>

                <form action="<?= BASE_URL ?>/auth/register" method="POST">
                    <?= View::csrfField() ?>

                    <div class="row">
                        <!-- Account details -->
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required value="<?= isset($post['username']) ? View::escape($post['username']) : '' ?>" placeholder="Pick a unique username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= isset($post['email']) ? View::escape($post['email']) : '' ?>" placeholder="name@domain.com">
                        </div>

                        <!-- Personal info -->
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required value="<?= isset($post['first_name']) ? View::escape($post['first_name']) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required value="<?= isset($post['last_name']) ? View::escape($post['last_name']) : '' ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required value="<?= isset($post['phone']) ? View::escape($post['phone']) : '' ?>" placeholder="+123456789">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required value="<?= isset($post['date_of_birth']) ? View::escape($post['date_of_birth']) : '' ?>">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Residential Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required placeholder="Street address, City, Country"><?= isset($post['address']) ? View::escape($post['address']) : '' ?></textarea>
                        </div>

                        <!-- Passwords -->
                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Choose a strong password">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Re-enter password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Submit Registration</button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Already have an account? <a href="<?= BASE_URL ?>/auth/login" class="text-decoration-none fw-semibold">Sign In here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
