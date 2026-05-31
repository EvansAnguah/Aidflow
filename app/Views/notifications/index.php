<?php
use App\Core\View;
use App\Core\Session;
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold"><i class="fas fa-bell me-2"></i>Notification Centre</h5>
        <a href="<?= BASE_URL ?>/notification/readAll" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-check-double me-1"></i> Mark All as Read
        </a>
    </div>

    <div class="card-body p-0">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-bell-slash fa-4x mb-3 opacity-25"></i>
                <h5 class="fw-normal">No notifications yet.</h5>
                <p class="small">System notifications about your account and welfare requests will appear here.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notif): ?>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start px-4 py-3 
                        <?= $notif['status'] === 'Unread' ? 'bg-light fw-semibold border-start border-4 border-primary' : '' ?>">
                        <div class="me-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark"><?= View::escape($notif['title']) ?></strong>
                                <?php if ($notif['status'] === 'Unread'): ?>
                                    <span class="badge bg-primary ms-2 flex-shrink-0">New</span>
                                <?php endif; ?>
                            </div>
                            <p class="mb-1 text-muted small fw-normal"><?= View::escape($notif['message']) ?></p>
                            <small class="text-muted opacity-75">
                                <i class="fas fa-clock me-1"></i><?= View::formatDateTime($notif['created_at']) ?>
                            </small>
                        </div>
                        <?php if ($notif['status'] === 'Unread'): ?>
                            <button class="btn btn-sm btn-outline-primary flex-shrink-0 align-self-center" 
                                    onclick="markRead(<?= $notif['id'] ?>, event); this.closest('.list-group-item').classList.remove('bg-light', 'fw-semibold', 'border-start', 'border-4', 'border-primary'); this.remove();"
                                    title="Mark as Read">
                                <i class="fas fa-check"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
