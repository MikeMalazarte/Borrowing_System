<!-- Header -->
<p class="text-muted small mb-0">Management</p>
<h5 class="fw-normal mb-1">Tools</h5>
<p class="text-muted small mb-4">Manage your tool inventory.</p>

<!-- Actions Bar -->
 <div>
    <button class="btn btn-sm btn-dark mb-3" id="btnOpenAddTool" style="font-size:13px;">
        + Add Tool
    </button>
 </div>
<div class="d-flex align-items-center mb-3">
    <input type="text" id="search_tools_admin" class="form-control form-control-sm w-auto"
        placeholder="Search tools..." style="font-size:13px; min-width:220px;">
</div>

<!-- Tools Table -->
<div class="card border-0 shadow-none" style="background:#fff; border: 0.5px solid #e9e9e9 !important;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm" style="font-size:13px;">
                <thead>
                    <tr class="text-muted" style="border-bottom: 0.5px solid #e9e9e9;">
                        <th class="fw-normal">Tool Code</th>
                        <th class="fw-normal">Tool Name</th>
                        <th class="fw-normal">Description</th>
                        <th class="fw-normal">Quantity</th>
                        <th class="fw-normal">Available</th>
                        <th class="fw-normal">Status</th>
                        <th class="fw-normal">Action</th>
                    </tr>
                </thead>
                <tbody id="admin_tools_list">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            <small>Loading...</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <small class="text-muted" id="tools_info"></small>
            <div id="tools_pagination" class="d-flex gap-1"></div>
        </div>
    </div>
</div>

<!-- Add Tool Modal -->
<div class="modal fade" id="modalAddTool" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Add Tool</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="add_tool_msg"></div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Tool Name</label>
                    <input type="text" class="form-control form-control-sm" id="add_tool_name" placeholder="e.g. Hammer">
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Description</label>
                    <textarea class="form-control form-control-sm" id="add_tool_description" rows="2"
                        placeholder="Short description..."></textarea>
                </div>
                <div class="mb-0">
                    <label class="text-muted small mb-1">Quantity</label>
                    <input type="number" class="form-control form-control-sm" id="add_tool_quantity" min="1" placeholder="e.g. 5">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-dark" id="btnConfirmAddTool">Add Tool</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Tool Modal -->
<div class="modal fade" id="modalEditTool" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Edit Tool</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="edit_tool_msg"></div>
                <input type="hidden" id="edit_tool_code">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Tool Name</label>
                    <input type="text" class="form-control form-control-sm" id="edit_tool_name">
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Description</label>
                    <textarea class="form-control form-control-sm" id="edit_tool_description" rows="2"></textarea>
                </div>
                <div class="mb-0">
                    <label class="text-muted small mb-1">Quantity</label>
                    <input type="number" class="form-control form-control-sm" id="edit_tool_quantity" min="1">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-dark" id="btnConfirmEditTool">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Tool Modal -->
<div class="modal fade" id="modalArchiveTool" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-normal">Archive Tool</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="archive_tool_msg"></div>
                <input type="hidden" id="archive_tool_code">
                <p class="small text-muted mb-0">Are you sure you want to archive
                    <strong id="archive_tool_name"></strong>?
                    It will no longer be available for borrowing.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-dark" id="btnConfirmArchiveTool">Archive</button>
            </div>
        </div>
    </div>
</div>