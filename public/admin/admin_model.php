<?php
// admin_model.php
require_once __DIR__ . '/../../config/config.php';

class AdminModel {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getTotalUsers() {
        $result = mysqli_query($this->conn, "SELECT COUNT(*) as total FROM users");
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public function getTotalRequests() {
        $result = mysqli_query($this->conn, "SELECT COUNT(*) as total FROM requests");
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public function getRequestStatusCounts() {
        $result = mysqli_query($this->conn, "SELECT status, COUNT(*) as total FROM requests GROUP BY status");
        $status = ['pending' => 0, 'fulfilled' => 0];
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['status'] === 'pending') $status['pending'] = $row['total'];
            if ($row['status'] === 'fulfilled') $status['fulfilled'] = $row['total'];
        }
        return $status;
    }

    public function getTotalDonations() {
        $result = mysqli_query($this->conn, "SELECT COUNT(*) as total FROM donations");
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public function getDonationStatusCounts() {
        $result = mysqli_query($this->conn, "SELECT status, COUNT(*) as total FROM donations GROUP BY status");
        $status = ['available' => 0, 'completed' => 0];
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['status'] === 'available') $status['available'] = $row['total'];
            if ($row['status'] === 'completed') $status['completed'] = $row['total'];
        }
        return $status;
    }

    public function getTotalAdmins() {
        $result = mysqli_query($this->conn, "SELECT COUNT(*) as total FROM admin_user");
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public function getUnverifiedUsers($limit = 10) {
        $result = mysqli_query($this->conn, "SELECT id, name, email, mobile FROM users WHERE is_verified = 0 ORDER BY id DESC LIMIT " . intval($limit));
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        return $users;
    }

    public function verifyUser($userId) {
        $userId = intval($userId);
        return mysqli_query($this->conn, "UPDATE users SET is_verified = 1 WHERE id = $userId");
    }

    public function getRecentAvailableDonations($limit = 5) {
        $result = mysqli_query($this->conn, "SELECT id, title, category, created_at, description FROM donations WHERE status = 'available' ORDER BY created_at DESC LIMIT " . intval($limit));
        $donations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $donations[] = $row;
        }
        return $donations;
    }
}
