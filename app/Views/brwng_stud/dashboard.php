<?php
$full_name = session()->get('full_name');
$user_code = session()->get('user_code');
$course = session()->get('course');
$year_level = session()->get('year_level');
?>


<!-- Greeting -->
<p class="text-muted small mb-0">Good day,</p>
<h5 class="fw-normal mb-1"><?= esc($full_name) ?></h5>  <!-- ← mb-1 not mb-4 -->
<p class="text-muted small mb-4"><?= esc($course) ?> — <?= esc($year_level) ?></p> <!-- ← p tag not h5 -->



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
                        <th class="fw-normal">Time Period</th>
                        <th class="fw-normal">Due</th>
                        <th class="fw-normal">Status</th>
                        <th class="fw-normal">Action</th> 
                    </tr>
                </thead>
                <tbody id="recent_borrowings">
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

<!-- Return Modal -->
<div class="modal fade" id="modalReturn" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Return Tool</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="return_msg"></div>
                <input type="hidden" id="return_brw_code">
                <p class="small text-muted mb-0">Are you sure you want to return <strong id="return_tool_name"></strong>?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-dark" id="btnConfirmReturn">Confirm Return</button>
            </div>
        </div>
    </div>
</div>