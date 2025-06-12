<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query admin_user table for matching email
    $stmt = mysqli_prepare($conn, "SELECT id, password FROM admin_user WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_bind_result($stmt, $admin_id, $hashed_password);
        mysqli_stmt_fetch($stmt);
        // If password is hashed, use password_verify. If plain, compare directly.
        if (password_verify($password, $hashed_password) || $password === $hashed_password) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin_id;
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/site-styles.css"> <!-- Link to your site's CSS -->
    <link rel="stylesheet" href="styles.css"> <!-- Additional admin-specific styles -->
</head>
<body>
    <main class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="" class="form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </main>
</body>
</html>
