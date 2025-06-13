<?php
session_start();
require_once '../config/config.php';
use App\Models\UserAuth;
use App\Models\Request;
use App\Models\Donation;

// Get user donations
$donation = new Donation($conn);
$user_donations = $donation->getUserDonations($_SESSION['user_id']);
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Get user details
$userAuth = new UserAuth($conn);
$user = $userAuth->getUserById($_SESSION['user_id']);
// Handle profile update
if(isset($_POST['update_profile'])) {
    try {
        $name = $_POST['name'] ?? null;
        $mobile = $_POST['mobile'] ?? null;
        $password = $_POST['password'] ?? null;
        
        $userAuth->updateProfile($_SESSION['user_id'], $name, $mobile, $password);
        $_SESSION['success'] = "Profile updated successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header("Location: dashboard.php");
// Handle new request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_request'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $category = $_POST['category'];
        // Validate input
        if (empty($title) || empty($category)) {
            throw new Exception("Title and category are required");
        }
        // Create request
        $request = new Request($conn);
        $request_id = $request->createCustomRequest(
            $_SESSION['user_id'],
            $title,
            $description,
            $category
        );
        $_SESSION['success'] = "Request created successfully!";
        header("Location: dashboard.php#requests-tab");
        exit();
// Handle notification actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Mark single notification as read
        if (isset($_POST['mark_read']) && isset($_POST['notification_id'])) {
            $notification_id = $_POST['notification_id'];
            $query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $notification_id, $_SESSION['user_id']);
            $stmt->execute();
            $_SESSION['success'] = "Notification marked as read";
        } 
        // Mark all notifications as read
        elseif (isset($_POST['mark_all_read'])) {
            $query = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
            $stmt->bind_param("i", $_SESSION['user_id']);
            $_SESSION['success'] = "All notifications marked as read";
        // Delete notification
        elseif (isset($_POST['delete']) && isset($_POST['notification_id'])) {
            $query = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
            $_SESSION['success'] = "Notification deleted";
        header("Location: dashboard.php#notifications");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Profile picture styles */
        .profile-picture-container {
            position: relative;
            display: inline-block;
        .profile-picture-container:hover .profile-picture-overlay {
            opacity: 1;
        .profile-picture-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        .profile-picture-overlay i {
            color: white;
            font-size: 1.5rem;
        /* Navbar dropdown styles */
        .dropdown-menu {
            min-width: 12rem;
        /* Ensure profile picture is clickable */
        .profile-picture-label {
        /* Hide the file input but keep it accessible */
        .profile-picture-input {
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
    </style>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
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
                        <a class="nav-link" href="about.php">
                            <i class="fa-solid fa-address-card me-1"></i>About
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php 
                            $profilePic = !empty($user['profile_picture']) 
                                ? 'uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=40';
                            ?>
                            <img src="<?php echo $profilePic; ?>" class="rounded-circle me-2 img-cover" width="30" height="30">
                            <?php echo htmlspecialchars($user['name']); ?>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    <li class="nav-item d-lg-none">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
            </div>
        </div>
    </nav>
    <div class="container-fluid py-4">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="text-center mb-4">
                            <form id="profilePictureForm" action="update-profile-picture.php" method="POST" enctype="multipart/form-data">
                                <div class="profile-picture-container">
                                    <?php 
                                    $profilePic = !empty($user['profile_picture']) 
                                        ? 'uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']);
                                    ?>
                                    <img id="profileImage" src="<?php echo $profilePic; ?>" alt="Profile" class="rounded-circle mb-3 profile-image-lg">
                                    <div class="profile-picture-overlay" onclick="document.getElementById('profilePictureInput').click();">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <input type="file" id="profilePictureInput" name="profile_picture" accept="image/*" class="profile-picture-input" onchange="document.getElementById('profilePictureForm').submit();">
                                </div>
                                <div class="text-muted small">
                                    Click on the image to upload a new profile picture<br>
                                    <small>Max size: 2MB • JPG, PNG, GIF</small>
                            </form>
                            <?php if(isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <?php endif; ?>
                        </div>
                        <h5 class="my-3"><?php echo htmlspecialchars($user['name']); ?></h5>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                        <p class="text-muted mb-4"><?php echo htmlspecialchars($user['mobile']); ?></p>
                    </div>
                </div>
                    <div class="card-body p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#profile" class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-tab">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a>
                            </li>
                                <a href="#donations" class="nav-link" data-bs-toggle="tab" data-bs-target="#donations-tab">
                                    <i class="fas fa-book me-2"></i>My Donations
                                <a href="#requests" class="nav-link" data-bs-toggle="tab" data-bs-target="#requests-tab">
                                    <i class="fas fa-book-open me-2"></i>My Requests
                                <a href="#notifications" class="nav-link" data-bs-toggle="tab" data-bs-target="#notifications-tab">
                                    <i class="fas fa-bell me-2"></i>Notifications
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile-tab">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Profile Information</h5>
                                <button class="btn btn-sm btn-outline-primary" onclick="editProfile()">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="" id="profileForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
                                        </div>
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" readonly 
                                                value="<?php echo htmlspecialchars($user['email']); ?>">
                                            <label for="mobile" class="form-label">Mobile Number</label>
                                            <input type="tel" class="form-control" id="mobile" name="mobile" 
                                                value="<?php echo htmlspecialchars($user['mobile']); ?>" disabled>
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" disabled>
                                            <small class="text-muted">Leave blank to keep current password</small>
                                    <div class="d-none" id="profileActions">
                                        <button type="submit" name="update_profile" class="btn btn-primary me-2">
                                            <i class="fas fa-save me-1"></i>Save Changes
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="cancelEdit()">
                                            Cancel
                                </form>
                    <!-- Donations Tab -->
                    <div class="tab-pane fade" id="donations-tab">
                            <div class="card-header">
                                <h5 class="mb-0">My Donations</h5>
                                <?php if(isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <?php endif; ?>
                                <?php if(isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h2 class="mb-0">My Donations</h2>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#donationModal">
                                        <i class="fas fa-plus me-2"></i>Donate Now
                                    </button>
                                <?php
                                $donation = new Donation($conn);
                                $user_donations = $donation->getUserDonations($_SESSION['user_id']);
                                ?>
                                <?php if(empty($user_donations)): ?>
                                    <div class="alert alert-info">
                                        You haven't posted any donations yet. <a href="donate.php">Post your first donation</a>!
                                <?php else: ?>
                                    <div class="row mt-4">
                                        <?php foreach($user_donations as $donation): ?>
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card shadow-sm h-100">
                                                    <?php if(!empty($donation['image'])): ?>
                                                        <img src="uploads/donations/<?php echo htmlspecialchars($donation['image']); ?>" class="card-img-top img-cover h-200" alt="<?php echo htmlspecialchars($donation['title']); ?>">
                                                    <?php else: ?>
                                                        <div class="bg-light d-flex align-items-center justify-content-center h-200">
                                                            <i class="fas fa-book fa-4x text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($donation['title']); ?></h5>
                                                            <span class="badge px-3 py-2 rounded-pill <?php 
                                                                switch($donation['status']) {
                                                                    case 'available':
                                                                        echo 'bg-success';
                                                                        break;
                                                                    case 'requested':
                                                                        echo 'bg-warning';
                                                                    case 'completed':
                                                                        echo 'bg-info';
                                                                    default:
                                                                        echo 'bg-secondary';
                                                                }
                                                            ?>">
                                                                <?php echo ucfirst($donation['status']); ?>
                                                            </span>
                                                        <?php
                                                            // Define category icons and colors
                                                            $category_icons = [
                                                                'Books' => ['icon' => 'book', 'color' => 'info'],
                                                                'Stationery' => ['icon' => 'pencil-alt', 'color' => 'success'],
                                                                'Uniforms' => ['icon' => 'tshirt', 'color' => 'primary'],
                                                                'Bags' => ['icon' => 'briefcase', 'color' => 'warning'],
                                                                'Electronics' => ['icon' => 'laptop', 'color' => 'danger']
                                                            ];
                                                            $category_icon = $category_icons[$donation['category']] ?? ['icon' => 'box', 'color' => 'secondary'];
                                                        ?>
                                                        <div class="mb-2">
                                                            <span class="badge bg-<?php echo $category_icon['color']; ?> bg-opacity-10 text-<?php echo $category_icon['color']; ?> px-3 py-2 rounded-pill">
                                                                <i class="fas fa-<?php echo $category_icon['icon']; ?> me-1"></i>
                                                                <?php echo htmlspecialchars($donation['category']); ?>
                                                        <p class="card-text mb-3">
                                                            <?php 
                                                            $description = htmlspecialchars($donation['description']);
                                                            echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                                            ?>
                                                        </p>
                                                        <p class="card-text">
                                                            <small class="text-muted">
                                                                <i class="far fa-calendar-alt me-1"></i> <?php echo date('M d, Y', strtotime($donation['created_at'])); ?>
                                                                <?php if(!empty($donation['location'])): ?>
                                                                    <span class="ms-3">
                                                                        <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($donation['location']); ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </small>
                                                    </div>
                                                    <div class="card-footer bg-transparent border-top-0 pt-0">
                                                        <?php if($donation['status'] !== 'completed'): ?>
                                                            <div class="d-flex gap-2">
                                                                <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1 edit-donation" 
                                                                    data-donation-id="<?php echo $donation['id']; ?>"
                                                                    data-title="<?php echo htmlspecialchars($donation['title']); ?>"
                                                                    data-description="<?php echo htmlspecialchars($donation['description']); ?>"
                                                                    data-category="<?php echo htmlspecialchars($donation['category']); ?>"
                                                                    data-condition="<?php echo htmlspecialchars($donation['condition']); ?>"
                                                                    data-location="<?php echo htmlspecialchars($donation['location']); ?>">
                                                                    <i class="fas fa-edit me-1"></i> Edit
                                                                </button>
                                                                <button type="button" class="btn btn-outline-danger btn-sm delete-donation" 
                                                                    data-donation-title="<?php echo htmlspecialchars($donation['title']); ?>">
                                                                    <i class="fas fa-trash"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                    <!-- Requests Tab -->
                    <div class="tab-pane fade" id="requests-tab">
                        <?php
                        $request = new Request($conn);
                        $user_requests = $request->getUserRequests($_SESSION['user_id']);
                        ?>
                        
                                <h5 class="mb-0">My Book Requests</h5>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRequestModal">
                                    <i class="fas fa-plus me-1"></i>New Request
                                <?php if(empty($user_requests)): ?>
                                        You haven't posted any requests yet. <a href="post-request.php">Post your first request</a>!
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Request Title</th>
                                                    <th>Donation</th>
                                                    <th>Category</th>
                                                    <th>Status</th>
                                                    <th>Created At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($user_requests as $req): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($req['title']); ?></td>
                                                        <td>
                                                            <?php if (!empty($req['donation_id'])): ?>
                                                                <a href="view-donation.php?id=<?php echo $req['donation_id']; ?>">
                                                                    <?php echo htmlspecialchars($req['donation_title'] ?? 'View Donation'); ?>
                                                                </a>
                                                            <?php else: ?>
                                                                Public Request
                                                            <?php endif; ?>
                                                        </td>
                                                            // Show donation category for donation requests, otherwise show request category
                                                            echo htmlspecialchars(!empty($req['donation_category']) ? $req['donation_category'] : ($req['category'] ?? 'N/A')); 
                                                            <span class="badge bg-<?php 
                                                                echo $req['status'] === 'pending' ? 'warning' : 
                                                                ($req['status'] === 'fulfilled' ? 'success' : 'secondary');
                                                                <?php echo ucfirst($req['status']); ?>
                                                        <td><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                                                <?php if (!empty($req['donation_id']) && $req['status'] === 'pending'): ?>
                                                                    <button type="button" class="btn btn-success btn-sm fulfill-request" 
                                                                            data-request-id="<?php echo $req['id']; ?>"
                                                                            data-request-title="<?php echo htmlspecialchars($req['title']); ?>">
                                                                        <i class="fas fa-check me-1"></i>Fulfill
                                                                    </button>
                                                                <?php if ($req['status'] === 'pending'): ?>
                                                                    <button type="button" class="btn btn-danger btn-sm delete-request" data-request-id="<?php echo $req['id']; ?>" data-request-title="<?php echo htmlspecialchars($req['title']); ?>">
                                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                    <!-- Notifications Tab -->
                    <div class="tab-pane fade" id="notifications-tab">
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
                                <h5 class="mb-0">My Notifications</h5>
                                <?php if($result->num_rows > 0): ?>
                                <form method="post" class="d-inline" onsubmit="return confirm('Mark all notifications as read?');">
                                    <button type="submit" name="mark_all_read" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-check-double me-1"></i>Mark All as Read
                            <div class="card-body p-0">
                                if ($result->num_rows > 0) {
                                    while ($notification = $result->fetch_assoc()) {
                                        $bgClass = $notification['is_read'] ? 'bg-white' : 'bg-light';
                                        echo '<div class="list-group-item list-group-item-action border-0 py-3 ' . $bgClass . '">';
                                        echo '  <div class="d-flex w-100 justify-content-between">';
                                        echo '    <h6 class="mb-1 fw-bold">' . htmlspecialchars($notification['title']) . '</h6>';
                                        echo '    <small class="text-muted">' . date('M d, Y h:i A', strtotime($notification['created_at'])) . '</small>';
                                        echo '  </div>';
                                        echo '  <p class="mb-2">' . nl2br(htmlspecialchars($notification['message'])) . '</p>';
                                        
                                        // Action buttons
                                        echo '  <div class="d-flex gap-2">';
                                        if (!$notification['is_read']) {
                                            echo '    <form method="post" class="d-inline">';
                                            echo '      <input type="hidden" name="notification_id" value="' . $notification['id'] . '">';
                                            echo '      <button type="submit" name="mark_read" class="btn btn-sm btn-outline-primary">';
                                            echo '        <i class="fas fa-check me-1"></i>Mark as Read';
                                            echo '      </button>';
                                            echo '    </form>';
                                        }
                                        echo '    <button type="button" class="btn btn-sm btn-outline-danger delete-notification-btn" data-notification-id="' . $notification['id'] . '" data-bs-toggle="tooltip" title="Delete notification">';
                                        echo '      <i class="fas fa-trash me-1"></i>Delete';
                                        echo '    </button>';
                                        echo '    </form>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="text-center p-4">';
                                    echo '  <i class="far fa-bell-slash fa-3x text-muted mb-3"></i>';
                                    echo '  <p class="text-muted">No notifications yet.</p>';
                                    echo '</div>';
                                }
    </div>
    <!-- New Request Modal -->
    <div class="modal fade" id="newRequestModal" tabindex="-1" aria-labelledby="newRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="newRequestModalLabel">
                        <i class="fas fa-hand-holding-heart me-2"></i>Create New Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                <form method="POST" id="requestForm" class="needs-validation" novalidate>
                    <input type="hidden" name="create_request" value="1">
                    <div class="modal-body p-4">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <?php endif; ?>
                        <div class="row g-3">
                            <!-- Title -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title" class="form-label fw-medium">
                                        Title <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-heading text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg" id="title" name="title" 
                                               placeholder="e.g., Grade 10 Math Book" required>
                                    <div class="form-text">A clear and descriptive title for your request</div>
                            
                            <!-- Category -->
                            <div class="col-md-6">
                                    <label for="category" class="form-label fw-medium">
                                        Category <span class="text-danger">*</span>
                                            <i class="fas fa-tag text-muted"></i>
                                        <select class="form-select form-select-lg" id="category" name="category" required>
                                            <option value="" selected disabled>Select a category</option>
                                            <option value="Books">Books</option>
                                            <option value="Stationery">Stationery</option>
                                            <option value="Uniforms">Uniforms</option>
                                            <option value="Bags">Bags</option>
                                            <option value="Electronics">Electronics</option>
                                        </select>
                            <!-- Description -->
                                    <label for="description" class="form-label fw-medium">
                                        Description <span class="text-danger">*</span>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4" placeholder="Describe what you need in detail..." required></textarea>
                                    <div class="form-text">Provide a detailed description of what you're looking for</div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-paper-plane me-1"></i>Post Request
                </form>
    <!-- Donation Modal -->
    <div class="modal fade" id="donationModal" tabindex="-1" aria-labelledby="donationModalLabel" aria-hidden="true">
                    <h5 class="modal-title" id="donationModalLabel">
                        <i class="fas fa-book-medical me-2"></i>Donate a Book
                <form id="donationForm" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <?php if (isset($error)): ?>
                        <div class="row mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                                <div class="invalid-feedback">Please enter the book title.</div>
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" selected disabled>Select a category</option>
                                    <option value="Books">Books</option>
                                    <option value="Stationery">Stationery</option>
                                    <option value="Uniforms">Uniforms</option>
                                    <option value="Bags">Bags</option>
                                    <option value="Electronics">Electronics</option>
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            <div class="invalid-feedback">Please provide a description of the book.</div>
                                <label for="condition" class="form-label">Condition <span class="text-danger">*</span></label>
                                <select class="form-select" id="condition" name="condition" required>
                                    <option value="" selected disabled>Select condition</option>
                                    <option value="New">New</option>
                                    <option value="Like New">Like New</option>
                                    <option value="Very Good">Very Good</option>
                                    <option value="Good">Good</option>
                                    <option value="Acceptable">Acceptable</option>
                                <div class="invalid-feedback">Please select the book condition.</div>
                                <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                <select class="form-select" id="location" name="location" required>
                                    <option value="" selected disabled>Select your district</option>
                                    <?php
                                    $districts = json_decode(file_get_contents('data/districts.json'), true);
                                    if (isset($districts['districts'])) {
                                        foreach ($districts['districts'] as $district) {
                                            echo '<option value="' . htmlspecialchars($district) . '">' . htmlspecialchars($district) . '</option>';
                                <div class="invalid-feedback">Please select your district.</div>
                            <label for="image" class="form-label">Book Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Upload a clear photo of the book (max 5MB, optional)</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Submit Donation
    <!-- Edit Donation Modal -->
    <div class="modal fade" id="editDonationModal" tabindex="-1" aria-labelledby="editDonationModalLabel" aria-hidden="true">
                    <h5 class="modal-title" id="editDonationModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Donation
                <form id="editDonationForm" class="needs-validation" novalidate>
                    <input type="hidden" id="editDonationId" name="id">
                        <div id="editDonationAlert" class="alert d-none"></div>
                                <label for="editTitle" class="form-label">Book Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editTitle" name="title" required>
                                <label for="editCategory" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="editCategory" name="category" required>
                                    <option value="" disabled>Select a category</option>
                                    <option value="Fiction">Fiction</option>
                                    <option value="Non-Fiction">Non-Fiction</option>
                                    <option value="Textbook">Textbook</option>
                                    <option value="Academic">Academic</option>
                                    <option value="Children">Children</option>
                                    <option value="Other">Other</option>
                            <label for="editDescription" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                                <label for="editCondition" class="form-label">Condition <span class="text-danger">*</span></label>
                                <select class="form-select" id="editCondition" name="condition" required>
                                    <option value="" disabled>Select condition</option>
                                <label for="editLocation" class="form-label">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editLocation" name="location" required>
                                <div class="form-text">Start typing to search for your location</div>
                                <div class="invalid-feedback">Please provide a location.</div>
                            <label for="editImage" class="form-label">Book Image</label>
                            <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                            <div class="form-text">Upload a new photo to replace the current one (max 5MB, optional)</div>
                            <div class="mt-2" id="currentImageContainer"></div>
                            <i class="fas fa-save me-1"></i>Save Changes
    <!-- Fulfill Request Confirmation Modal -->
    <div class="modal fade" id="fulfillRequestModal" tabindex="-1" aria-labelledby="fulfillRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="fulfillRequestModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Confirm Fulfillment
                <div class="modal-body">
                    <p>Are you sure you want to mark <strong id="fulfillRequestTitle"></strong> as fulfilled?</p>
                    <p class="text-muted small">This action cannot be undone.</p>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="confirmFulfill">
                        <i class="fas fa-check me-1"></i> Yes, Mark as Fulfilled
    <!-- Delete Request Confirmation Modal -->
    <div class="modal fade" id="deleteRequestModal" tabindex="-1" aria-labelledby="deleteRequestModalLabel" aria-hidden="true">
                <div class="modal-header border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3">
                            <i class="fas fa-exclamation-circle text-danger fs-4"></i>
                        <h5 class="modal-title" id="deleteRequestModalLabel">Delete Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <p>Are you sure you want to delete the request: <strong id="requestToDeleteTitle"></strong>?</p>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteRequest" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete
    <!-- Delete Donation Confirmation Modal -->
    <div class="modal fade" id="deleteDonationModal" tabindex="-1" aria-labelledby="deleteDonationModalLabel" aria-hidden="true">
                        <h5 class="modal-title" id="deleteDonationModalLabel">Delete Donation</h5>
                    <p>Are you sure you want to delete the donation: <strong id="donationToDeleteTitle"></strong>?</p>
                    <form id="deleteDonationForm" method="post" class="d-inline">
                        <input type="hidden" name="donation_id" id="donationIdToDelete">
                        <button type="submit" name="delete_donation" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete
                    </form>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteNotificationModal" tabindex="-1" aria-labelledby="deleteNotificationModalLabel" aria-hidden="true">
                        <h5 class="modal-title" id="deleteNotificationModalLabel">Delete Notification</h5>
                    <p class="mb-0">This notification will be permanently deleted. This action cannot be undone.</p>
                    <form id="deleteNotificationForm" method="post" class="d-inline">
                        <input type="hidden" name="notification_id" id="notificationIdToDelete">
                        <button type="submit" name="delete" class="btn btn-danger">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Handle form submission
    document.addEventListener('DOMContentLoaded', function() {
        const requestForm = document.getElementById('requestForm');
        if (requestForm) {
            requestForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...';
                }
            });
    });
        // Form validation and submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('requestForm');
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                    // Show loading state
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Posting...';
                });
            }
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            // Show success/error messages
            <?php if(isset($_SESSION['success'])): ?>
                showToast('Success', '<?php echo addslashes($_SESSION['success']); ?>', 'success');
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                showToast('Error', '<?php echo addslashes($_SESSION['error']); ?>', 'error');
                <?php unset($_SESSION['error']); ?>
        });
        // Show toast notification
        function showToast(title, message, type = 'info') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
            Toast.fire({
                icon: type === 'error' ? 'error' : 'success',
                title: title,
                text: message
        // Global variable to store districts data
        let districtsData = [];
        // Fetch districts data
        async function loadDistricts() {
            try {
                const response = await fetch('data/districts.json');
                if (!response.ok) {
                    throw new Error('Failed to load districts data');
                const data = await response.json();
                districtsData = data.districts || [];
                initializeLocationAutocomplete();
            } catch (error) {
                console.error('Error loading districts:', error);
        // Initialize location autocomplete
        function initializeLocationAutocomplete() {
            $("#location, #editLocation").autocomplete({
                source: districtsData,
                minLength: 2,
                classes: {
                    "ui-autocomplete": "dropdown-menu"
                },
                appendTo: ".modal-body"
            }).addClass('form-control');
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        // Profile edit functionality
        function editProfile() {
            document.getElementById('name').disabled = false;
            document.getElementById('mobile').disabled = false;
            document.getElementById('password').disabled = false;
            document.getElementById('profileActions').classList.remove('d-none');
        function cancelEdit() {
            document.getElementById('profileForm').reset();
            document.getElementById('name').disabled = true;
            document.getElementById('mobile').disabled = true;
            document.getElementById('password').disabled = true;
            document.getElementById('profileActions').classList.add('d-none');
        // Function to show alert message
        function showAlert(message, type = 'success') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            // Remove any existing alerts
            document.querySelectorAll('.alert-dismissible').forEach(alert => alert.remove());
            // Add the new alert before the donations tab content
            const donationsTab = document.getElementById('donations-tab');
            if (donationsTab) {
                donationsTab.insertAdjacentHTML('afterbegin', alertHtml);
                
                // Auto-hide success messages after 5 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        const alert = document.querySelector('.alert');
                        if (alert) {
                            const bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }
                    }, 5000);
        // Handle edit donation button clicks
        function initializeEditDonationButtons() {
            document.querySelectorAll('.edit-donation').forEach(button => {
                button.addEventListener('click', function() {
                    const donationId = this.getAttribute('data-donation-id');
                    const donationTitle = this.getAttribute('data-title');
                    const donationDesc = this.getAttribute('data-description');
                    const donationCategory = this.getAttribute('data-category');
                    const donationCondition = this.getAttribute('data-condition');
                    const donationLocation = this.getAttribute('data-location');
                    // Set form values
                    document.getElementById('editDonationId').value = donationId;
                    document.getElementById('editTitle').value = donationTitle;
                    document.getElementById('editDescription').value = donationDesc;
                    document.getElementById('editCategory').value = donationCategory;
                    document.getElementById('editCondition').value = donationCondition;
                    document.getElementById('editLocation').value = donationLocation;
                    // Show current image if exists
                    const card = this.closest('.card');
                    const img = card.querySelector('img');
                    const currentImageContainer = document.getElementById('currentImageContainer');
                    currentImageContainer.innerHTML = '';
                    if (img && img.src) {
                        currentImageContainer.innerHTML = `
                            <div class="mt-2">
                                <p class="small text-muted mb-1">Current Image:</p>
                                <img src="${img.src}" class="img-thumbnail max-h-100">
                            </div>`;
                    // Reset form validation
                    const form = document.getElementById('editDonationForm');
                    form.classList.remove('was-validated');
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editDonationModal'));
                    modal.show();
        // Handle delete donation button clicks
        function initializeDeleteDonationButtons() {
            document.querySelectorAll('.delete-donation').forEach(button => {
                    const donationTitle = this.getAttribute('data-donation-title');
                    document.getElementById('donationIdToDelete').value = donationId;
                    document.getElementById('donationToDeleteTitle').textContent = donationTitle;
                    const modal = new bootstrap.Modal(document.getElementById('deleteDonationModal'));
        // Handle edit donation form submission
        function handleEditDonationForm() {
            const form = document.getElementById('editDonationForm');
            if (!form) return;
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    return;
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                const alertDiv = document.getElementById('editDonationAlert');
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...';
                // Submit form via AJAX
                fetch('update-donation.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'An error occurred. Please try again.');
                        });
                    return response.json();
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showAlert(data.message || 'Donation updated successfully!', 'success');
                        // Close modal after delay
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editDonationModal'));
                        setTimeout(() => {
                            modal.hide();
                            // Reload the page to show updated data
                            location.reload();
                        }, 1500);
                    } else {
                        // Show error message from server
                        throw new Error(data.message || 'An error occurred. Please try again.');
                .catch(error => {
                    console.error('Error:', error);
                    const errorMessage = error.message || 'An error occurred while updating the donation. Please try again.';
                    // Update the alert div with the error message
                    alertDiv.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${errorMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`;
                    alertDiv.classList.remove('d-none');
                    // Scroll to the alert
                    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
        // Handle delete request button clicks
        function handleDeleteRequestButtons() {
            const deleteButtons = document.querySelectorAll('.delete-request');
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteRequestModal'));
            let requestIdToDelete = null;
            deleteButtons.forEach(button => {
                    requestIdToDelete = this.dataset.requestId;
                    document.getElementById('requestToDeleteTitle').textContent = this.dataset.requestTitle;
                    deleteModal.show();
            // Handle delete confirmation
            const confirmDeleteBtn = document.getElementById('confirmDeleteRequest');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function() {
                    if (!requestIdToDelete) return;
                    const originalBtnText = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Deleting...';
                    // Submit delete request via AJAX
                    const formData = new FormData();
                    formData.append('request_id', requestIdToDelete);
                    fetch('delete-request.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showAlert(data.message || 'Request deleted successfully!', 'success');
                            // Close modal and remove the deleted row
                            deleteModal.hide();
                            const rowToDelete = document.querySelector(`.delete-request[data-request-id="${requestIdToDelete}"]`).closest('tr');
                            if (rowToDelete) {
                                rowToDelete.style.transition = 'opacity 0.3s';
                                rowToDelete.style.opacity = '0';
                                setTimeout(() => {
                                    rowToDelete.remove();
                                    
                                    // If no requests left, show empty state
                                    const tbody = document.querySelector('#requests-tab tbody');
                                    if (tbody && tbody.children.length === 0) {
                                        location.reload(); // Reload to show empty state message
                                }, 300);
                            } else {
                                location.reload(); // Fallback to page reload
                            }
                        } else {
                            showAlert(data.message || 'An error occurred. Please try again.', 'danger');
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('An error occurred. Please try again.', 'danger');
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = originalBtnText;
                    });
        // Handle delete donation form submission
        function handleDeleteDonationForm() {
            const form = document.getElementById('deleteDonationForm');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Deleting...';
                fetch('delete-donation.php', {
                .then(response => response.json())
                        showAlert(data.message || 'Donation deleted successfully!', 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteDonationModal'));
                        // Show error message
                        showAlert(data.message || 'An error occurred. Please try again.', 'danger');
                        // Close modal
                        modal.hide();
                    showAlert('An error occurred. Please try again.', 'danger');
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteDonationModal'));
                    modal.hide();
        // Handle fulfill request button clicks
        function handleFulfillRequestButtons() {
            const fulfillButtons = document.querySelectorAll('.fulfill-request');
            const fulfillModal = new bootstrap.Modal(document.getElementById('fulfillRequestModal'));
            const fulfillRequestTitle = document.getElementById('fulfillRequestTitle');
            const confirmFulfillBtn = document.getElementById('confirmFulfill');
            let currentRequestId = null;
            // Handle fulfill button clicks
            fulfillButtons.forEach(button => {
                    const requestId = this.getAttribute('data-request-id');
                    const requestTitle = this.getAttribute('data-request-title');
                    currentRequestId = requestId;
                    fulfillRequestTitle.textContent = '"' + requestTitle + '"';
                    fulfillModal.show();
            // Handle confirm fulfillment
            if (confirmFulfillBtn) {
                confirmFulfillBtn.addEventListener('click', function() {
                    if (!currentRequestId) return;
                    formData.append('request_id', currentRequestId);
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Processing...';
                    fetch('fulfill-request.php', {
                        body: formData
                            showAlert(data.message || 'Request marked as fulfilled successfully!', 'success');
                            // Hide the modal
                            fulfillModal.hide();
                            // Reload the page to reflect changes
                            setTimeout(() => window.location.reload(), 1000);
                            throw new Error(data.message || 'Failed to fulfill request');
                        showAlert(error.message || 'An error occurred while processing your request', 'danger');
        // Initialize tab if hash is present in URL
            // Initialize request handlers
            handleDeleteRequestButtons();
            handleFulfillRequestButtons();
            // Show any existing alerts from PHP
            <?php if (isset($_SESSION['success'])): ?>
                showAlert('<?php echo addslashes($_SESSION['success']); ?>', 'success');
            <?php if (isset($_SESSION['error'])): ?>
                showAlert('<?php echo addslashes($_SESSION['error']); ?>', 'danger');
            if (window.location.hash) {
                const tabTrigger = document.querySelector(`[data-bs-target="${window.location.hash}-tab"]`);
                if (tabTrigger) {
                    const tab = new bootstrap.Tab(tabTrigger);
                    tab.show();
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            // Initialize donation buttons
            initializeEditDonationButtons();
            initializeDeleteDonationButtons();
            // Initialize form handlers
            handleEditDonationForm();
            handleDeleteDonationForm();
            // Handle delete notification button clicks
            document.querySelectorAll('.delete-notification-btn').forEach(button => {
                    const notificationId = this.getAttribute('data-notification-id');
                    document.getElementById('notificationIdToDelete').value = notificationId;
                    const modal = new bootstrap.Modal(document.getElementById('deleteNotificationModal'));
            // Initialize donation form
            const donationForm = document.getElementById('donationForm');
            if (donationForm) {
                // Handle form submission
                donationForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Submitting...';
                    // Submit form via AJAX
                    fetch('donate.php', {
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'An error occurred. Please try again.');
                            showAlert(data.message || 'Donation submitted successfully!', 'success');
                            // Reset form
                            donationForm.reset();
                            donationForm.classList.remove('was-validated');
                            // Close modal after delay
                            const modal = bootstrap.Modal.getInstance(document.getElementById('donationModal'));
                            setTimeout(() => {
                                modal.hide();
                                // Reload the page to show the new donation
                                location.reload();
                            }, 1500);
                        showAlert(error.message || 'An error occurred. Please try again.', 'danger');
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                // Initialize form validation
                donationForm.addEventListener('submit', function(event) {
                    if (!donationForm.checkValidity()) {
                    donationForm.classList.add('was-validated');
                }, false);
            // Initialize location autocomplete
            loadDistricts();
    </script>
</body>
</html>
