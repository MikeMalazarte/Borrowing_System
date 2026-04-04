<!-- Header -->
<p class="text-muted small mb-0">Student</p>
<h5 class="fw-normal mb-4">Browse Tools</h5>

<!-- Search -->
<div class="card border-0 mb-3" style="border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body py-2">
        <input type="text" class="form-control form-control-sm border-0"
            id="search_tools" placeholder="Search tools...">
    </div>
</div>

<!-- Borrow Modal -->
<div class="modal fade" id="modalBorrow" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal" id="modal_tool_name"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal_msg"></div>
                <input type="hidden" id="modal_tool_code">

                <!-- Borrow Date -->
                <div class="mb-3">
                    <label class="form-label small text-muted">Borrow Date</label>
                    <input type="date" class="form-control form-control-sm"
                        id="borrow_date"
                        value="<?= date('Y-m-d') ?>"
                        min="<?= date('Y-m-d') ?>">
                </div>

                <!-- Borrow Time -->
                <div class="mb-3">
                    <label class="form-label small text-muted">Borrow Time</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small text-muted">From</label>
                            <input type="time" class="form-control form-control-sm"
                                id="borrow_time_from">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">To</label>
                            <input type="time" class="form-control form-control-sm"
                                id="borrow_time_to">
                        </div>
                    </div>
                </div>

                <!-- Due Date -->
                <div class="mb-3">
                    <label class="form-label small text-muted">Due Date</label>
                    <input type="date" class="form-control form-control-sm"
                        id="borrow_due_date"
                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>

            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-dark" id="btnConfirmBorrow">
                    Confirm Borrow
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tools Grid -->
<div class="row g-3" id="tools_list">
    <div class="col-12 text-center text-muted py-4">
        <small>Loading tools...</small>
    </div>
</div>