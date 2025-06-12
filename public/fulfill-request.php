<?php
session_start();
require_once '../config/config.php';
use App\Models\Request;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Check if request ID is provided
if (!isset($_POST['request_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Request fulfilled successfully']);
    exit;
$user_id = $_SESSION['user_id'];
$request_id = $_POST['request_id'];
try {
    $request = new Request($conn);
    
    // First, get the request details to verify ownership
    $query = "SELECT r.*, dr.donation_id 
             FROM requests r 
             LEFT JOIN donation_requests dr ON r.id = dr.request_id 
             WHERE r.id = ? AND r.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $request_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $req = $result->fetch_assoc();
    if (!$req) {
        throw new Exception("Request not found or unauthorized");
    }
    // If this is a donation request, update both request and donation status
    if ($req['donation_id']) {
        // Update donation status to completed
        $query = "UPDATE donations SET status = 'completed' WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $req['donation_id']);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update donation status");
        }
        
        // Update donation request status
        $query = "UPDATE donation_requests SET status = 'completed' WHERE request_id = ?";
        $stmt->bind_param("i", $request_id);
            throw new Exception("Failed to update donation request status");
    // Update request status
    $request->fulfillRequest($request_id, $user_id);
    $_SESSION['success'] = "Request marked as fulfilled!";
echo json_encode(['success' => true, 'message' => 'Request fulfilled successfully']);
exit;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
echo json_encode(['success' => false, 'message' => $e->getMessage()]);
?>
