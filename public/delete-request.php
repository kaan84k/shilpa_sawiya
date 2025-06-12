<?php
session_start();
require_once '../config/config.php';
use App\Models\Request;

// Set content type to JSON
header('Content-Type: application/json');
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
    exit();
}
// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
// Check if request_id is provided
if (!isset($_POST['request_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Request ID is required']);
$request_id = (int)$_POST['request_id'];
$user_id = (int)$_SESSION['user_id'];
try {
    $conn->begin_transaction();
    $query = "SELECT id, title FROM requests WHERE id = ? AND user_id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    $stmt->bind_param("ii", $request_id, $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Error checking request ownership: " . $stmt->error);
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Request not found or you don't have permission to delete it");
    $request_data = $result->fetch_assoc();
    $query = "DELETE FROM donation_requests WHERE request_id = ?";
    $stmt->bind_param("i", $request_id);
        throw new Exception("Error deleting associated donation requests: " . $stmt->error);
    $query = "DELETE FROM requests WHERE id = ?";
        throw new Exception("Error deleting request: " . $stmt->error);
    $conn->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Request "' . htmlspecialchars($request_data['title']) . '" has been deleted successfully',
        'request_id' => $request_id
    ]);
} catch (Exception $e) {
    if (isset($conn) && $conn) {
        $conn->rollback();
    http_response_code(500);
        'success' => false,
        'message' => $e->getMessage()
} finally {
    if (isset($stmt)) {
        $stmt->close();
        $conn->close();
