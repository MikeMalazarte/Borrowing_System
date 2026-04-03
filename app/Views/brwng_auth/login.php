<!-- app/Views/brwng/login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Engineering Borrowing System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f5f5f5;
        }
        .login-card {
            max-width: 380px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card login-card">
        <div class="card-body p-4">

            <!-- Header -->
            <div class="text-center mb-4">
                <small class="text-muted d-block">Engineering Tools</small>
                <h5 class="fw-normal mb-0">Borrowing System</h5>
            </div>

            <!-- Error message placeholder -->
            <div id="login_msg"></div>

            <!-- Form -->
            <div class="mb-3">
                <label class="form-label small text-muted">Email</label>
                <input type="email" class="form-control form-control-sm"
                    id="login_email" placeholder="your@email.com">
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">Password</label>
                <input type="password" class="form-control form-control-sm"
                    id="login_password" placeholder="Enter Password">
            </div>

            <div class="d-grid mb-3">
                <button class="btn btn-dark btn-sm" id="btnLogin">
                    Sign in
                </button>
            </div>

            <p class="text-center small text-muted mb-0">
                Don't have an account?
                <a href="<?= base_url('Borrowing-System?meaction=REGISTER') ?>"
                    class="text-dark">Register</a>
            </p>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var mesiteurl = "<?= base_url() ?>"; 
</script>
<script src="<?= base_url('assets/js/brwng/brwng.js') ?>"></script>



</body>
</html>