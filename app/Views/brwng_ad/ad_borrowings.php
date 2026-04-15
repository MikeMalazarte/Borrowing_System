<!-- Header -->
<p class="text-muted small mb-0">Management</p>
<h5 class="fw-normal mb-1">Borrowings</h5>
<p class="text-muted small mb-4">Track and manage all tool borrowings.</p>

<!-- Filter Chips + Search + Export -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap" id="status_filters">
        <button class="btn btn-sm btn-dark filter_chip" data-status="all" style="font-size:12px;">All <span id="chip_all" class="ms-1">—</span></button>
        <button class="btn btn-sm btn-outline-secondary filter_chip" data-status="1" style="font-size:12px;">Active <span id="chip_active" class="ms-1">—</span></button>
        <button class="btn btn-sm btn-outline-secondary filter_chip" data-status="3" style="font-size:12px;">Overdue <span id="chip_overdue" class="ms-1">—</span></button>
        <button class="btn btn-sm btn-outline-secondary filter_chip" data-status="2" style="font-size:12px;">Returned <span id="chip_returned" class="ms-1">—</span></button>
    </div>
</div>

<!-- Search + Export -->
<div class="mb-2">
    <div class="d-flex gap-2 align-items-center">
        <input type="text" id="search_borrowings_admin" class="form-control form-control-sm"
            placeholder="Search student or tool..." style="font-size:13px; width:244px;">
        <button class="btn btn-sm btn-outline-secondary" id="btnExportCSV" style="font-size:13px; white-space:nowrap;">
            Export CSV
        </button>
    </div>
</div>

<!-- Borrowings Table -->
<div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm" style="font-size:13px;">
                <thead>
                    <tr class="text-muted" style="border-bottom: 0.5px solid #e9e9e9;">
                        <th class="fw-normal">Brw Code</th>
                        <th class="fw-normal">Student</th>
                        <th class="fw-normal">Tool</th>
                        <th class="fw-normal">Borrowed</th>
                        <th class="fw-normal">Time Period</th>
                        <th class="fw-normal">Due</th>
                        <th class="fw-normal">Status</th>
                        <th class="fw-normal">Action</th>
                    </tr>
                </thead>
                <tbody id="admin_borrowings_list">
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
            <small class="text-muted" id="borrowings_tab_info"></small>
            <div id="borrowings_tab_pagination" class="d-flex gap-1"></div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="modalViewBorrowing" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Borrowing Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                    <span class="text-muted">Brw Code</span>
                    <span id="view_brw_code"></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                    <span class="text-muted">Student</span>
                    <span id="view_full_name"></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                    <span class="text-muted">Student ID</span>
                    <span id="view_user_code"></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                    <span class="text-muted">Tool</span>
                    <span id="view_tool_name"></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                    <span class="text-muted">Borrowed</span>
                    <span id="view_borrowed_at"></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                    <span class="text-muted">Time Period</span>
                    <span id="view_time_period"></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                    <span class="text-muted">Due Date</span>
                    <span id="view_due_date"></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px; border-color:#f0f0f0 !important;">
                    <span class="text-muted">Returned</span>
                    <span id="view_returned_at"></span>
                </div>
                <div class="d-flex justify-content-between py-2" style="font-size:13px;">
                    <span class="text-muted">Status</span>
                    <span id="view_status"></span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Returned Modal -->
<div class="modal fade" id="modalAdminReturn" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Mark as Returned</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="admin_return_msg"></div>
                <input type="hidden" id="admin_return_brw_code">
                <p class="small text-muted mb-0">Are you sure you want to mark
                    <strong id="admin_return_tool_name"></strong> as returned?
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-dark" id="btnConfirmAdminReturn">Confirm Return</button>
            </div>
        </div>
    </div>
</div>