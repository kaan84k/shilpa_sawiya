<?php
session_start();
require_once '../config/config.php';
use App\Models\Request;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to request items'
    ]);
    exit();
}
// Check if donation_id is provided
if (!isset($_POST['donation_id'])) {
        'message' => 'Invalid request'
$user_id = $_SESSION['user_id'];
$donation_id = $_POST['donation_id'];
try {
    $request = new Request($conn);
    $request_id = $request->requestDonation($donation_id, $user_id);
    
        'success' => true,
        'message' => 'Donation request sent successfully! The donor will be notified.'
} catch (Exception $e) {
        'message' => $e->getMessage()
?>
