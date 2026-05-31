<?php
use App\Core\View;
?>
<div class="row">
    <!-- Stat Cards -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white bg-primary">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Total Members</h6>
                    <h3 class="mb-0 fw-bold"><?= $member_counts['total'] ?></h3>
                </div>
                <i class="fas fa-users fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white bg-success">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Active Members</h6>
                    <h3 class="mb-0 fw-bold"><?= $member_counts['active'] ?></h3>
                </div>
                <i class="fas fa-user-check fa-3x opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white bg-info">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Total Contributions</h6>
                    <h3 class="mb-0 fw-bold"><?= View::formatCurrency($financial['total_contributions']) ?></h3>
                </div>
                <i class="fas fa-coins fa-3x opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white bg-danger">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Available Welfare Fund</h6>
                    <h3 class="mb-0 fw-bold"><?= View::formatCurrency($financial['available_balance']) ?></h3>
                </div>
                <i class="fas fa-wallet fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Request counts mini summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap justify-content-around text-center">
                    <div class="p-2 border-end flex-grow-1">
                        <span class="text-muted small">Pending Requests</span>
                        <h4 class="mb-0 fw-semibold text-warning"><?= $requests['pending'] ?></h4>
                    </div>
                    <div class="p-2 border-end flex-grow-1">
                        <span class="text-muted small">Under Review</span>
                        <h4 class="mb-0 fw-semibold text-primary"><?= $requests['review'] ?></h4>
                    </div>
                    <div class="p-2 border-end flex-grow-1">
                        <span class="text-muted small">Approved</span>
                        <h4 class="mb-0 fw-semibold text-success"><?= $requests['approved'] ?></h4>
                    </div>
                    <div class="p-2 border-end flex-grow-1">
                        <span class="text-muted small">Rejected</span>
                        <h4 class="mb-0 fw-semibold text-danger"><?= $requests['rejected'] ?></h4>
                    </div>
                    <div class="p-2 flex-grow-1">
                        <span class="text-muted small">Completed Disbursements</span>
                        <h4 class="mb-0 fw-semibold text-indigo" style="color: #6366f1;"><?= $requests['completed'] ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Timelines -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Contribution Statistics (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="contributionsChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Welfare Request Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="categoriesChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent System Logs -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Recent System Activities (Audit Trail)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_activities as $log): ?>
                                <tr>
                                    <td class="small"><?= View::formatDateTime($log['created_at']) ?></td>
                                    <td>
                                        <span class="fw-medium"><?= View::escape($log['username'] ?? 'System') ?></span>
                                        <small class="text-muted d-block"><?= View::escape($log['role'] ?? '') ?></small>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= View::escape($log['action']) ?></span></td>
                                    <td><?= View::escape($log['details']) ?></td>
                                    <td class="text-muted small"><?= View::escape($log['ip_address']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
