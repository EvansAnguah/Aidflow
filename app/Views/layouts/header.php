<?php
use App\Core\Session;
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? View::escape($title) . ' - ' : '' ?><?= APP_NAME ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Iconset -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<?php if (Session::isLoggedIn()): ?>
<div class="d-flex">
    <!-- Main Content Wrapper (matches sidebar offset) -->
    <div class="main-wrapper">
        <!-- Top Navigation Bar -->
        <nav class="main-navbar">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-semibold text-truncate d-none d-md-block"><?= isset($title) ? View::escape($title) : 'Dashboard' ?></h4>
            </div>
            
            <div class="d-flex align-items-center">
                <!-- Theme Toggler -->
                <button class="theme-toggle-btn me-3" id="themeToggle" title="Toggle Light/Dark Theme">
                    <i class="fas fa-moon"></i>
                </button>

                <!-- Notifications Bell Dropdown -->
                <div class="dropdown me-3">
                    <button class="notification-bell btn" type="button" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger d-none" id="notificationBadge">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notificationBell" id="notificationList" style="width: 300px; max-height: 400px; overflow-y: auto;">
                        <li><a class="dropdown-item text-center text-muted" href="#">Loading...</a></li>
                    </ul>
                </div>

                <!-- User Account Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center border-0 px-2" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg me-2"></i>
                        <span class="fw-medium"><?= View::escape(Session::get('username')) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userMenu">
                        <?php if (Session::get('role') === 'Member'): ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/member/show"><i class="fas fa-id-card fa-fw me-2"></i>My Profile</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout"><i class="fas fa-sign-out-alt fa-fw me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Flash Messages Block -->
        <div class="container-fluid mt-4 px-4">
            <?php if ($flashSuccess = Session::getFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= View::escape($flashSuccess) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($flashError = Session::getFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= View::escape($flashError) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Main Content Inner Body -->
        <div class="content-body">
<?php else: ?>
    <!-- Layout for Guest/Auth pages -->
    <div class="container mt-4">
        <?php if ($flashSuccess = Session::getFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= View::escape($flashSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($flashError = Session::getFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= View::escape($flashError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
