<?php
$full_name = session()->get('full_name');
?>

<!-- Header -->
<p class="text-muted small mb-0">Good day,</p>
<h5 class="fw-normal mb-1"><?= esc($full_name) ?></h5>
<p class="text-muted small mb-4">Administrator</p>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Tools</p>
                <h3 class="fw-normal mb-0" id="stat_total_tools">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Available Tools</p>
                <h3 class="fw-normal mb-0" id="stat_available_tools">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Active Borrowings</p>
                <h3 class="fw-normal mb-0" id="stat_active_borrowings">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Students</p>
                <h3 class="fw-normal mb-0" id="stat_total_students">—</h3>
            </div>
        </div>
    </div>
</div>

<!-- Second Row Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Overdue</p>
                <h3 class="fw-normal mb-0 text-danger" id="stat_overdue">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Returned Today</p>
                <h3 class="fw-normal mb-0" id="stat_returned_today">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Borrowed Today</p>
                <h3 class="fw-normal mb-0" id="stat_borrowed_today">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Borrowings</p>
                <h3 class="fw-normal mb-0" id="stat_total_borrowings">—</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Borrowing History -->
    <div class="col-md-8">
        <div class="card border-0" style="border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="small fw-500 mb-0">Recent Borrowings</p>
                    <a href="<?= base_url('Borrowing-System?meaction=ADMIN-BORROWINGS') ?>"
                        class="small text-muted text-decoration-none">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size: 13px;">
                        <thead>
                            <tr class="text-muted" style="border-bottom: 0.5px solid #e9e9e9;">
                                <th class="fw-normal">Student</th>
                                <th class="fw-normal">Tool</th>
                                <th class="fw-normal">Borrowed</th>
                                <th class="fw-normal">Due</th>
                                <th class="fw-normal">Status</th>
                            </tr>
                        </thead>
                        <tbody id="admin_recent_borrowings">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <small>Loading...</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory -->
    <div class="col-md-4">
        <div class="card border-0" style="border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="small fw-500 mb-0">Inventory</p>
                    <a href="<?= base_url('Borrowing-System?meaction=ADMIN-TOOLS') ?>"
                        class="small text-muted text-decoration-none">Manage</a>
                </div>
                <div id="admin_inventory">
                    <p class="text-muted small text-center py-3">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>