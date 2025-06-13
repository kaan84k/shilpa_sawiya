<?php
session_start();
require_once '../config/config.php';
use App\Models\UserAuth;
use App\Models\Donation;

// Check if it's an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
         strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    if ($isAjax) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
        exit;
    } else {
        header("Location: login.php");
        exit();
    }
}
$user_id = $_SESSION['user_id'];
$donation = new Donation($conn);
// Handle donation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set JSON header for all responses
    header('Content-Type: application/json');
    
    try {
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $condition = trim($_POST['condition'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $image = $_FILES['image'] ?? null;
        // Basic validation
        $errors = [];
        if (empty($title)) $errors[] = 'Title is required';
        if (empty($description)) $errors[] = 'Description is required';
        if (empty($category)) $errors[] = 'Category is required';
        if (empty($condition)) $errors[] = 'Condition is required';
        if (empty($location)) $errors[] = 'Location is required';
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => implode('\n', $errors)
            ]);
            exit;
        }
        $donation_id = $donation->createDonation(
            $user_id,
            $title,
            $description,
            $category,
            $condition,
            $location,
            $image
        );
        $response = [
            'success' => true,
            'message' => 'Donation posted successfully!',
            'donation_id' => $donation_id
        ];
        // Always return JSON response
        http_response_code(200);
        echo json_encode($response);
    } catch (Exception $e) {
        $error = $e->getMessage();
            'success' => false,
            'message' => $error
        // Always return JSON response for errors too
        http_response_code(400);
    // If we get here, it's a non-AJAX request with an error
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Donation - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
                    <a href="my-donations.php" class="list-group-item list-group-item-action active">My Donations</a>
                    <a href="my-requests.php" class="list-group-item list-group-item-action">My Requests</a>
                    <a href="notifications.php" class="list-group-item list-group-item-action">My Notifications</a>
                </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4>Post a New Donation</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        <div id="donateAlert" class="alert d-none" role="alert" aria-live="assertive"></div>
                        
                        <form action="donate.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="title" class="form-label">Book Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Upload Image (Optional)</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <div class="form-text">Supported formats: JPG, JPEG, PNG. Max size: 2MB</div>
                                    <div class="mt-2">
                                        <img id="imagePreview" src="#" alt="Image Preview" class="img-thumbnail max-w-200 max-h-200 d-none">
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select a category</option>
                                        <option value="Books">Books</option>
                                        <option value="Stationery">Stationery</option>
                                        <option value="Uniforms">Uniforms</option>
                                        <option value="Bags">Bags</option>
                                        <option value="Electronics">Electronics</option>
                                    </select>
                                    <label for="condition" class="form-label">Condition <span class="text-danger">*</span></label>
                                    <select class="form-select" id="condition" name="condition" required>
                                        <option value="">Select condition</option>
                                        <option value="New">New</option>
                                        <option value="Like New">Like New</option>
                                        <option value="Very Good">Very Good</option>
                                        <option value="Good">Good</option>
                                        <option value="Acceptable">Acceptable</option>
                                <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" required>
                                <div class="form-text">Start typing to search for your location</div>
                            <div class="mb-4">
                                <label for="image" class="form-label">Book Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Upload a clear photo of the book (max 5MB, optional)</div>
                            <div class="d-flex gap-2">
                                <button type="submit" id="submitDonationBtn" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i>Submit Donation
                                </button>
                                <a href="donations.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                        </form>
    </div>
    <?php include '../src/Views/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
    function showAlert(message, type = 'success') {
        const alertDiv = document.getElementById('donateAlert');
        alertDiv.className = 'alert alert-' + type;
        alertDiv.textContent = message;
        alertDiv.classList.remove('d-none');
    }
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = document.getElementById('imagePreview');
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
    });
    // Form submission with AJAX
    $(document).ready(function() {
        $('form.needs-validation').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = new FormData(this);
            const btn = $('#submitDonationBtn');
            btn.prop('disabled', true)
               .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...');
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'view-donation.php?id=' + response.donation_id;
                        }, 1500);
                    } else {
                        showAlert(response.message || 'Please review the form for errors.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    showAlert('Unable to submit your donation right now.', 'danger');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Submit Donation');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="assets/js/search.js"></script>
    <script>
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
    });
    </script>
</body>
</html>
