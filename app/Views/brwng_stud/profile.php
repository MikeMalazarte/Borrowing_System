<?php
$full_name = session()->get('full_name');
$email     = session()->get('email');
$user_code = session()->get('user_code');
$course    = session()->get('course');
$year_level = session()->get('year_level');

// Generate initials from full name
$name_parts = explode(' ', trim($full_name));
$initials = '';
foreach ($name_parts as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
$initials = substr($initials, 0, 2);
?>

<!-- Profile Header -->
<p class="text-muted small mb-0">My Profile</p>
<h5 class="fw-normal mb-1">Account Details</h5>
<p class="text-muted small mb-4">Your personal information and credentials.</p>

<!-- Avatar + Name Card -->
<div class="card border-0 shadow-none mb-3" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-light flex-shrink-0"
             style="width:56px; height:56px; border: 0.5px solid #e9e9e9; font-size:18px; font-weight:500; color:#555;">
            <?= esc($initials) ?>
        </div>
        <div>
            <p class="mb-0 fw-500" style="font-size:15px;"><?= esc($full_name) ?></p>
            <p class="text-muted small mb-1"><?= esc($user_code) ?></p>
            <span class="badge rounded-pill" style="background:#f3f3f3; border: 0.5px solid #e9e9e9; color:#555; font-size:11px; font-weight:400;">
                <?= esc($course) ?> — <?= esc($year_level) ?>
            </span>
        </div>
    </div>
</div>

<!-- Personal Information -->
<div class="card border-0 shadow-none mb-3" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <p class="text-uppercase text-muted mb-3" style="font-size:11px; letter-spacing:0.04em;">Personal Information</p>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
            <span class="text-muted">Full Name</span>
            <span><?= esc($full_name) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
            <span class="text-muted">Student ID</span>
            <span><?= esc($user_code) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
            <span class="text-muted">Course</span>
            <span><?= esc($course) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2" style="font-size:13px;">
            <span class="text-muted">Year Level</span>
            <span><?= esc($year_level) ?></span>
        </div>
    </div>
</div>

<!-- Account -->
<div class="card border-0 shadow-none mb-3" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <p class="text-uppercase text-muted mb-3" style="font-size:11px; letter-spacing:0.04em;">Account</p>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
            <span class="text-muted">Email</span>
            <span id="profile_email"><?= esc($email) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2" style="font-size:13px;">
            <span class="text-muted">Password</span>
            <button class="btn btn-sm btn-outline-secondary" style="font-size:13px;"
                    data-bs-toggle="modal" data-bs-target="#modalChangePassword">
                Change Password
            </button>
        </div>
    </div>
</div>

<!-- Borrowing Summary -->
<div class="card border-0 shadow-none mb-3" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <p class="text-uppercase text-muted mb-3" style="font-size:11px; letter-spacing:0.04em;">Borrowing Summary</p>
        <div class="row g-3">
            <div class="col-4">
                <div class="card border-0 shadow-none" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1" style="font-size:12px;">Active</p>
                        <h3 class="fw-normal mb-0" id="profile_stat_active">—</h3>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-none" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1" style="font-size:12px;">Total</p>
                        <h3 class="fw-normal mb-0" id="profile_stat_total">—</h3>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-none" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1" style="font-size:12px;">Returned</p>
                        <h3 class="fw-normal mb-0" id="profile_stat_returned">—</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="modalChangePassword" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Change Password</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="change_pw_msg"></div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Current Password</label>
                    <input type="password" class="form-control form-control-sm" id="current_password" placeholder="••••••••">
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">New Password</label>
                    <input type="password" class="form-control form-control-sm" id="new_password" placeholder="••••••••">
                </div>
                <div class="mb-0">
                    <label class="text-muted small mb-1">Confirm New Password</label>
                    <input type="password" class="form-control form-control-sm" id="confirm_password" placeholder="••••••••">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-dark" id="btnConfirmChangePassword">Update Password</button>
            </div>
        </div>
    </div>
</div>