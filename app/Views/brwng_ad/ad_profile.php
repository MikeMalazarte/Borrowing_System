<?php
$full_name = session()->get('full_name');
$email     = session()->get('email');
$user_code = session()->get('user_code');

$name_parts = explode(' ', trim($full_name));
$initials   = '';
foreach ($name_parts as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
$initials = substr($initials, 0, 2);
?>

<!-- Header -->
<p class="text-muted small mb-0">System</p>
<h5 class="fw-normal mb-1">My Profile</h5>
<p class="text-muted small mb-4">Your account information and credentials.</p>

<!-- Avatar + Name Card -->
<div class="card border-0 shadow-none mb-3" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-light flex-shrink-0"
             style="width:56px; height:56px; border: 0.5px solid #e9e9e9; font-size:18px; font-weight:500; color:#555;">
            <?= esc($initials) ?>
        </div>
        <div>
            <p class="mb-0" style="font-size:15px; font-weight:500;"><?= esc($full_name) ?></p>
            <p class="text-muted small mb-1"><?= esc($user_code) ?></p>
            <span class="badge rounded-pill"
                style="background:#f3f3f3; border: 0.5px solid #e9e9e9; color:#555; font-size:11px; font-weight:400;">
                Administrator
            </span>
        </div>
    </div>
</div>

<!-- Account Information -->
<div class="card border-0 shadow-none mb-3" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <p class="text-uppercase text-muted mb-3" style="font-size:11px; letter-spacing:0.04em;">Account Information</p>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
            <span class="text-muted">Full Name</span>
            <span><?= esc($full_name) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
            <span class="text-muted">Admin ID</span>
            <span><?= esc($user_code) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
            <span class="text-muted">Email</span>
            <span><?= esc($email) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2" style="font-size:13px;">
            <span class="text-muted">Role</span>
            <span>Administrator</span>
        </div>
    </div>
</div>

<!-- Security -->
<div class="card border-0 shadow-none mb-3" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <p class="text-uppercase text-muted mb-3" style="font-size:11px; letter-spacing:0.04em;">Security</p>
        <div class="d-flex justify-content-between align-items-center py-2" style="font-size:13px;">
            <div>
                <p class="mb-0">Password</p>
                <p class="text-muted mb-0" style="font-size:12px;">Update your admin password.</p>
            </div>
            <button class="btn btn-sm btn-outline-secondary" style="font-size:13px;"
                data-bs-toggle="modal" data-bs-target="#modalAdminChangePassword">
                Change Password
            </button>
        </div>
    </div>
</div>

<!-- System Overview -->
<div class="card border-0 shadow-none mb-3" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <p class="text-uppercase text-muted mb-3" style="font-size:11px; letter-spacing:0.04em;">System Overview</p>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-none" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                    <div class="card-body p-3">
                        <p class="text-muted mb-1" style="font-size:12px;">Total Tools</p>
                        <h3 class="fw-normal mb-0" id="admin_prof_total_tools">—</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-none" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                    <div class="card-body p-3">
                        <p class="text-muted mb-1" style="font-size:12px;">Total Students</p>
                        <h3 class="fw-normal mb-0" id="admin_prof_total_students">—</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-none" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                    <div class="card-body p-3">
                        <p class="text-muted mb-1" style="font-size:12px;">Active Borrowings</p>
                        <h3 class="fw-normal mb-0" id="admin_prof_active_borrowings">—</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-none" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                    <div class="card-body p-3">
                        <p class="text-muted mb-1" style="font-size:12px;">Overdue</p>
                        <h3 class="fw-normal mb-0" style="color:#c0392b;" id="admin_prof_overdue">—</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="modalAdminChangePassword" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Change Password</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="admin_change_pw_msg"></div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Current Password</label>
                    <input type="password" class="form-control form-control-sm" id="admin_current_password" placeholder="••••••••">
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">New Password</label>
                    <input type="password" class="form-control form-control-sm" id="admin_new_password" placeholder="••••••••">
                </div>
                <div class="mb-0">
                    <label class="text-muted small mb-1">Confirm New Password</label>
                    <input type="password" class="form-control form-control-sm" id="admin_confirm_password" placeholder="••••••••">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-dark" id="btnConfirmAdminChangePassword">Update Password</button>
            </div>
        </div>
    </div>
</div>