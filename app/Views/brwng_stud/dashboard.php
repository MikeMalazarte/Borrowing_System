<?php
$full_name = session()->get('full_name');
$user_code = session()->get('user_code');
?>

<!-- Greeting -->
<p class="text-muted small mb-0">Good day,</p>
<h5 class="fw-normal mb-4"><?= esc($full_name) ?></h5>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Active Borrowings</p>
                <h3 class="fw-normal mb-0" id="stat_active">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Borrowed</p>
                <h3 class="fw-normal mb-0" id="stat_total">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
            <div class="card-body">
                <p class="text-muted small mb-1">Available Tools</p>
                <h3 class="fw-normal mb-0" id="stat_available">—</h3>
            </div>
        </div>
    </div>
</div>

<!-- Recent Borrowings -->
<div class="card border-0" style="border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="small fw-500 mb-0">Recent Borrowings</p>
            <a href="<?= base_url('Borrowing-System?meaction=MY-BORROWINGS') ?>"
                class="small text-muted text-decoration-none">View all</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm" style="font-size: 13px;">
                <thead>
                    <tr class="text-muted" style="border-bottom: 0.5px solid #e9e9e9;">
                        <th class="fw-normal">Tool</th>
                        <th class="fw-normal">Borrowed</th>
                        <th class="fw-normal">Due</th>
                        <th class="fw-normal">Status</th>
                    </tr>
                </thead>
                <tbody id="recent_borrowings">
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            <small>Loading...</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>