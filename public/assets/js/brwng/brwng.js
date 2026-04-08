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
        var self = this;

        // open return modal
        $(document).on('click', '.btnReturn', function () {
            var brw_code  = $(this).data('brw_code');
            var tool_name = $(this).data('tool_name');
            $('#return_brw_code').val(brw_code);
            $('#return_tool_name').text(tool_name);
            $('#return_msg').html('');
            var modal = new bootstrap.Modal(document.getElementById('modalReturn'));
            modal.show();
        });

        // confirm return
        $(document).on('click', '#btnConfirmReturn', function () {
            var brw_code = $('#return_brw_code').val();
            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : { meaction : 'DO-RETURN', brw_code : brw_code },
                dataType : 'json',
                success  : function (data) {
                    if (data.status === 'ok') {
                        $('#return_msg').html('<div class="alert alert-success py-2 small">Returned successfully!</div>');
                        setTimeout(function () {
                            bootstrap.Modal.getInstance(document.getElementById('modalReturn')).hide();
                            self.loadDashboard();
                        }, 1500);
                    } else {
                        $('#return_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                    }
                },
                error : function () {
                    $('#return_msg').html('<div class="alert alert-danger py-2 small">Something went wrong.</div>');
                }
            });
        });

        // load stat cards via JSON
        $.ajax({
            type     : 'POST',
            url      : mesiteurl + 'Borrowing-System',
            data     : { meaction : 'GET-DASHBOARD-STATS' },
            dataType : 'json',
            success  : function (data) {
                $('#stat_active').text(data.active);
                $('#stat_total').text(data.total);
                $('#stat_available').text(data.available);
            },
            error : function (xhr) {
                console.error('Stats error:', xhr.responseText);
            }
        });

        // load table rows from PHP view — no HTML in JS
        $.ajax({
            type    : 'POST',
            url     : mesiteurl + 'Borrowing-System',
            data    : { meaction : 'GET-DASHBOARD-RECS' },
            success : function (html) {
                $('#recent_borrowings').html(html); // ← directly insert PHP rendered HTML
            },
            error : function () {
                $('#recent_borrowings').html('<tr><td colspan="6" class="text-center text-muted">Error loading data.</td></tr>');
            }
        });
    };


    this.loadTools = function () {
        var self = this;

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
            $('#borrow_time_from').val('');
            $('#borrow_time_to').val('');
            $('#borrow_due_date').val('');

            // same date allowed — min due = borrow date
            var today = new Date().toISOString().split('T')[0];
            $('#borrow_date').val(today);
            $('#borrow_due_date').attr('min', today); 

            var modal = new bootstrap.Modal(document.getElementById('modalBorrow'));
            modal.show();
        });

        // when borrow date changes → due date min = same borrow date
        $(document).on('change', '#borrow_date', function () {
            var borrowDate = $(this).val();
            $('#borrow_due_date').attr('min', borrowDate); 
            $('#borrow_due_date').val('');
        });

        // confirm borrow
        $(document).on('click', '#btnConfirmBorrow', function () {
            var tool_code   = $('#modal_tool_code').val();
            var borrow_date = $('#borrow_date').val();
            var time_from   = $('#borrow_time_from').val();
            var time_to     = $('#borrow_time_to').val();
            var due_date    = $('#borrow_due_date').val();

            if (borrow_date === '' || time_from === '' || time_to === '' || due_date === '') {
                $('#modal_msg').html('<div class="alert alert-danger py-2 small">Please fill in all fields.</div>');
                return false;
            }

            if (time_from >= time_to) {
                $('#modal_msg').html('<div class="alert alert-danger py-2 small">Time From must be earlier than Time To.</div>');
                return false;
            }

            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : {
                    meaction    : 'DO-BORROW',
                    tool_code   : tool_code,
                    borrow_date : borrow_date,
                    time_from   : time_from,
                    time_to     : time_to,
                    due_date    : due_date
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
                var html = '';

                if (data.length === 0) {
                    html = '<div class="col-12 text-center text-muted py-4"><small>No tools found.</small></div>';
                } else {
                    $.each(data, function (i, tool) {
                        var available = parseInt(tool.available);
                        var badge     = available > 0
                            ? '<span style="background:#f0faf0; color:#3a7d44; font-size:11px; padding:2px 8px; border-radius:4px;">Available</span>'
                            : '<span style="background:#fff0f0; color:#b33a3a; font-size:11px; padding:2px 8px; border-radius:4px;">Unavailable</span>';
                        var btn       = available > 0
                            ? '<button class="btn btn-sm btn-dark btnBorrow" style="font-size:12px;" '
                            + 'data-tool_code="' + tool.tool_code + '" '
                            + 'data-tool_name="' + tool.tool_name + '">Borrow</button>'
                            : '<button class="btn btn-sm btn-secondary" style="font-size:12px;" disabled>Borrow</button>';

                        html += '<div class="col-md-4">'
                            + '<div class="card border-0 h-100" style="border: 0.5px solid #e9e9e9 !important;">'
                            + '<div class="card-body">'
                            + '<div class="d-flex justify-content-between align-items-start mb-2">'
                            + '<p class="small fw-500 mb-0">' + tool.tool_name + '</p>'
                            + badge
                            + '</div>'
                            + '<p class="small text-muted mb-3">' + tool.description + '</p>'
                            + '<div class="d-flex justify-content-between align-items-center">'
                            + '<small class="text-muted">' + tool.available + ' available</small>'
                            + btn
                            + '</div>'
                            + '</div>'
                            + '</div>'
                            + '</div>';
                    });
                }
                $('#tools_list').html(html);
            },
            error : function () {
                $('#tools_list').html('<div class="col-12 text-center text-muted">Error loading tools.</div>');
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

    this.changePass = function () {
        $(document).on('click', '#btnConfirmChangePassword', function () {
            var current_password  = $.trim($('#current_password').val());
            var new_password      = $.trim($('#new_password').val());
            var confirm_password  = $.trim($('#confirm_password').val());

            // Client-side validation
            if (current_password === '' || new_password === '' || confirm_password === '') {
                $('#change_pw_msg').html('<div class="alert alert-danger py-2 small">Please fill in all fields.</div>');
                return false;
            }

            if (new_password !== confirm_password) {
                $('#change_pw_msg').html('<div class="alert alert-danger py-2 small">New passwords do not match.</div>');
                return false;
            }

            if (new_password === current_password) {
                $('#change_pw_msg').html('<div class="alert alert-danger py-2 small">New password must differ from current password.</div>');
                return false;
            }

            $.ajax({
                type     : 'POST',
                url      : mesiteurl + 'Borrowing-System',
                data     : {
                    meaction         : 'DO-CHANGE-PASSWORD',
                    current_password : current_password,
                    new_password     : new_password
                },
                dataType : 'json',
                success  : function (data) {
                    if (data.status === 'ok') {
                        $('#change_pw_msg').html('<div class="alert alert-success py-2 small">Password updated successfully!</div>');
                        setTimeout(function () {
                            bootstrap.Modal.getInstance(document.getElementById('modalChangePassword')).hide();
                            $('#current_password, #new_password, #confirm_password').val('');
                            $('#change_pw_msg').html('');
                        }, 1500);
                    } else {
                        // Server says current password is wrong
                        $('#change_pw_msg').html('<div class="alert alert-danger py-2 small">' + data.message + '</div>');
                    }
                },
                error : function () {
                    $('#change_pw_msg').html('<div class="alert alert-danger py-2 small">Something went wrong. Try again.</div>');
                }
            });
        });
    };  

};

// Initialize on DOM ready
$(document).ready(function () {
    var me = new BrwngSys();
    me.doLogin();
    me.doRegister();
    me.doLogout();
    me.changePass();

    if ($('#stat_active').length > 0)  { me.loadDashboard(); }
    if ($('#tools_list').length > 0)   { me.loadTools(); }    
});