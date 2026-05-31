<?php
use App\Core\Session;
use App\Core\View;

$role = Session::get('role');
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?= BASE_URL ?>/dashboard" class="sidebar-brand">
            <i class="fa-solid fa-layer-group me-2"></i><?= APP_NAME ?>
        </a>
    </div>
    
    <ul class="sidebar-menu">
        <!-- Dashboard (All Roles) -->
        <li class="sidebar-item <?= View::activeClass('/dashboard') ?>">
            <a href="<?= BASE_URL ?>/dashboard" class="sidebar-link">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Member Management (Admins, Treasurers, Welfare Officers) -->
        <?php if (in_array($role, ['Admin', 'Treasurer', 'Welfare Officer'])): ?>
            <li class="sidebar-item <?= View::activeClass('/member') ?>">
                <a href="<?= BASE_URL ?>/member" class="sidebar-link">
                    <i class="fas fa-users"></i>
                    <span>Members</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Contributions -->
        <?php if (in_array($role, ['Admin', 'Treasurer'])): ?>
            <li class="sidebar-item <?= View::activeClass('/contribution') && !View::activeClass('/record') ?>">
                <a href="<?= BASE_URL ?>/contribution" class="sidebar-link">
                    <i class="fas fa-coins"></i>
                    <span>Contributions</span>
                </a>
            </li>
            <li class="sidebar-item <?= View::activeClass('/contribution/record') ?>">
                <a href="<?= BASE_URL ?>/contribution/record" class="sidebar-link">
                    <i class="fas fa-plus-circle"></i>
                    <span>Record Payment</span>
                </a>
            </li>
        <?php elseif ($role === 'Member'): ?>
            <li class="sidebar-item <?= View::activeClass('/contribution/my_contributions') ?>">
                <a href="<?= BASE_URL ?>/contribution/my_contributions" class="sidebar-link">
                    <i class="fas fa-coins"></i>
                    <span>My Contributions</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Welfare Requests (All Roles) -->
        <li class="sidebar-item <?= View::activeClass('/welfare') && !View::activeClass('/create') && !View::activeClass('/categories') ?>">
            <a href="<?= BASE_URL ?>/welfare" class="sidebar-link">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Welfare Requests</span>
            </a>
        </li>
        <?php if ($role === 'Member'): ?>
            <li class="sidebar-item <?= View::activeClass('/welfare/create') ?>">
                <a href="<?= BASE_URL ?>/welfare/create" class="sidebar-link">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Request</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Welfare Categories (Admin only) -->
        <?php if ($role === 'Admin'): ?>
            <li class="sidebar-item <?= View::activeClass('/category') ?>">
                <a href="<?= BASE_URL ?>/category" class="sidebar-link">
                    <i class="fas fa-tags"></i>
                    <span>Welfare Categories</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Disbursements (Admins, Treasurers) -->
        <?php if (in_array($role, ['Admin', 'Treasurer'])): ?>
            <li class="sidebar-item <?= View::activeClass('/disbursement') && !View::activeClass('/record') ?>">
                <a href="<?= BASE_URL ?>/disbursement" class="sidebar-link">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Disbursements</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($role === 'Treasurer'): ?>
            <li class="sidebar-item <?= View::activeClass('/disbursement/record') ?>">
                <a href="<?= BASE_URL ?>/disbursement/record" class="sidebar-link">
                    <i class="fas fa-wallet"></i>
                    <span>Disburse Funds</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Reports (Admins, Treasurers, Welfare Officers) -->
        <?php if (in_array($role, ['Admin', 'Treasurer', 'Welfare Officer'])): ?>
            <li class="sidebar-item <?= View::activeClass('/report') ?>">
                <a href="<?= BASE_URL ?>/report" class="sidebar-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports & Audits</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- System Settings (Admin only) -->
        <?php if ($role === 'Admin'): ?>
            <li class="sidebar-item <?= View::activeClass('/settings') ?>">
                <a href="<?= BASE_URL ?>/settings" class="sidebar-link">
                    <i class="fas fa-cogs"></i>
                    <span>System Settings</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-footer">
        <span class="small text-muted">Signed in as:<br><strong class="text-white"><?= View::escape($role) ?></strong></span>
    </div>
</aside>
