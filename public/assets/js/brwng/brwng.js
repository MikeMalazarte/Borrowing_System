var BrwngSys = function () {

    this.doLogin = function () {
        $(document).on('click', '#btnLogin', function () {
            var email    = $.trim($('#login_email').val());
            var password = $.trim($('#login_password').val());

            if (email === '' || password === '') {
                $('#login_msg').html('<div class="alert alert-danger py-2 small">Please fill in all fields.</div>');
                return false;
            }

            $.ajax({
                type    : 'POST',
                url     : mesiteurl + 'Borrowing-System',
                data    : {
                    meaction       : 'DO-LOGIN',
                    login_email    : email,
                    login_password : password
                },
                dataType : 'json',
                success : function (data) {
                    if (data.status === 'ok') {
                        if (data.user_role == 1) {
                            // Admin
                            window.location.href = mesiteurl + 'Borrowing-System?meaction=ADMIN-DASHBOARD';
                        } else {
                            // Student
                            window.location.href = mesiteurl + 'Borrowing-System?meaction=STUDENT-DASHBOARD';
                        }
                    } else {
                        $('#login_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                    }
                },
            });
        });

        // Enter key to submit
        $(document).on('keypress', '#login_email, #login_password', function (e) {
            if (e.which === 13) $('#btnLogin').click();
        });
    };

    this.doRegister = function () {
    $(document).on('click', '#btnRegister', function () {
        var firstname        = $.trim($('#reg_firstname').val());
        var lastname         = $.trim($('#reg_lastname').val());
        var email            = $.trim($('#reg_email').val());
        var course           = $.trim($('#reg_course').val());
        var yearlevel        = $.trim($('#reg_yearlevel').val());
        var password         = $.trim($('#reg_password').val());
        var password_confirm = $.trim($('#reg_password_confirm').val());

        // client side validation
        if (firstname === '' || lastname === '' || email === '' ||
            course    === '' || yearlevel === '' || password === '') {
            $('#reg_msg').html('<div class="alert alert-danger py-2 small">Please fill in all fields.</div>');
            return false;
        }

        if (password !== password_confirm) {
            $('#reg_msg').html('<div class="alert alert-danger py-2 small">Passwords do not match.</div>');
            return false;
        }

        $.ajax({
            type    : 'POST',
            url     : mesiteurl + 'Borrowing-System',
            data    : {
                meaction             : 'DO-REGISTER',
                reg_firstname        : firstname,
                reg_lastname         : lastname,
                reg_email            : email,
                reg_course           : course,
                reg_yearlevel        : yearlevel,
                reg_password         : password,
                reg_password_confirm : password_confirm
            },
            dataType : 'json',
            success  : function (data) {
                if (data.status === 'ok') {
                    $('#reg_msg').html('<div class="alert alert-success py-2 small">Account created! Redirecting...</div>');
                    setTimeout(function () {
                        window.location.href = mesiteurl + 'Borrowing-System';
                    }, 1500);
                } else {
                    $('#reg_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                }
            },
            error : function () {
                $('#reg_msg').html('<div class="alert alert-danger py-2 small">Something went wrong. Try again.</div>');
            }
        });
    });
    };

    this.loadDashboard = function () {
        $.ajax({
            type     : 'POST',
            url      : mesiteurl + 'Borrowing-System',
            data     : { meaction : 'GET-DASHBOARD-STATS' },
            dataType : 'json',
            success  : function (data) {
                console.log('Dashboard data:', data); // ← ADD THIS first

                // ✅ check if recent exists before using it
                if (!data.recent) {
                    console.error('recent is missing from response');
                    return;
                }

                // stat cards
                $('#stat_active').text(data.active);
                $('#stat_total').text(data.total);
                $('#stat_available').text(data.available);

                // recent borrowings table
                var html = '';
                if (data.recent.length === 0) {
                    html = '<tr><td colspan="4" class="text-center text-muted py-3"><small>No borrowings yet.</small></td></tr>';
                } else {
                    $.each(data.recent, function (i, row) {
                        var badge = '';
                        if (row.status == 'Active') {
                            badge = '<span class="badge" style="background:#f0faf0; color:#3a7d44; font-size:11px;">Active</span>';
                        } else if (row.status == 'Overdue') {
                            badge = '<span class="badge" style="background:#fff8f0; color:#b35900; font-size:11px;">Overdue</span>';
                        } else {
                            badge = '<span class="badge" style="background:#f5f5f5; color:#888; font-size:11px;">Returned</span>';
                        }
                        html += '<tr>' +
                            '<td>' + row.tool_name  + '</td>' +
                            '<td class="text-muted">' + row.borrowed_at + '</td>' +
                            '<td class="text-muted">' + row.due_date    + '</td>' +
                            '<td>' + badge + '</td>' +
                            '</tr>';
                    });
                }
                $('#recent_borrowings').html(html);
            },
            error : function (xhr) {
                console.error('AJAX error:', xhr.responseText); // ← ADD THIS
            }
        });
    };



    this.loadTools = function () {
        var self = this;

        // load tools on page load
        self.fetchTools('');

        // search
        $(document).on('keyup', '#search_tools', function () {
            var term = $.trim($(this).val());
            self.fetchTools(term);
        });

        // open borrow modal
        $(document).on('click', '.btnBorrow', function () {
            var tool_code = $(this).data('tool_code');
            var tool_name = $(this).data('tool_name');

            $('#modal_tool_code').val(tool_code);
            $('#modal_tool_name').text(tool_name);
            $('#modal_msg').html('');
            $('#borrow_due_date').val('');

            var modal = new bootstrap.Modal(document.getElementById('modalBorrow'));
            modal.show();
        });

        // confirm borrow
        $(document).on('click', '#btnConfirmBorrow', function () {
            var tool_code = $('#modal_tool_code').val();
            var due_date  = $('#borrow_due_date').val();

            if (due_date === '') {
                $('#modal_msg').html('<div class="alert alert-danger py-2 small">Please select a due date.</div>');
                return false;
            }

            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : {
                    meaction  : 'DO-BORROW',
                    tool_code : tool_code,
                    due_date  : due_date
                },
                dataType : 'json',
                success  : function (data) {
                    if (data.status === 'ok') {
                        $('#modal_msg').html('<div class="alert alert-success py-2 small">Borrowed successfully!</div>');
                        setTimeout(function () {
                            bootstrap.Modal.getInstance(document.getElementById('modalBorrow')).hide();
                            self.fetchTools('');
                        }, 1500);
                    } else {
                        $('#modal_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                    }
                },
                error : function () {
                    $('#modal_msg').html('<div class="alert alert-danger py-2 small">Something went wrong.</div>');
                }
            });
        });
    };

    this.fetchTools = function (term) {
        $.ajax({
            type     : 'POST',
            url      : mesiteurl + 'Borrowing-System',
            data     : { meaction : 'GET-TOOLS', term : term },
            dataType : 'json',
            success  : function (data) {
                console.log('GET-TOOLS response:', data); // ← ADD
                var html = '';

                if (data.length === 0) {
                    html = '<div class="col-12 text-center text-muted py-4"><small>No tools found.</small></div>';
                } else {
                    $.each(data, function (i, tool) {
                        var available  = parseInt(tool.available);
                        var badge      = available > 0
                            ? '<span style="background:#f0faf0; color:#3a7d44; font-size:11px; padding:2px 8px; border-radius:4px;">Available</span>'
                            : '<span style="background:#fff0f0; color:#b33a3a; font-size:11px; padding:2px 8px; border-radius:4px;">Unavailable</span>';
                        var btn        = available > 0
                            ? '<button class="btn btn-sm btn-dark btnBorrow" style="font-size:12px;" data-tool_code="' + tool.tool_code + '" data-tool_name="' + tool.tool_name + '">Borrow</button>'
                            : '<button class="btn btn-sm btn-secondary" style="font-size:12px;" disabled>Borrow</button>';

                        html += '<div class="col-md-4">' +
                            '<div class="card border-0 h-100" style="border: 0.5px solid #e9e9e9 !important;">' +
                            '<div class="card-body">' +
                            '<div class="d-flex justify-content-between align-items-start mb-2">' +
                            '<p class="small fw-500 mb-0">' + tool.tool_name + '</p>' +
                            badge +
                            '</div>' +
                            '<p class="small text-muted mb-3">' + tool.description + '</p>' +
                            '<div class="d-flex justify-content-between align-items-center">' +
                            '<small class="text-muted">' + tool.available + ' available</small>' +
                            btn +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                    });
                }
                $('#tools_list').html(html);
            },
            error : function () {
                console.log('GET-TOOLS error:', xhr.responseText); // ← ADD
                $('#tools_list').html('<div class="col-12 text-center text-muted">Error loading tools.</div>');
            }
        });
    };
    

};

// Initialize on DOM ready
$(document).ready(function () {
    var me = new BrwngSys();
    me.doLogin();
    me.doRegister();

    console.log('tools_list found:', $('#tools_list').length);  // should be 1
    console.log('stat_active found:', $('#stat_active').length); // should be 0 on browse page

    if ($('#stat_active').length > 0)  { me.loadDashboard(); }
    if ($('#tools_list').length > 0)   { me.loadTools(); }    // ← add this
});