<?php
session_start();
require_once '../config/config.php';

use App\Models\UserAuth;
use App\Models\Donation;
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$donation = new Donation($conn);
// Handle update or delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update'])) {
            $donation_id = $_POST['donation_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $category = $_POST['category'];
            $condition = $_POST['condition'];
            
            $donation->updateDonation(
                $donation_id,
                $title,
                $description,
                $category,
                $condition,
                null // Add the appropriate 6th argument here, e.g., $image or null if not updating image
            );
            $_SESSION['success'] = "Donation updated successfully!";
        } elseif (isset($_POST['delete'])) {
            $donation->deleteDonation($donation_id);
            $_SESSION['success'] = "Donation deleted successfully!";
        }
        
        header("Location: my-donations.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
// Get user's donations
$user_donations = $donation->getUserDonations($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Donations - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <?php
// Get user details
$userAuth = new UserAuth($conn);
$user = $userAuth->getUserById($_SESSION['user_id']);
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
                    <a href="my-donations.php" class="list-group-item list-group-item-action active">My Donations</a>
                    <a href="my-requests.php" class="list-group-item list-group-item-action">My Requests</a>
                    <a href="notifications.php" class="list-group-item list-group-item-action">My Notifications</a>
                </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4>My Donations</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5>Manage Your Donations</h5>
                            <a href="donate.php" class="btn btn-primary">Post New Donation</a>
                        </div>
                        <?php if(empty($user_donations)): ?>
                            <div class="alert alert-info">
                                You haven't posted any donations yet. <a href="donate.php">Post your first donation</a>!
                            </div>
                        <?php else: ?>
                            <div class="row mt-4">
                                <?php foreach($user_donations as $donation): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm h-100">
                                            <?php if(!empty($donation['image'])): ?>
                                                <img src="uploads/donations/<?php echo htmlspecialchars($donation['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($donation['title']); ?>" style="height: 200px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                    <i class="fas fa-box-open fa-3x text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
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
                                                    ?>"><?php echo ucfirst($donation['status']); ?></span>
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
                                                <div class="mb-3">
                                                    <span class="badge bg-<?php echo $category_icon['color']; ?> bg-opacity-10 text-<?php echo $category_icon['color']; ?> px-3 py-2 rounded-pill">
                                                        <i class="fas fa-<?php echo $category_icon['icon']; ?> me-1"></i>
                                                        <?php echo htmlspecialchars($donation['category']); ?>
                                                    </span>
                                                <p class="card-text mb-3"><?php echo htmlspecialchars($donation['description']); ?></p>
                                                <p class="card-text mb-4">
                                                    <small class="text-muted">
                                                        <i class="far fa-calendar-alt me-1"></i> <?php echo date('Y-m-d', strtotime($donation['created_at'])); ?>
                                                        <?php if(!empty($donation['location'])): ?>
                                                            <span class="ms-3">
                                                                <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($donation['location']); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </small>
                                                </p>
                                                
                                                <?php if($donation['status'] !== 'completed'): ?>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <a href="update-donation.php?id=<?php echo $donation['id']; ?>" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-edit me-1"></i> Edit
                                                        </a>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
                                                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this donation?')">
                                                                <i class="fas fa-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
    </div>
    <?php include '../src/Views/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
