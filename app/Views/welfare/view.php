<?php
use App\Core\View;
use App\Core\Session;

$role = Session::get('role');
$userId = Session::get('user_id');
?>
<div class="row">
    <!-- Main Request Details Panel -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Assistance Details: Request #<?= $request['id'] ?></h5>
                <?php 
                    $badgeClass = 'bg-secondary';
                    if ($request['status'] === 'Pending') $badgeClass = 'badge-pending';
                    elseif ($request['status'] === 'Under Review') $badgeClass = 'badge-review';
                    elseif ($request['status'] === 'Approved') $badgeClass = 'badge-approved';
                    elseif ($request['status'] === 'Rejected') $badgeClass = 'badge-rejected';
                    elseif ($request['status'] === 'Completed') $badgeClass = 'badge-completed';
                ?>
                <span class="badge <?= $badgeClass ?> p-2"><?= View::escape($request['status']) ?></span>
            </div>
            
            <div class="card-body">
                <h3 class="fw-bold text-dark mb-3"><?= View::escape($request['title']) ?></h3>
                
                <div class="row g-3 mb-4 bg-light p-3 rounded">
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Member Submitting</span>
                        <strong><?= View::escape($request['first_name'] . ' ' . $request['last_name']) ?></strong> (<?= View::escape($request['member_number']) ?>)
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Support Category</span>
                        <strong><?= View::escape($request['category_name']) ?></strong> (Max: <?= View::formatCurrency($request['category_max']) ?>)
                    </div>
                    
                    <div class="col-sm-6 border-top pt-2 mt-2">
                        <span class="text-muted d-block small">Requested Amount</span>
                        <strong class="text-primary fs-5"><?= View::formatCurrency($request['requested_amount']) ?></strong>
                    </div>
                    <div class="col-sm-6 border-top pt-2 mt-2">
                        <span class="text-muted d-block small">Date Submitted</span>
                        <strong><?= View::formatDateTime($request['created_at']) ?></strong>
                    </div>
                </div>

                <h6 class="fw-semibold text-dark">Reason & Explanation:</h6>
                <div class="text-muted mb-4" style="line-height: 1.7; white-space: pre-wrap;"><?= View::escape($request['description']) ?></div>

                <?php if (!empty($request['supporting_document'])): ?>
                    <div class="card border border-primary p-3 bg-light bg-opacity-25 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-file-invoice fa-2x text-primary me-3"></i>
                            <div>
                                <strong class="text-dark">Supporting Documentation</strong>
                                <small class="text-muted d-block">Uploaded Verification Attachment</small>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/<?= View::escape($request['supporting_document']) ?>" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download me-1"></i> View Document
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning border-0 small mb-0"><i class="fas fa-exclamation-triangle"></i> No supporting documentation uploaded.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comments & Activity Timeline -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-comments me-2"></i>Workflow Timeline & Comments</h5>
            </div>
            <div class="card-body">
                <div class="timeline-log mb-4">
                    <?php if (empty($history)): ?>
                        <p class="text-center text-muted py-3 small">No workflow updates logged yet.</p>
                    <?php endif; ?>
                    <?php foreach ($history as $h): ?>
                        <div class="border-start border-3 border-primary ps-3 pb-3 mb-3 position-relative">
                            <div class="small text-muted mb-1"><?= View::formatDateTime($h['created_at']) ?></div>
                            <strong class="text-dark"><?= View::escape($h['username']) ?></strong>
                            <span class="badge bg-secondary ms-1 small"><?= View::escape($h['role']) ?></span>
                            <span class="badge bg-primary ms-1"><?= View::escape($h['action']) ?></span>
                            <?php if (!empty($h['comments'])): ?>
                                <div class="bg-light p-2 rounded mt-2 text-dark small"><?= View::escape($h['comments']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Add Comment Form -->
                <form action="<?= BASE_URL ?>/welfare/comment/<?= $request['id'] ?>" method="POST">
                    <?= View::csrfField() ?>
                    <div class="mb-3">
                        <label for="comments" class="form-label small fw-semibold">Add a comment / message:</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3" required placeholder="Type comments here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm px-3 float-end">Post Comment</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Workflow Control Panel (Admins & Welfare Officers only) -->
    <div class="col-lg-4">
        <?php if (in_array($role, ['Admin', 'Welfare Officer']) && $request['status'] !== 'Completed' && $request['status'] !== 'Rejected'): ?>
            <div class="card border-0 shadow-sm bg-light mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-semibold text-primary"><i class="fas fa-tasks me-2"></i>Workflow Controls</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/welfare/workflow/<?= $request['id'] ?>" method="POST">
                        <?= View::csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Select Action:</label>
                            <select class="form-select border-primary" name="action" required id="workflowAction">
                                <option value="">-- Choose Action --</option>
                                
                                <!-- Welfare Officer options -->
                                <?php if ($role === 'Welfare Officer'): ?>
                                    <?php if ($request['status'] === 'Pending'): ?>
                                        <option value="Review">Mark as 'Under Review'</option>
                                    <?php endif; ?>
                                    <?php if ($request['status'] === 'Under Review' || $request['status'] === 'Pending'): ?>
                                        <option value="Recommend">Log Recommendation</option>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Admin options -->
                                <?php if ($role === 'Admin'): ?>
                                    <option value="Approve">Approve Assistance Request</option>
                                    <option value="Reject">Reject Assistance Request</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="workflowComments" class="form-label small fw-semibold">Action Comments / Reason:</label>
                            <textarea class="form-control" id="workflowComments" name="comments" rows="3" placeholder="Enter reason, approval recommendations, or rejection cause..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">Execute Action</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Disbursement Details (Completed only) -->
        <?php if ($request['status'] === 'Completed'): ?>
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 border border-success p-3 text-center mb-4">
                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                <h5 class="fw-bold text-success">Funds Disbursed</h5>
                <p class="text-muted small mb-0">This request is fully paid and closed. The Treasurer has issued the payments.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
