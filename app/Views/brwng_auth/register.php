<!-- app/Views/brwng/register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Engineering Borrowing System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f5f5f5; }
        .register-card { max-width: 420px; width: 100%; }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100 py-4">
    <div class="card register-card">
        <div class="card-body p-4">

            <!-- Header -->
            <div class="text-center mb-4">
                <small class="text-muted d-block">Engineering Tools</small>
                <h5 class="fw-normal mb-0">Create Account</h5>
            </div>

            <!-- Message placeholder -->
            <div id="reg_msg"></div>

            <!-- Name row -->
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label small text-muted">First Name</label>
                    <input type="text" class="form-control form-control-sm"
                        id="reg_firstname" placeholder="Juan">
                </div>
                <div class="col-6">
                    <label class="form-label small text-muted">Last Name</label>
                    <input type="text" class="form-control form-control-sm"
                        id="reg_lastname" placeholder="Dela Cruz">
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label small text-muted">Email</label>
                <input type="email" class="form-control form-control-sm"
                    id="reg_email" placeholder="your@email.com">
            </div>

            <!-- Course and Year Level -->
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label small text-muted">Course</label>
                    <select class="form-select form-select-sm" id="reg_course">
                        <option value="">Select course</option>
                        <option value="BSCE">BSCE</option>
                        <option value="BSEE">BSEE</option>
                        <option value="BSME">BSME</option>
                        <option value="BSECE">BSECE</option>
                        <option value="BSIE">BSIE</option>
                        <option value="BSCpE">BSCpE</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small text-muted">Year Level</label>
                    <select class="form-select form-select-sm" id="reg_yearlevel">
                        <option value="">Select year</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label small text-muted">Password</label>
                <input type="password" class="form-control form-control-sm"
                    id="reg_password" placeholder="••••••••">
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label class="form-label small text-muted">Confirm Password</label>
                <input type="password" class="form-control form-control-sm"
                    id="reg_password_confirm" placeholder="••••••••">
            </div>

            <!-- Submit -->
            <div class="d-grid mb-3">
                <button class="btn btn-dark btn-sm" id="btnRegister">
                    Create Account
                </button>
            </div>

            <p class="text-center small text-muted mb-0">
                Already have an account?
                <a href="<?= base_url('Borrowing-System') ?>" class="text-dark">Sign in</a>
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