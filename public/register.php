<?php
session_start();
require_once '../config/config.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if(isset($_POST['register'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    // Validate mobile number
    if (empty($mobile)) {
        $error = "Mobile number is required!";
    } else if (!preg_match("/^\d{10}$/", $mobile)) {
        $error = "Please enter a valid 10-digit mobile number!";
    } else {
        // Check if email already exists
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        
        if(mysqli_num_rows($result) > 0) {
            $error = "Email already exists!";
        } else {
            // Insert new user
            $sql = "INSERT INTO users (name, email, password, mobile, location, is_verified, created_at) 
                    VALUES ('$name', '$email', '$password', '$mobile', '$location', 0, NOW())";
            
            if(mysqli_query($conn, $sql)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Registration successful!']);
                } else {
                    $_SESSION['success'] = "Registration successful! Please login.";
                    header("Location: login.php");
                }
                exit();
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json', true, 400);
                    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
                    exit();
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Register</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <div id="registerAlert" class="alert d-none" role="alert"></div>

                        <form method="POST" action="" id="registerForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="mobile" name="mobile" required pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number">
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required placeholder="Enter your district">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="register" id="registerBtn" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        function showRegisterAlert(message, type = 'danger') {
            const alertDiv = document.getElementById('registerAlert');
            alertDiv.className = 'alert alert-' + type;
            alertDiv.textContent = message;
            alertDiv.classList.remove('d-none');
        }
        // Initialize autocomplete for location
        $(document).ready(function() {
            $.getJSON('data/districts.json', function(data) {
                $("#location").autocomplete({
                    source: data.districts,
                    minLength: 1,
                    select: function(event, ui) {
                        $(this).val(ui.item.value);
                        return false;
                    }
                });
            });

            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                const password = $('#password').val();
                const confirmPassword = $('#confirm_password').val();
                if (password !== confirmPassword) {
                    showRegisterAlert('Passwords do not match!', 'danger');
                    return;
                }

                const btn = $('#registerBtn');
                const alertDiv = $('#registerAlert');
                btn.prop('disabled', true)
                   .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Registering...');
                alertDiv.addClass('d-none').removeClass('alert-danger alert-success').text('');
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json'
                }).done(function(res) {
                    if (res.success) {
                        alertDiv.removeClass('d-none alert-danger').addClass('alert-success').text(res.message);
                        setTimeout(function(){ window.location.href = 'login.php'; }, 1500);
                    } else {
                        alertDiv.removeClass('d-none').addClass('alert-danger').text(res.message || 'Registration failed');
                    }
                }).fail(function() {
                    alertDiv.removeClass('d-none').addClass('alert-danger').text('An error occurred. Please try again.');
                }).always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-user-plus me-1"></i>Register');
                });
            });
        });
    </script>
</body>
</html>
