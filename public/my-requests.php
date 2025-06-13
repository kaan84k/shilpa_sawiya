<?php
session_start();
require_once '../config/config.php';
use App\Models\UserAuth;
use App\Models\Request;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$request = new Request($conn);
// Handle request fulfillment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fulfill'])) {
    try {
        $request_id = $_POST['request_id'];
        $request->fulfillRequest($request_id, $user_id);
        $_SESSION['success'] = "Request marked as fulfilled!";
        header("Location: my-requests.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
// Get user's requests
$user_requests = $request->getUserRequests($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests - Shilpa Sawiya</title>
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
    ?>
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
                    <a href="my-requests.php" class="list-group-item list-group-item-action active">My Requests</a>
                    <a href="notifications.php" class="list-group-item list-group-item-action">My Notifications</a>
                </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4>My Requests</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5>Manage Your Requests</h5>
                            <a href="post-request.php" class="btn btn-primary">Post New Request</a>
                        </div>
                        <?php if(empty($user_requests)): ?>
                            <div class="alert alert-info">
                                You haven't posted any requests yet. <a href="post-request.php">Post your first request</a>!
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
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
                                                            <?php echo htmlspecialchars($req['donation_title']); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        Public Request
                                                    <?php endif; ?>
                                                </td>
                                                    <?php 
                                                    // Show donation category for donation requests, otherwise show request category
                                                    echo htmlspecialchars(!empty($req['donation_category']) ? $req['donation_category'] : $req['category']); 
                                                    ?>
                                                    <span class="badge bg-<?php 
                                                        echo $req['status'] === 'pending' ? 'warning' : 
                                                        ($req['status'] === 'fulfilled' ? 'success' : 'secondary')
                                                    ?>">
                                                        <?php echo ucfirst($req['status']); ?>
                                                    </span>
                                                <td><?php echo date('F j, Y', strtotime($req['created_at'])); ?></td>
                                                    <?php if (!empty($req['donation_id']) && $req['status'] === 'pending'): ?>
                                                    <button type="button" class="btn btn-success btn-sm fulfill-request" 
                                                            data-request-id="<?php echo $req['id']; ?>">
                                                        Fulfill Request
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($req['status'] === 'pending'): ?>
                                                    <button type="button" class="btn btn-danger btn-sm delete-request" 
                                                        Delete Request
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to show alerts
        function showAlert(message, success = true) {
            const alertDiv = document.createElement('div');
            alertDiv.className = success ? 'alert alert-success alert-dismissible fade show' : 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = message;
            alertDiv.innerHTML += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.insertBefore(alertDiv, cardBody.firstChild);
                
                // Auto-refresh on success after 2 seconds
                if (success) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Fulfill request handler
            document.querySelectorAll('.fulfill-request').forEach(button => {
                button.addEventListener('click', function() {
                    const requestId = this.dataset.requestId;
                    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                    
                    document.getElementById('confirmModalTitle').textContent = 'Fulfill Request';
                    document.getElementById('confirmModalBody').innerHTML = 'Are you sure you want to fulfill this request?';
                    document.getElementById('confirmModalOk').onclick = function() {
                        fetch('fulfill-request.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'request_id=' + requestId
                        })
                        .then(response => {
    if (!response.ok) {
        throw new Error('Network response was not ok');
    return response.json();
})
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                showAlert(data.message, false);
                            }
                        .catch(error => {
                            showAlert('An error occurred while processing your request.', false);
                        });
                    };
                    confirmModal.show();
                });
            });
            // Delete request handler
            document.querySelectorAll('.delete-request').forEach(button => {
                    document.getElementById('confirmModalTitle').textContent = 'Delete Request';
                    document.getElementById('confirmModalBody').innerHTML = 'Are you sure you want to delete this request?';
                        fetch('delete-request.php', {
                        .then(response => response.json())
        });
    </script>
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body" id="confirmModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmModalOk">OK</button>
    <?php include '../src/Views/footer.php'; ?>
</body>
</html>
