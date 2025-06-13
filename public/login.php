<?php
session_start();
require_once '../config/config.php';
use App\Models\UserAuth;

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if(isset($_POST['login'])) {
    try {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $userAuth = new UserAuth($conn);
        $result = $userAuth->login($email, $password);
        if ($result) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header("Location: dashboard.php");
            }
            exit();
        }
    } catch (Exception $e) {
        if ($isAjax) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit();
        } else {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include '../src/Views/header.php'; ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <div id="loginAlert" class="alert d-none" role="alert" aria-live="assertive"></div>
                        <form method="POST" action="" id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            <button type="submit" name="login" id="loginBtn" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#loginBtn');
            const alertDiv = $('#loginAlert');
            const email = $('#email').val().trim();
            const password = $('#password').val().trim();
            if (!email) {
                alertDiv.removeClass('d-none alert-success').addClass('alert-danger').text('Please enter your email address.');
                return;
            }
            if (!password) {
                alertDiv.removeClass('d-none alert-success').addClass('alert-danger').text('Please enter your password.');
                return;
            }
            btn.prop('disabled', true)
               .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Logging in...');
            alertDiv.addClass('d-none').removeClass('alert-danger');
            $.ajax({
                url: '',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function(res) {
                if (res.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    alertDiv.removeClass('d-none').addClass('alert-danger').text(res.message || 'Invalid email or password.');
                }
            }).fail(function() {
                alertDiv.removeClass('d-none').addClass('alert-danger').text('Unable to process the login at this time.');
            }).always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt me-1"></i>Login');
            });
        });
    });
    </script>
</body>
</html>
