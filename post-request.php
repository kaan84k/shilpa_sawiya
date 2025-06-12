<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and verify it's working
session_start();

// Debug: Check if session is being maintained
if (session_status() === PHP_SESSION_NONE) {
    die('Session not started');
}

// Debug: Log session ID
if (empty(session_id())) {
    die('No session ID');
}

error_log('Session ID: ' . session_id());
require_once 'config/database.php';
require_once 'includes/UserAuth.php';
require_once 'includes/Request.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Debug: Check session and user_id
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Error: User not properly logged in. Please login again.");
}

$user_id = (int)$_SESSION['user_id'];

// Debug: Verify user_id is valid
if ($user_id <= 0) {
    die("Error: Invalid user ID. Please login again.");
}

$request = new Request($conn);

// Debug: Log session and POST data
error_log("Session data: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

// Handle request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug: Log user_id before creating request
        error_log("Creating request with user_id: " . $user_id);
        $title = $_POST['title'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        
        $request_id = $request->createCustomRequest(
            $user_id,
            $title,
            $description,
            $category
        );
        
        $_SESSION['success'] = "Request posted successfully!";
        header("Location: my-requests.php");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Request - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php
    // Get user details
    $userAuth = new UserAuth($conn);
    $user = $userAuth->getUserById($_SESSION['user_id']);
    ?>


<nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-reader me-2"></i>
                <span class="brand-text">Shilpa Sawiya</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="donations.php">
                        <i class="fa-solid fa-book"></i>Donations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="public-requests.php">
                        <i class="fa-solid fa-book-open"></i>Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                        <i class="fa-solid fa-address-card"></i>About
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user me-1"></i>Welcome, <?php echo htmlspecialchars($user['name']); ?>
                        </span>
                     </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Profile</a>
                    <a href="my-donations.php" class="list-group-item list-group-item-action">My Donations</a>
                    <a href="my-requests.php" class="list-group-item list-group-item-action">My Requests</a>
                    <a href="notifications.php" class="list-group-item list-group-item-action">My Notifications</a>
                </div>
            </div>
            <div class="col-md-9">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h4>Post Request</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                                <small class="text-muted">e.g., "Grade 10 Math Book"</small>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                <small class="text-muted">Describe what you need and any specific requirements</small>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select category</option>
                                    <option value="Books">Books</option>
                                    <option value="Stationery">Stationery</option>
                                    <option value="Uniforms">Uniforms</option>
                                    <option value="Bags">Bags</option>
                                    <option value="Electronics">Electronics</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
