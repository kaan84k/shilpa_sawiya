<?php
namespace App\Models;

class Admin {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function authenticate($email, $password) {
        $query = "SELECT * FROM users WHERE email = ? AND is_admin = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }
    
    public function getAllUsers() {
        $query = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllDonations($status = null) {
        $query = "SELECT d.*, u.name as user_name FROM donations d 
                 JOIN users u ON d.user_id = u.id 
                 WHERE d.status = ? 
                 ORDER BY d.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $actual_status = $status ?? 'available';
        $stmt->bind_param("s", $actual_status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllRequests($status = null) {
        $query = "SELECT r.*, dr.donation_id, d.title as donation_title, u.name as requester_name 
                 FROM requests r 
                 JOIN donation_requests dr ON r.id = dr.request_id 
                 JOIN donations d ON dr.donation_id = d.id 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.status = ? 
                 ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $actual_status = $status ?? 'pending';
        $stmt->bind_param("s", $actual_status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserDonations($user_id) {
        $query = "SELECT d.*, u.name as user_name 
                 FROM donations d 
                 JOIN users u ON d.user_id = u.id 
                 WHERE d.user_id = ? 
                 ORDER BY d.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserRequests($user_id) {
        $query = "SELECT r.*, dr.donation_id, d.title as donation_title, d.category as donation_category 
                 FROM requests r 
                 LEFT JOIN donation_requests dr ON r.id = dr.request_id 
                 LEFT JOIN donations d ON dr.donation_id = d.id 
                 WHERE r.user_id = ? 
                 ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus($table, $id, $status) {
        $query = "UPDATE $table SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function updateDonationStatus($donation_id, $status) {
        return $this->updateStatus('donations', $donation_id, $status);
    }

    public function getRequestById($request_id) {
        $query = "SELECT r.*, dr.donation_id, d.title as donation_title 
                 FROM requests r 
                 JOIN donation_requests dr ON r.id = dr.request_id 
                 JOIN donations d ON dr.donation_id = d.id 
                 WHERE r.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getDonationById($donation_id) {
        $query = "SELECT d.*, u.name as user_name 
                 FROM donations d 
                 JOIN users u ON d.user_id = u.id 
                 WHERE d.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateRequestStatus($request_id, $status) {
        return $this->updateStatus('requests', $request_id, $status);
    }

    public function deleteUser($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
    
    public function deleteDonation($donation_id) {
        // First delete any associated requests
        $query = "DELETE FROM requests WHERE donation_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        
        // Then delete the donation
        $query = "DELETE FROM donations WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $donation_id);
        return $stmt->execute();
    }
}
