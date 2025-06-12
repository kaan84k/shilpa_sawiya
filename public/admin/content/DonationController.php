<?php
// filepath: c:/xampp/htdocs/shilpa-sawiya/admin/content/DonationController.php
require_once '../../../config/config.php';

use App\Models\Donation;
use App\Models\UserAuth;

header('Content-Type: application/json');
$action = $_REQUEST['action'] ?? '';
$donation = new Donation($conn);
$userAuth = new UserAuth($conn);
switch ($action) {
    case 'list':
        // Get all donations with donor info
        $query = "SELECT d.*, u.name as donor_name, u.email as donor_email, u.mobile as donor_mobile, u.profile_picture, u.created_at as donor_created_at
                  FROM donations d
                  JOIN users u ON d.user_id = u.id
                  ORDER BY d.created_at DESC";
        $result = $conn->query($query);
        $donations = [];
        while ($row = $result->fetch_assoc()) {
            // Defensive: fallback if donor fields are missing
            $row['donor'] = [
                'id' => $row['user_id'],
                'name' => $row['donor_name'] ?? '',
                'email' => $row['donor_email'] ?? '',
                'mobile' => $row['donor_mobile'] ?? '',
                'profile_picture' => $row['profile_picture'] ?? null,
                'created_at' => $row['donor_created_at'] ?? null
            ];
            $row['date'] = $row['created_at'] ?? '';
            // Always provide donor object for frontend
            if (!isset($row['donor']) || !is_array($row['donor'])) {
                $row['donor'] = [
                    'id' => $row['user_id'],
                    'name' => '',
                    'email' => '',
                    'mobile' => '',
                    'profile_picture' => null,
                    'created_at' => null
                ];
            }
            unset($row['donor_name'], $row['donor_email'], $row['donor_mobile'], $row['profile_picture'], $row['donor_created_at']);
            $donations[] = $row;
        }
        echo json_encode(['success' => true, 'donations' => $donations]);
        break;
    case 'get':
        $id = intval($_GET['id'] ?? 0);
        $row = $donation->getDonationById($id);
        if ($row) {
            $donor = $userAuth->getUserById($row['user_id']);
                'id' => $donor['id'],
                'name' => $donor['name'],
                'email' => $donor['email'],
                'mobile' => $donor['mobile'],
                'profile_picture' => $donor['profile_picture'] ?? null,
                'created_at' => $donor['created_at']
            echo json_encode(['success' => true, 'donation' => $row]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Donation not found']);
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        // Optionally add condition/location if you want to support them
        try {
            $donation->updateDonation($id, $title, $description, $category, '', '');
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    case 'delete':
            $donation->deleteDonation($id);
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
