<?php
session_start();
require_once '../config/config.php';
use App\Models\UserAuth;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Get user details
$userAuth = new UserAuth($conn);
$user = $userAuth->getUserById($_SESSION['user_id']);
// Handle marking notifications as read
if (isset($_POST['mark_read'])) {
    $notification_id = $_POST['notification_id'];
    $query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $notification_id, $_SESSION['user_id']);
    $stmt->execute();
// Handle deleting notifications
if (isset($_POST['delete'])) {
    $query = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <?php
// Get user details (already included at the top)
<nav class="navbar navbar-expand-lg sticky-top" role="navigation" aria-label="Main navigation">
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
                        <i class="fa-solid fa-book me-1"></i>Donations
                    </a>
                </li>
                    <a class="nav-link" href="public-requests.php">
                        <i class="fa-solid fa-book-open me-1"></i>Requests
                    <a class="nav-link" href="#about">
                        <i class="fa-solid fa-address-card me-1"></i>About
            </ul>
            <ul class="navbar-nav">
                    <span class="nav-link">
                        <i class="fas fa-user me-1"></i>Welcome, <?php echo htmlspecialchars($user['name']); ?>
                    </span>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
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
                    <a href="notifications.php" class="list-group-item list-group-item-action active">My Notifications</a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4>My Notifications</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get notifications
                        $query = "SELECT n.*, u.name as requester_name 
                                 FROM notifications n 
                                 LEFT JOIN users u ON n.user_id = u.id 
                                 WHERE n.user_id = ? 
                                 ORDER BY n.created_at DESC";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $_SESSION['user_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($notification = $result->fetch_assoc()) {
                                $class = $notification['is_read'] ? 'alert-secondary' : 'alert-info';
                                echo '<div class="alert ' . $class . ' mb-2" role="alert">';
                                echo '<div class="d-flex justify-content-between align-items-center">';
                                echo '<h5 class="mb-1">' . htmlspecialchars($notification['title']) . '</h5>';
                                echo '<small class="text-muted">' . date('Y-m-d H:i', strtotime($notification['created_at'])) . '</small>';
                                echo '</div>';
                                echo '<p class="mb-2">' . nl2br(htmlspecialchars($notification['message'])) . '</p>';
                                
                                // Action buttons
                                echo '<div class="d-flex gap-2">
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="notification_id" value="' . $notification['id'] . '">
                                            <button type="submit" name="mark_read" class="btn btn-sm btn-outline-primary">Mark as Read</button>
                                        </form>
                                            <button type="submit" name="delete" class="btn btn-sm btn-outline-danger">Delete</button>
                                      </div>';
                            }
                        } else {
                            echo '<div class="alert alert-info">No notifications yet.</div>';
                        }
                        ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
