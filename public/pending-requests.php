<?php
session_start();
require_once '../config/database.php';
require_once '../src/Models/UserAuth.php';
require_once '../src/Models/Request.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$request = new Request($conn);

// Handle request status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $request_id = $_POST['request_id'];
        $status = $_POST['status'];
        
        if (!in_array($status, ['accepted', 'rejected'])) {
            throw new Exception("Invalid status");
        }
        
        $request->handleDonationRequest($request_id, $status);
        $_SESSION['success'] = "Request status updated successfully!";
        header("Location: pending-requests.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Get user's pending donation requests
$pending_requests = $request->getPendingDonationRequests($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Requests - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include '../src/Views/header.php'; ?>
    
    <div class="container mt-4">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <h2>Pending Donation Requests</h2>
        
        <?php if(empty($pending_requests)): ?>
            <div class="alert alert-info">
                No pending requests at the moment. Your donations are available for others to request.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($pending_requests as $req): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($req['donation_title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr($req['donation_description'], 0, 100)); ?>...</p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Requested by: <?php echo htmlspecialchars($req['requester_name']); ?> | 
                                        Status: <?php echo htmlspecialchars($req['status']); ?>
                                    </small>
                                </p>
                                
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" name="status" value="accepted" class="btn btn-success btn-sm">Accept</button>
                                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../src/Views/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
