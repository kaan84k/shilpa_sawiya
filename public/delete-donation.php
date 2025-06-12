<?php
session_start();
require_once '../config/config.php';
use App\Models\UserAuth;
use App\Models\Donation;

// Set content type to JSON
header('Content-Type: application/json');
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
    exit();
}
// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$user_id = $_SESSION['user_id'];
$donation_id = isset($_POST['donation_id']) ? intval($_POST['donation_id']) : 0;
if ($donation_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid donation ID']);
$donation = new Donation($conn);
// Check if donation exists and belongs to the user
$donation_data = $donation->getDonationById($donation_id);
if (!$donation_data || $donation_data['user_id'] !== $user_id) {
    echo json_encode(['success' => false, 'message' => 'Donation not found or you don\'t have permission to delete it.']);
try {
    // Delete the donation
    if ($donation->deleteDonation($donation_id)) {
        echo json_encode([
            'success' => true,
            'message' => 'Donation deleted successfully!'
        ]);
    } else {
        throw new Exception('Failed to delete donation. Please try again.');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
?>
