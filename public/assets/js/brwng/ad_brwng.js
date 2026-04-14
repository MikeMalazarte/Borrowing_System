var AdBrwngSys = function () {

    this.loadAdminDashboard = function () {
        var self         = this;
        var currentPage  = 1;
        var searchTerm   = '';

        function fetchBorrowings() {
            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : {
                    meaction : 'GET-ADMIN-DASHBOARD-STATS',
                    page     : currentPage,
                    term     : searchTerm
                },
                dataType : 'json',
                success  : function (res) {

                    // Only update stat cards on first load
                    if (currentPage === 1 && searchTerm === '') {
                        $('#stat_total_tools').text(res.total_tools);
                        $('#stat_available_tools').text(res.available_tools);
                        $('#stat_active_borrowings').text(res.active_borrowings);
                        $('#stat_total_students').text(res.total_students);
                        $('#stat_overdue').text(res.overdue);
                        $('#stat_returned_today').text(res.returned_today);
                        $('#stat_borrowed_today').text(res.borrowed_today);
                        $('#stat_total_borrowings').text(res.total_borrowings);

                        // Inventory
                        var inv = '';
                        if (res.inventory.length === 0) {
                            inv = '<p class="text-muted small text-center py-3">No tools found.</p>';
                        } else {
                            $.each(res.inventory, function (i, t) {
                                var pct      = t.quantity > 0 ? Math.round((t.available / t.quantity) * 100) : 0;
                                var barColor = pct > 50 ? '#1a1a1a' : (pct > 20 ? '#888' : '#c0392b');
                                inv += '<div class="mb-3">' +
                                    '<div class="d-flex justify-content-between mb-1">' +
                                        '<small>' + t.tool_name + '</small>' +
                                        '<small class="text-muted">' + t.available + ' / ' + t.quantity + '</small>' +
                                    '</div>' +
                                    '<div style="height:4px; background:#f0f0f0; border-radius:2px;">' +
                                        '<div style="height:4px; width:' + pct + '%; background:' + barColor + '; border-radius:2px;"></div>' +
                                    '</div>' +
                                '</div>';
                            });
                        }
                        $('#admin_inventory').html(inv);
                    }

                    // Recent borrowings table
                    var rows = '';
                    if (res.recent.length === 0) {
                        rows = '<tr><td colspan="5" class="text-center text-muted py-3"><small>No borrowings found.</small></td></tr>';
                    } else {
                        $.each(res.recent, function (i, r) {
                            var badge = '';
                            if (r.status === 'Active')   badge = '<span class="badge rounded-pill" style="background:#f3f3f3; color:#555; border: 0.5px solid #e9e9e9; font-weight:400;">Active</span>';
                            if (r.status === 'Returned') badge = '<span class="badge rounded-pill" style="background:#f3f3f3; color:#555; border: 0.5px solid #e9e9e9; font-weight:400;">Returned</span>';
                            if (r.status === 'Overdue')  badge = '<span class="badge rounded-pill" style="background:#fff0f0; color:#c0392b; border: 0.5px solid #f5c6c6; font-weight:400;">Overdue</span>';

                            rows += '<tr>' +
                                '<td>' + r.full_name + '<br><small class="text-muted">' + r.user_code + '</small></td>' +
                                '<td>' + r.tool_name + '</td>' +
                                '<td>' + r.borrowed_at + '</td>' +
                                '<td>' + r.due_date + '</td>' +
                                '<td>' + badge + '</td>' +
                            '</tr>';
                        });
                    }
                    $('#admin_recent_borrowings').html(rows);

                    // Info text
                    var from = res.total_records === 0 ? 0 : ((currentPage - 1) * 8) + 1;
                    var to   = Math.min(currentPage * 8, res.total_records);
                    $('#borrowings_info').text('Showing ' + from + '–' + to + ' of ' + res.total_records);

                    // Pagination buttons
                    var pages = '';
                    if (res.total_pages > 1) {
                        pages += '<button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:12px;" id="btn_prev_page" '
                            + (currentPage === 1 ? 'disabled' : '') + '>&#8249;</button>';

                        for (var p = 1; p <= res.total_pages; p++) {
                            var active = p === currentPage
                                ? 'btn-dark'
                                : 'btn-outline-secondary';
                            pages += '<button class="btn btn-sm ' + active + ' py-0 px-2 btn_page" style="font-size:12px;" data-page="' + p + '">' + p + '</button>';
                        }

                        pages += '<button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:12px;" id="btn_next_page" '
                            + (currentPage === res.total_pages ? 'disabled' : '') + '>&#8250;</button>';
                    }
                    $('#borrowings_pagination').html(pages);
                },
                error : function () {
                    console.error('Failed to load admin dashboard stats.');
                }
            });
        }

        // Initial load
        fetchBorrowings();

        // Search — debounced
        var searchTimer;
        $(document).on('keyup', '#search_borrowings', function () {
            clearTimeout(searchTimer);
            var term = $.trim($(this).val());
            searchTimer = setTimeout(function () {
                searchTerm  = term;
                currentPage = 1;
                fetchBorrowings();
            }, 400);
        });

        // Pagination clicks
        $(document).on('click', '.btn_page', function () {
            currentPage = parseInt($(this).data('page'));
            fetchBorrowings();
        });

        $(document).on('click', '#btn_prev_page', function () {
            if (currentPage > 1) {
                currentPage--;
                fetchBorrowings();
            }
        });

        $(document).on('click', '#btn_next_page', function () {
            currentPage++;
            fetchBorrowings();
        });
    };

    this.doLogout = function () {
        $(document).on('click', '#btnLogout', function (e) {
            e.preventDefault();

            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : { meaction : 'DO-LOGOUT' },
                dataType : 'json',
                success  : function (data) {
                    if (data.status === 'ok') {
                        window.location.href = mesiteurl + 'Borrowing-System';
                    }
                },
                error : function () {
                    alert('Something went wrong. Try again.');
                }
            });
        });
    };

    this.loadAdminTools = function () {
        var self        = this;
        var currentPage = 1;
        var searchTerm  = '';

        function fetchTools() {
            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : {
                    meaction : 'GET-ADMIN-TOOLS',
                    page     : currentPage,
                    term     : searchTerm
                },
                dataType : 'json',
                success  : function (res) {
                    var rows = '';
                    if (res.tools.length === 0) {
                        rows = '<tr><td colspan="7" class="text-center text-muted py-3"><small>No tools found.</small></td></tr>';
                    } else {
                        $.each(res.tools, function (i, t) {
                            var statusBadge = '<span class="badge rounded-pill" style="background:#f0faf0; color:#3a7d44; border: 0.5px solid #c3e6cb; font-weight:400;">Active</span>';
                            var availBadge  = parseInt(t.available) > 0
                                ? '<span style="color:#3a7d44;">' + t.available + '</span>'
                                : '<span style="color:#c0392b;">' + t.available + '</span>';

                            rows += '<tr>' +
                                '<td class="text-muted">' + t.tool_code + '</td>' +
                                '<td>' + t.tool_name + '</td>' +
                                '<td class="text-muted">' + (t.description || '—') + '</td>' +
                                '<td>' + t.quantity + '</td>' +
                                '<td>' + availBadge + '</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '<td>' +
                                    '<button class="btn btn-sm btn-outline-secondary me-1 btnEditTool" style="font-size:12px;" ' +
                                        'data-tool_code="' + t.tool_code + '" ' +
                                        'data-tool_name="' + t.tool_name + '" ' +
                                        'data-tool_description="' + (t.description || '') + '" ' +
                                        'data-tool_quantity="' + t.quantity + '">Edit</button>' +
                                    '<button class="btn btn-sm btn-outline-secondary btnArchiveTool" style="font-size:12px; color:#c0392b;" ' +
                                        'data-tool_code="' + t.tool_code + '" ' +
                                        'data-tool_name="' + t.tool_name + '">Archive</button>' +
                                '</td>' +
                            '</tr>';
                        });
                    }
                    $('#admin_tools_list').html(rows);

                    // Info
                    var from = res.total_records === 0 ? 0 : ((currentPage - 1) * 10) + 1;
                    var to   = Math.min(currentPage * 10, res.total_records);
                    $('#tools_info').text('Showing ' + from + '–' + to + ' of ' + res.total_records);

                    // Pagination
                    var pages = '';
                    if (res.total_pages > 1) {
                        pages += '<button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:12px;" id="btn_tools_prev" '
                            + (currentPage === 1 ? 'disabled' : '') + '>&#8249;</button>';
                        for (var p = 1; p <= res.total_pages; p++) {
                            var active = p === currentPage ? 'btn-dark' : 'btn-outline-secondary';
                            pages += '<button class="btn btn-sm ' + active + ' py-0 px-2 btn_tools_page" style="font-size:12px;" data-page="' + p + '">' + p + '</button>';
                        }
                        pages += '<button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:12px;" id="btn_tools_next" '
                            + (currentPage === res.total_pages ? 'disabled' : '') + '>&#8250;</button>';
                    }
                    $('#tools_pagination').html(pages);
                },
                error : function () {
                    console.error('Failed to load tools.');
                }
            });
        }

        // Initial load
        fetchTools();

        // Search — debounced
        var searchTimer;
        $(document).on('keyup', '#search_tools_admin', function () {
            clearTimeout(searchTimer);
            var term = $.trim($(this).val());
            searchTimer = setTimeout(function () {
                searchTerm  = term;
                currentPage = 1;
                fetchTools();
            }, 400);
        });

        // Pagination
        $(document).on('click', '.btn_tools_page', function () {
            currentPage = parseInt($(this).data('page'));
            fetchTools();
        });
        $(document).on('click', '#btn_tools_prev', function () {
            if (currentPage > 1) { currentPage--; fetchTools(); }
        });
        $(document).on('click', '#btn_tools_next', function () {
            currentPage++; fetchTools();
        });

        // Open Add Modal
        $(document).on('click', '#btnOpenAddTool', function () {
            $('#add_tool_name').val('');
            $('#add_tool_description').val('');
            $('#add_tool_quantity').val('');
            $('#add_tool_msg').html('');
            var modal = new bootstrap.Modal(document.getElementById('modalAddTool'));
            modal.show();
        });

        // Confirm Add
        $(document).on('click', '#btnConfirmAddTool', function () {
            var tool_name    = $.trim($('#add_tool_name').val());
            var description  = $.trim($('#add_tool_description').val());
            var quantity     = $.trim($('#add_tool_quantity').val());

            if (tool_name === '' || quantity === '') {
                $('#add_tool_msg').html('<div class="alert alert-danger py-2 small">Please fill in all required fields.</div>');
                return false;
            }

            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : {
                    meaction         : 'DO-ADD-TOOL',
                    tool_name        : tool_name,
                    tool_description : description,
                    tool_quantity    : quantity
                },
                dataType : 'json',
                success  : function (data) {
                    if (data.status === 'ok') {
                        $('#add_tool_msg').html('<div class="alert alert-success py-2 small">Tool added successfully!</div>');
                        setTimeout(function () {
                            bootstrap.Modal.getInstance(document.getElementById('modalAddTool')).hide();
                            fetchTools();
                        }, 1500);
                    } else {
                        $('#add_tool_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                    }
                },
                error : function () {
                    $('#add_tool_msg').html('<div class="alert alert-danger py-2 small">Something went wrong.</div>');
                }
            });
        });

        // Open Edit Modal
        $(document).on('click', '.btnEditTool', function () {
            $('#edit_tool_code').val($(this).data('tool_code'));
            $('#edit_tool_name').val($(this).data('tool_name'));
            $('#edit_tool_description').val($(this).data('tool_description'));
            $('#edit_tool_quantity').val($(this).data('tool_quantity'));
            $('#edit_tool_msg').html('');
            var modal = new bootstrap.Modal(document.getElementById('modalEditTool'));
            modal.show();
        });

        // Confirm Edit
        $(document).on('click', '#btnConfirmEditTool', function () {
            var tool_code   = $('#edit_tool_code').val();
            var tool_name   = $.trim($('#edit_tool_name').val());
            var description = $.trim($('#edit_tool_description').val());
            var quantity    = $.trim($('#edit_tool_quantity').val());

            if (tool_name === '' || quantity === '') {
                $('#edit_tool_msg').html('<div class="alert alert-danger py-2 small">Please fill in all required fields.</div>');
                return false;
            }

            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : {
                    meaction         : 'DO-EDIT-TOOL',
                    tool_code        : tool_code,
                    tool_name        : tool_name,
                    tool_description : description,
                    tool_quantity    : quantity
                },
                dataType : 'json',
                success  : function (data) {
                    if (data.status === 'ok') {
                        $('#edit_tool_msg').html('<div class="alert alert-success py-2 small">Tool updated successfully!</div>');
                        setTimeout(function () {
                            bootstrap.Modal.getInstance(document.getElementById('modalEditTool')).hide();
                            fetchTools();
                        }, 1500);
                    } else {
                        $('#edit_tool_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                    }
                },
                error : function () {
                    $('#edit_tool_msg').html('<div class="alert alert-danger py-2 small">Something went wrong.</div>');
                }
            });
        });

        // Open Archive Modal
        $(document).on('click', '.btnArchiveTool', function () {
            $('#archive_tool_code').val($(this).data('tool_code'));
            $('#archive_tool_name').text($(this).data('tool_name'));
            $('#archive_tool_msg').html('');
            var modal = new bootstrap.Modal(document.getElementById('modalArchiveTool'));
            modal.show();
        });

        // Confirm Archive
        $(document).on('click', '#btnConfirmArchiveTool', function () {
            var tool_code = $('#archive_tool_code').val();

            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : { meaction : 'DO-ARCHIVE-TOOL', tool_code : tool_code },
                dataType : 'json',
                success  : function (data) {
                    if (data.status === 'ok') {
                        $('#archive_tool_msg').html('<div class="alert alert-success py-2 small">Tool archived successfully!</div>');
                        setTimeout(function () {
                            bootstrap.Modal.getInstance(document.getElementById('modalArchiveTool')).hide();
                            fetchTools();
                        }, 1500);
                    } else {
                        $('#archive_tool_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                    }
                },
                error : function () {
                    $('#archive_tool_msg').html('<div class="alert alert-danger py-2 small">Something went wrong.</div>');
                }
            });
        });
    };

    this.loadAdminBorrowings = function () {
        var self        = this;
        var currentPage = 1;
        var searchTerm  = '';
        var statusFilter = 'all';

    function fetchBorrowings() {
        $.ajax({
            type     : 'POST',
            url      : mesiteurl + 'Borrowing-System',
            data     : {
                meaction : 'GET-ADMIN-BORROWINGS',
                page     : currentPage,
                term     : searchTerm,
                status   : statusFilter
            },
            dataType : 'json',
            success  : function (res) {

                // Update chip counts
                $('#chip_all').text('(' + res.cnt_all + ')');
                $('#chip_active').text('(' + res.cnt_active + ')');
                $('#chip_overdue').text('(' + res.cnt_overdue + ')');
                $('#chip_returned').text('(' + res.cnt_returned + ')');

                // Table rows
                var rows = '';
                if (res.borrowings.length === 0) {
                    rows = '<tr><td colspan="8" class="text-center text-muted py-3"><small>No borrowings found.</small></td></tr>';
                } else {
                    $.each(res.borrowings, function (i, r) {
                        var badge = '';
                        var rowStyle = '';
                        if (r.status_label === 'Active') {
                            badge    = '<span class="badge rounded-pill" style="background:#f3f3f3; color:#555; border: 0.5px solid #e9e9e9; font-weight:400;">Active</span>';
                        }
                        if (r.status_label === 'Returned') {
                            badge    = '<span class="badge rounded-pill" style="background:#f3f3f3; color:#555; border: 0.5px solid #e9e9e9; font-weight:400;">Returned</span>';
                        }
                        if (r.status_label === 'Overdue') {
                            badge    = '<span class="badge rounded-pill" style="background:#fff0f0; color:#c0392b; border: 0.5px solid #f5c6c6; font-weight:400;">Overdue</span>';
                            rowStyle = 'background:#fff8f8;';
                        }

                        var actionBtn = '';
                        if (r.status_label === 'Active' || r.status_label === 'Overdue') {
                            actionBtn = '<button class="btn btn-sm btn-outline-secondary me-1 btnAdminReturn" style="font-size:12px;" ' +
                                'data-brw_code="'  + r.brw_code  + '" ' +
                                'data-tool_name="' + r.tool_name + '">Return</button>';
                        }
                        actionBtn += '<button class="btn btn-sm btn-outline-secondary btnViewBorrowing" style="font-size:12px;" ' +
                            'data-brw_code="'    + r.brw_code    + '" ' +
                            'data-full_name="'   + r.full_name   + '" ' +
                            'data-user_code="'   + r.user_code   + '" ' +
                            'data-tool_name="'   + r.tool_name   + '" ' +
                            'data-borrowed_at="' + r.borrowed_at + '" ' +
                            'data-time_from="'   + r.time_from   + '" ' +
                            'data-time_to="'     + r.time_to     + '" ' +
                            'data-due_date="'    + r.due_date    + '" ' +
                            'data-returned_at="' + (r.returned_at || '—') + '" ' +
                            'data-status="'      + r.status_label + '">View</button>';

                        rows += '<tr style="' + rowStyle + '">' +
                            '<td class="text-muted">' + r.brw_code + '</td>' +
                            '<td>' + r.full_name + '<br><small class="text-muted">' + r.user_code + '</small></td>' +
                            '<td>' + r.tool_name + '</td>' +
                            '<td>' + r.borrowed_at + '</td>' +
                            '<td class="text-muted">' + r.time_from + ' – ' + r.time_to + '</td>' +
                            '<td>' + r.due_date + '</td>' +
                            '<td>' + badge + '</td>' +
                            '<td>' + actionBtn + '</td>' +
                        '</tr>';
                    });
                }
                $('#admin_borrowings_list').html(rows);

                // Info
                var from = res.total_records === 0 ? 0 : ((currentPage - 1) * 10) + 1;
                var to   = Math.min(currentPage * 10, res.total_records);
                $('#borrowings_tab_info').text('Showing ' + from + '–' + to + ' of ' + res.total_records);

                // Pagination
                var pages = '';
                if (res.total_pages > 1) {
                    pages += '<button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:12px;" id="btn_brw_prev" '
                        + (currentPage === 1 ? 'disabled' : '') + '>&#8249;</button>';
                    for (var p = 1; p <= res.total_pages; p++) {
                        var active = p === currentPage ? 'btn-dark' : 'btn-outline-secondary';
                        pages += '<button class="btn btn-sm ' + active + ' py-0 px-2 btn_brw_page" style="font-size:12px;" data-page="' + p + '">' + p + '</button>';
                    }
                    pages += '<button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:12px;" id="btn_brw_next" '
                        + (currentPage === res.total_pages ? 'disabled' : '') + '>&#8250;</button>';
                }
                $('#borrowings_tab_pagination').html(pages);
            },
            error : function () {
                console.error('Failed to load borrowings.');
            }
        });
    }

    // Initial load
    fetchBorrowings();

    // Filter chips
    $(document).on('click', '.filter_chip', function () {
        $('.filter_chip').removeClass('btn-dark').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-dark');
        statusFilter = $(this).data('status');
        currentPage  = 1;
        fetchBorrowings();
    });

    // Search — debounced
    var searchTimer;
    $(document).on('keyup', '#search_borrowings_admin', function () {
        clearTimeout(searchTimer);
        var term = $.trim($(this).val());
        searchTimer = setTimeout(function () {
            searchTerm  = term;
            currentPage = 1;
            fetchBorrowings();
        }, 400);
    });

    // Pagination
    $(document).on('click', '.btn_brw_page', function () {
        currentPage = parseInt($(this).data('page'));
        fetchBorrowings();
    });
    $(document).on('click', '#btn_brw_prev', function () {
        if (currentPage > 1) { currentPage--; fetchBorrowings(); }
    });
    $(document).on('click', '#btn_brw_next', function () {
        currentPage++; fetchBorrowings();
    });

    // View Details Modal
    $(document).on('click', '.btnViewBorrowing', function () {
        $('#view_brw_code').text($(this).data('brw_code'));
        $('#view_full_name').text($(this).data('full_name'));
        $('#view_user_code').text($(this).data('user_code'));
        $('#view_tool_name').text($(this).data('tool_name'));
        $('#view_borrowed_at').text($(this).data('borrowed_at'));
        $('#view_time_period').text($(this).data('time_from') + ' – ' + $(this).data('time_to'));
        $('#view_due_date').text($(this).data('due_date'));
        $('#view_returned_at').text($(this).data('returned_at'));
        $('#view_status').text($(this).data('status'));
        var modal = new bootstrap.Modal(document.getElementById('modalViewBorrowing'));
        modal.show();
    });

    // Mark as Returned Modal
    $(document).on('click', '.btnAdminReturn', function () {
        $('#admin_return_brw_code').val($(this).data('brw_code'));
        $('#admin_return_tool_name').text($(this).data('tool_name'));
        $('#admin_return_msg').html('');
        var modal = new bootstrap.Modal(document.getElementById('modalAdminReturn'));
        modal.show();
    });

    // Confirm Return
    $(document).on('click', '#btnConfirmAdminReturn', function () {
        var brw_code = $('#admin_return_brw_code').val();
        $.ajax({
            type     : 'POST',
            url      : mesiteurl + 'Borrowing-System',
            data     : { meaction : 'DO-ADMIN-RETURN', brw_code : brw_code },
            dataType : 'json',
            success  : function (data) {
                if (data.status === 'ok') {
                    $('#admin_return_msg').html('<div class="alert alert-success py-2 small">Marked as returned successfully!</div>');
                    setTimeout(function () {
                        bootstrap.Modal.getInstance(document.getElementById('modalAdminReturn')).hide();
                        fetchBorrowings();
                    }, 1500);
                } else {
                    $('#admin_return_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                }
            },
            error : function () {
                $('#admin_return_msg').html('<div class="alert alert-danger py-2 small">Something went wrong.</div>');
            }
        });
    });

    // Export CSV
    $(document).on('click', '#btnExportCSV', function () {
        window.location.href = mesiteurl + 'Borrowing-System?meaction=EXPORT-BORROWINGS-CSV';
    });
    };

};

// Initialize on DOM ready
$(document).ready(function () {
    var me = new AdBrwngSys();
    me.doLogout();

    if ($('#stat_total_tools').length > 0)       { me.loadAdminDashboard(); }
    if ($('#admin_tools_list').length > 0)        { me.loadAdminTools(); }
    if ($('#admin_borrowings_list').length > 0)   { me.loadAdminBorrowings(); }
});