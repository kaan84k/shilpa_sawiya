<?php
require_once '../../config/database.php';
require_once '../../includes/Request.php';
require_once '../../includes/UserAuth.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$request = new Request($conn);
$userAuth = new UserAuth($conn);

switch ($action) {
    case 'list':
        // Get all requests with requester info
        $query = "SELECT r.*, u.name as requester_name, u.email as requester_email, u.mobile as requester_mobile, u.profile_picture as requester_profile_picture, u.created_at as requester_created_at
                  FROM requests r
                  JOIN users u ON r.user_id = u.id
                  ORDER BY r.created_at DESC";
        $result = $conn->query($query);
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $row['requester'] = [
                'id' => $row['user_id'],
                'name' => $row['requester_name'] ?? '',
                'email' => $row['requester_email'] ?? '',
                'mobile' => $row['requester_mobile'] ?? '',
                'profile_picture' => $row['requester_profile_picture'] ?? null,
                'created_at' => $row['requester_created_at'] ?? null
            ];
            $row['date'] = $row['created_at'] ?? '';
            unset($row['requester_name'], $row['requester_email'], $row['requester_mobile'], $row['requester_profile_picture'], $row['requester_created_at']);
            $requests[] = $row;
        }
        echo json_encode(['success' => true, 'requests' => $requests]);
        break;
    case 'get':
        $id = intval($_GET['id'] ?? 0);
        $row = $request->getRequestById($id);
        if ($row) {
            $requester = $userAuth->getUserById($row['user_id']);
            $row['requester'] = [
                'id' => $requester['id'],
                'name' => $requester['name'],
                'email' => $requester['email'],
                'mobile' => $requester['mobile'],
                'profile_picture' => $requester['profile_picture'] ?? null,
                'created_at' => $requester['created_at']
            ];
            echo json_encode(['success' => true, 'request' => $row]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Request not found']);
        }
        break;
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        try {
            $request->updateRequest($id, $title, $description, $category);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        try {
            $request->deleteRequest($id);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
