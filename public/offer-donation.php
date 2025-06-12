<?php
session_start();
require_once '../config/config.php';
use App\Models\Request;
use App\Models\Donation;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Check if request_id is provided
if (!isset($_GET['request_id'])) {
    header("Location: public-requests.php");
$request_id = $_GET['request_id'];
$user_id = $_SESSION['user_id'];
$request = new Request($conn);
// Get request details
$query = "SELECT r.*, u.name as requestor_name FROM requests r 
          JOIN users u ON r.user_id = u.id 
          WHERE r.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$public_request = $result->fetch_assoc();
// Handle donation offer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $condition = $_POST['condition'];
        $image = $_FILES['image'] ?? null;
        
        // Create donation
        $donation = new Donation($conn);
        $donation_id = $donation->createDonation(
            $user_id,
            $title,
            $description,
            $public_request['category'],
            $condition,
            $image
        );
        // Create donation request
        $request->requestDonation($donation_id, $public_request['user_id']);
        $_SESSION['success'] = "Donation offered successfully! The requestor will be notified.";
        header("Location: my-donations.php");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Donation - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include '../src/Views/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h4>Offer Donation for: <?php echo htmlspecialchars($public_request['title']); ?></h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($public_request['title']); ?>" required>
                            </div>
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required>
                                    <?php echo htmlspecialchars($public_request['description']); ?>
                                </textarea>
                                <label for="condition" class="form-label">Condition</label>
                                <select class="form-select" id="condition" name="condition" required>
                                    <option value="">Select condition</option>
                                    <option value="new">New</option>
                                    <option value="like_new">Like New</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                </select>
                                <label for="image" class="form-label">Item Photo (Optional)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <button type="submit" class="btn btn-primary">Offer Donation</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
    <?php include '../src/Views/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
