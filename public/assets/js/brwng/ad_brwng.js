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

};

// Initialize on DOM ready
$(document).ready(function () {
    var me = new AdBrwngSys();
    me.doLogout();

    if ($('#stat_total_tools').length > 0) { me.loadAdminDashboard(); }
});