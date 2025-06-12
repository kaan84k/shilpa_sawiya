<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/database.php';
require_once 'includes/UserAuth.php';
require_once 'includes/Donation.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$donation = new Donation($conn);

// Get donation ID from POST data
$donation_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Check if donation exists and belongs to the user
$donation_data = $donation->getDonationById($donation_id);
if (!$donation_data || $donation_data['user_id'] !== $user_id) {
    echo json_encode(['success' => false, 'message' => 'Donation not found or you don\'t have permission to edit it.']);
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['title', 'description', 'category', 'condition', 'location'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                $missing_fields[] = ucfirst($field);
            }
        }
        
        if (!empty($missing_fields)) {
            throw new Exception("The following fields are required: " . implode(', ', $missing_fields));
        }

        // Get form data
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);
        $condition = trim($_POST['condition']);
        $location = trim($_POST['location']);
        $image = $_FILES['image'] ?? null;

        // Update donation
        if ($donation->updateDonation($donation_id, $title, $description, $category, $condition, $location, $image)) {
            // Get updated donation data
            $updated_donation = $donation->getDonationById($donation_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Donation updated successfully!',
                'donation' => $updated_donation
            ]);
        } else {
            throw new Exception("Failed to update donation. Please try again.");
        }
    } catch (Exception $e) {
        http_response_code(400);
        error_log('Update Donation Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    exit();
}

// If not a POST request, return 405 Method Not Allowed
http_response_code(405);
echo json_encode([
    'success' => false,
    'message' => 'Method not allowed'
]);
?>
