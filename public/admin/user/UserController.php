<?php
// Handles user management actions: fetch, update, delete
require_once '../../config/database.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
        $users = $stmt->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'users' => $users]);
        break;
    case 'get':
        $id = intval($_GET['id'] ?? 0);
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        echo json_encode(['success' => !!$user, 'user' => $user]);
        break;
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $mobile = $_POST['mobile'] ?? '';
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, mobile=? WHERE id=?");
        $stmt->bind_param('sssi', $name, $email, $mobile, $id);
        $ok = $stmt->execute();
        echo json_encode(['success' => $ok]);
        break;
    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        echo json_encode(['success' => $ok]);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
