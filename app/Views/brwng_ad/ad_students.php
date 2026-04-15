<!-- Header -->
<p class="text-muted small mb-0">Management</p>
<h5 class="fw-normal mb-1">Students</h5>
<p class="text-muted small mb-4">Monitor student accounts and borrowing behavior.</p>

<!-- Filter Chips -->
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap" id="student_status_filters">
        <button class="btn filter_student_chip" data-status="all"
            style="font-size:13px; padding:10px 20px; border-radius:8px; background:#1a1a1a; color:#fff; border:none;">
            All
            <span id="chip_students_all" style="font-size:20px; font-weight:400; display:block; line-height:1;">—</span>
        </button>
        <button class="btn filter_student_chip" data-status="1"
            style="font-size:13px; padding:10px 20px; border-radius:8px; background:#fff; color:#555; border:0.5px solid #e9e9e9;">
            Active
            <span id="chip_students_active" style="font-size:20px; font-weight:400; display:block; line-height:1;">—</span>
        </button>
        <button class="btn filter_student_chip" data-status="0"
            style="font-size:13px; padding:10px 20px; border-radius:8px; background:#fff; color:#c0392b; border:0.5px solid #f5c6c6;">
            Suspended
            <span id="chip_students_suspended" style="font-size:20px; font-weight:400; display:block; line-height:1;">—</span>
        </button>
    </div>

    <!-- Search -->
    <div class="d-flex gap-2 align-items-center">
        <input type="text" id="search_students" class="form-control form-control-sm"
            placeholder="Search name, ID, or email..." style="font-size:13px; width:220px;">
    </div>
</div>

<!-- Students Table -->
<div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm" style="font-size:13px;">
                <thead>
                    <tr class="text-muted" style="border-bottom: 0.5px solid #e9e9e9;">
                        <th class="fw-normal">Student</th>
                        <th class="fw-normal">Course</th>
                        <th class="fw-normal">Year</th>
                        <th class="fw-normal">Total Borrowed</th>
                        <th class="fw-normal">Total Overdue</th>
                        <th class="fw-normal">Active Now</th>
                        <th class="fw-normal">Status</th>
                        <th class="fw-normal">Action</th>
                    </tr>
                </thead>
                <tbody id="admin_students_list">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            <small>Loading...</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <small class="text-muted" id="students_info"></small>
            <div id="students_pagination" class="d-flex gap-1"></div>
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div class="modal fade" id="modalViewStudent" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Student Profile</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <!-- Student Info + Stats -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-none h-100" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                            <div class="card-body">
                                <p class="text-uppercase text-muted mb-3" style="font-size:11px; letter-spacing:0.04em;">Personal Information</p>
                                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                                    <span class="text-muted">Full Name</span>
                                    <span id="view_std_full_name"></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                                    <span class="text-muted">Student ID</span>
                                    <span id="view_std_user_code"></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                                    <span class="text-muted">Email</span>
                                    <span id="view_std_email"></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                                    <span class="text-muted">Course</span>
                                    <span id="view_std_course"></span>
                                </div>
                                <div class="d-flex justify-content-between py-2" style="font-size:13px;">
                                    <span class="text-muted">Year Level</span>
                                    <span id="view_std_year_level"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-none h-100" style="background:#fafafa; border: 0.5px solid #e9e9e9 !important;">
                            <div class="card-body">
                                <p class="text-uppercase text-muted mb-3" style="font-size:11px; letter-spacing:0.04em;">Borrowing Behavior</p>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="card border-0" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
                                            <div class="card-body p-3">
                                                <p class="text-muted mb-1" style="font-size:11px;">Total Borrowed</p>
                                                <h4 class="fw-normal mb-0" id="view_std_total"></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card border-0" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
                                            <div class="card-body p-3">
                                                <p class="text-muted mb-1" style="font-size:11px;">Total Overdue</p>
                                                <h4 class="fw-normal mb-0" id="view_std_overdue" style="color:#c0392b;"></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card border-0" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
                                            <div class="card-body p-3">
                                                <p class="text-muted mb-1" style="font-size:11px;">Active Now</p>
                                                <h4 class="fw-normal mb-0" id="view_std_active"></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card border-0" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
                                            <div class="card-body p-3">
                                                <p class="text-muted mb-1" style="font-size:11px;">Returned On Time</p>
                                                <h4 class="fw-normal mb-0" id="view_std_ontime"></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Borrowing History -->
                <p class="text-uppercase text-muted mb-2" style="font-size:11px; letter-spacing:0.04em;">Borrowing History</p>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:13px;">
                        <thead>
                            <tr class="text-muted" style="border-bottom: 0.5px solid #e9e9e9;">
                                <th class="fw-normal">Tool</th>
                                <th class="fw-normal">Borrowed</th>
                                <th class="fw-normal">Due</th>
                                <th class="fw-normal">Returned</th>
                                <th class="fw-normal">Status</th>
                            </tr>
                        </thead>
                        <tbody id="view_std_history">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <small>Loading...</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Suspend/Activate Modal -->
<div class="modal fade" id="modalToggleStudent" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal" id="toggle_modal_title">Suspend Student</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="toggle_student_msg"></div>
                <input type="hidden" id="toggle_student_user_code">
                <input type="hidden" id="toggle_student_new_status">
                <p class="small text-muted mb-0" id="toggle_student_confirm_text"></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-dark" id="btnConfirmToggleStudent">Confirm</button>
            </div>
        </div>
    </div>
</div>