<?php
use App\Core\View;
use App\Core\Session;

$role = Session::get('role');
$userId = Session::get('user_id');
?>
<div class="row">
    <div class="col-md-4">
        <!-- Profile Avatar Card -->
        <div class="card border-0 shadow-sm text-center p-4 mb-4">
            <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; font-size: 3rem; font-weight: 700;">
                <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
            </div>
            <h4 class="fw-bold mb-1"><?= View::escape($member['first_name'] . ' ' . $member['last_name']) ?></h4>
            <p class="text-muted small mb-3"><?= View::escape($member['member_number']) ?></p>
            <div>
                <?php if ($member['user_status'] === 'Pending'): ?>
                    <span class="badge badge-pending p-2">Pending Approval</span>
                <?php else: ?>
                    <span class="badge <?= $member['status'] === 'Active' ? 'bg-success' : 'bg-danger' ?> p-2"><?= View::escape($member['status']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Profile details -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-id-card me-2"></i>Membership Information</h5>
                <?php if ($role === 'Admin' || $member['user_id'] === $userId): ?>
                    <a href="<?= BASE_URL ?>/member/edit/<?= $member['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Username</span>
                        <strong class="text-dark"><?= View::escape($member['username']) ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Email Address</span>
                        <strong class="text-dark"><?= View::escape($member['email']) ?></strong>
                    </div>
                    
                    <div class="col-sm-6 border-top pt-3">
                        <span class="text-muted d-block small">Phone Number</span>
                        <strong class="text-dark"><?= View::escape($member['phone']) ?></strong>
                    </div>
                    <div class="col-sm-6 border-top pt-3">
                        <span class="text-muted d-block small">Date of Birth</span>
                        <strong class="text-dark"><?= View::formatDate($member['date_of_birth']) ?></strong>
                    </div>

                    <div class="col-sm-6 border-top pt-3">
                        <span class="text-muted d-block small">Joining Date</span>
                        <strong class="text-dark"><?= View::formatDate($member['join_date']) ?></strong>
                    </div>
                    <div class="col-sm-6 border-top pt-3">
                        <span class="text-muted d-block small">System Role</span>
                        <strong class="text-dark"><?= View::escape($member['role']) ?></strong>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <span class="text-muted d-block small">Residential Address</span>
                        <strong class="text-dark"><?= nl2br(View::escape($member['address'])) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
