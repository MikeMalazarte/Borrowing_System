var AdBrwngSys = function () {

    this.loadAdminDashboard = function () {
        $.ajax({
            type     : 'POST',
            url      : mesiteurl + 'Borrowing-System',
            data     : { meaction : 'GET-ADMIN-DASHBOARD-STATS' },
            dataType : 'json',
            success  : function (res) {
                // Stat cards
                $('#stat_total_tools').text(res.total_tools);
                $('#stat_available_tools').text(res.available_tools);
                $('#stat_active_borrowings').text(res.active_borrowings);
                $('#stat_total_students').text(res.total_students);
                $('#stat_overdue').text(res.overdue);
                $('#stat_returned_today').text(res.returned_today);
                $('#stat_borrowed_today').text(res.borrowed_today);
                $('#stat_total_borrowings').text(res.total_borrowings);

                // Recent borrowings table
                var rows = '';
                if (res.recent.length === 0) {
                    rows = '<tr><td colspan="5" class="text-center text-muted py-3"><small>No borrowings yet.</small></td></tr>';
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

                // Inventory list
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
            },
            error : function () {
                console.error('Failed to load admin dashboard stats.');
            }
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