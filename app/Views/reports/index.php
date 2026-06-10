<?php
use App\Core\View;
?>
<div class="row">
    <!-- Reports Panel options -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-chart-line me-2"></i>Generate Reports</h5>
            </div>
            
            <div class="card-body">
                <form action="<?= BASE_URL ?>/report/generate" method="GET" target="_blank">
                    <div class="mb-3">
                        <label for="type" class="form-label">Report Category</label>
                        <select class="form-select border-primary" id="type" name="type" required>
                            <option value="members">Member Directory List</option>
                            <option value="contributions">Contribution Ledger</option>
                            <option value="requests">Welfare Request Pipeline</option>
                            <option value="financial">Financial Balance Sheet</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="col-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block">Export Format</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="format" id="formatHtml" value="html" checked>
                            <label class="form-check-input-label" for="formatHtml"><i class="fas fa-print me-1 text-primary"></i>Print / Save PDF</label>
                        </div>
                        <div class="form-check form-check-inline ms-3">
                            <input class="form-check-input" type="radio" name="format" id="formatExcel" value="excel">
                            <label class="form-check-input-label" for="formatExcel"><i class="fas fa-file-excel me-1 text-success"></i>Export Excel (CSV)</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="fas fa-cog me-2"></i>Generate Report
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard stats overview -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold text-dark"><i class="fas fa-info-circle me-2"></i>Report Types Information</h5>
            </div>
            
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0">
                        <h6 class="fw-semibold text-primary mb-1">Member Directory List</h6>
                        <p class="text-muted small mb-0">List of all registered members, statuses, phone contacts and join dates.</p>
                    </div>
                    <div class="list-group-item px-0">
                        <h6 class="fw-semibold text-success mb-1">Contribution Ledger</h6>
                        <p class="text-muted small mb-0">Ledger of all monthly contributions recorded. Filterable by date ranges.</p>
                    </div>
                    <div class="list-group-item px-0">
                        <h6 class="fw-semibold text-warning mb-1">Welfare Request Pipeline</h6>
                        <p class="text-muted small mb-0">Record of all submitted welfare cases, statuses and amounts requested.</p>
                    </div>
                    <div class="list-group-item px-0">
                        <h6 class="fw-semibold text-danger mb-1">Financial Balance Sheet</h6>
                        <p class="text-muted small mb-0">Aggregation of total contributions, disbursements, and available virtual net balances.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
