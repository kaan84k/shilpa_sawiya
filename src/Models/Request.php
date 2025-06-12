<?php
namespace App\Models;

class Request {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function createCustomRequest($user_id, $title, $description, $category) {
        try {
            // Log the incoming parameters
            error_log("createCustomRequest called with: user_id=$user_id, title=$title, category=$category");
            
            // Validate user_id
            if (empty($user_id) || !is_numeric($user_id) || $user_id <= 0) {
                $error = "Invalid user_id in createCustomRequest: " . print_r($user_id, true);
                error_log($error);
                throw new Exception("Invalid user session. Please login again.");
            }
            
            // Check if user exists in database
            $user_check = $this->conn->prepare("SELECT id FROM users WHERE id = ?");
            $user_check->bind_param("i", $user_id);
            $user_check->execute();
            $user_result = $user_check->get_result();
            
            if ($user_result->num_rows === 0) {
                $error = "User with ID $user_id does not exist in the database";
                error_log($error);
                throw new Exception("User account not found. Please contact support.");
            }
            
            // Validate input
            if (empty($title) || empty($category)) {
                $error = "Title or category is empty: title=$title, category=$category";
                error_log($error);
                throw new Exception("Title and category are required");
            }
            
            // Insert request
            $query = "INSERT INTO requests (user_id, title, description, category) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt === false) {
                $error = "Prepare failed: " . $this->conn->error;
                error_log($error);
                throw new Exception("Database error. Please try again later.");
            }
            
            $stmt->bind_param("isss", $user_id, $title, $description, $category);
            
            if ($stmt->execute()) {
                $request_id = $this->conn->insert_id;
                error_log("Successfully created request with ID: $request_id");
                
                // Create notification for public request
                try {
                    $this->createNotification(null, "New Public Request", 
                        "New request posted: " . $title, 'public_request');
                } catch (Exception $e) {
                    // Log but don't fail the request if notification fails
                    error_log("Failed to create notification: " . $e->getMessage());
                }
                
                return $request_id;
            } else {
                $error = "Execute failed: " . $stmt->error . " (Error Code: " . $stmt->errno . ")";
                error_log($error);
                throw new Exception("Failed to create request. Please try again.");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function requestDonation($donation_id, $requester_id) {
        try {
            // Validate input
            if (empty($requester_id)) {
                throw new Exception("Requester ID is required");
            }
            
            // Check if donation exists and is available
            $query = "SELECT * FROM donations WHERE id = ? AND status = 'available'";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $donation_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $donation = $result->fetch_assoc();
            
            if (!$donation) {
                throw new Exception("Donation not found or not available");
            }
            
            // Create request record
            $query = "INSERT INTO requests (user_id, title, description, category, status) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $title = $donation['title'];
            $description = "Request for donation: " . $donation['title'];
            $category = $donation['category'];
            $status = 'pending';
            $stmt->bind_param("issss", $requester_id, $title, $description, $category, $status);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create request record");
            }
            
            $request_id = $this->conn->insert_id;
            
            // Create donation request record
            $query = "INSERT INTO donation_requests (donation_id, request_id, requested_by, status) 
                     VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $status = 'pending';
            $stmt->bind_param("iiis", $donation_id, $request_id, $requester_id, $status);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create donation request record");
            }
            
            // Update donation status
            $query = "UPDATE donations SET status = 'requested' WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $donation_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update donation status");
            }
            
            // Get both donor and requester details
            $query = "SELECT u1.name as donor_name, u1.email as donor_email, u1.mobile as donor_mobile, 
                            u2.name as requester_name, u2.email as requester_email, u2.mobile as requester_mobile 
                     FROM donations d 
                     JOIN users u1 ON d.user_id = u1.id 
                     JOIN users u2 ON u2.id = ? 
                     WHERE d.id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $requester_id, $donation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $details = $result->fetch_assoc();
            
            // Create notification message with requester details
            $notification_message = "New Donation Request:\n\n";
            $notification_message .= "Donation: " . $donation['title'] . "\n";
            $notification_message .= "Category: " . $donation['category'] . "\n";
            $notification_message .= "Condition: " . $donation['condition'] . "\n\n";
            
            $notification_message .= "Requester Details:\n";
            $notification_message .= "Name: " . $details['requester_name'] . "\n";
            $notification_message .= "Email: " . $details['requester_email'] . "\n";
            $notification_message .= "Mobile: " . $details['requester_mobile'] . "\n\n";
            
            $notification_message .= "Status: Pending\n";
            $notification_message .= "Created: " . date('Y-m-d H:i', strtotime($donation['created_at']));
            
            $this->createNotification($donation['user_id'], "New Donation Request", 
                $notification_message, 'donation_request');
            
            return $request_id;
        } catch (Exception $e) {
            throw $e;
        }
    }
    

    
    public function createNotification($user_id, $title, $message, $type = 'donation_request') {
        try {
            $query = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("isss", $user_id, $title, $message, $type);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getUserRequests($user_id) {
        try {
            $query = "SELECT r.*, dr.donation_id, d.title as donation_title, d.category as donation_category 
                     FROM requests r 
                     LEFT JOIN donation_requests dr ON r.id = dr.request_id 
                     LEFT JOIN donations d ON dr.donation_id = d.id 
                     WHERE r.user_id = ? 
                     ORDER BY r.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getPendingDonationRequests($user_id) {
        try {
            $query = "SELECT dr.*, d.title as donation_title, d.description as donation_description, 
                     u.name as requester_name 
                     FROM donation_requests dr 
                     JOIN donations d ON dr.donation_id = d.id 
                     JOIN users u ON dr.requested_by = u.id 
                     WHERE d.user_id = ? AND dr.status = 'pending' 
                     ORDER BY dr.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function fulfillRequest($request_id, $user_id) {
        try {
            // Start transaction
            $this->conn->begin_transaction();
            
            // Get request details
            $query = "SELECT * FROM requests WHERE id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $request_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $request = $result->fetch_assoc();
            
            if (!$request) {
                throw new Exception("Request not found or unauthorized");
            }
            
            // Get associated donation request
            $query = "SELECT * FROM donation_requests WHERE request_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $donation_request = $result->fetch_assoc();
            
            if (!$donation_request) {
                throw new Exception("No associated donation request found");
            }
            
            // Update request status
            $query = "UPDATE requests SET status = 'fulfilled', updated_at = NOW() WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            
            // Update donation request status
            $query = "UPDATE donation_requests SET status = 'completed', updated_at = NOW() WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $donation_request['id']);
            $stmt->execute();
            
            // Update donation status
            $query = "UPDATE donations SET status = 'completed' WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $donation_request['donation_id']);
            $stmt->execute();
            
            // Commit transaction
            $this->conn->commit();
            
            // Get donor's ID and donation details
            $query = "SELECT d.user_id as donor_id, d.title 
                     FROM donations d 
                     WHERE d.id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $donation_request['donation_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $donation = $result->fetch_assoc();
            
            if (!$donation) {
                throw new Exception("Donation not found");
            }
            
            // Create notification for donor
            $this->createNotification($donation['donor_id'], "Donation Request Completed", 
                "Your donation request for " . $donation['title'] . " has been marked as completed.", 
                'donation_completed');
            
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function handleDonationRequest($donation_request_id, $status) {
        try {
            // Start transaction
            $this->conn->begin_transaction();
            
            // Get donation request details
            $query = "SELECT * FROM donation_requests WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $donation_request_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $request = $result->fetch_assoc();
            
            if (!$request) {
                throw new Exception("Donation request not found");
            }
            
            // Update donation request status
            $query = "UPDATE donation_requests SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $status, $donation_request_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update donation request status");
            }
            
            // Get donation ID from request
            $query = "SELECT donation_id FROM donation_requests WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $donation_request_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $donation = $result->fetch_assoc();
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update donation request status");
            }
            
            // Update donation status based on request status
            if ($status == 'accepted') {
                $query = "UPDATE donations SET status = 'reserved', reserved_until = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("i", $donation['donation_id']);
            } elseif ($status == 'completed') {
                $query = "UPDATE donations SET status = 'completed' WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("i", $donation['donation_id']);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update donation status");
            }
            
            // Update request status
            $query = "UPDATE requests SET status = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $status, $request['request_id']);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update request status");
            }
            
            // Get user details for notifications
            $query = "SELECT u1.*, u2.* FROM users u1 
                     JOIN donation_requests dr ON u1.id = dr.requested_by 
                     JOIN users u2 ON u2.id = (SELECT user_id FROM donations WHERE id = dr.donation_id) 
                     WHERE dr.id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $donation_request_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_assoc();
            
            // Send notifications based on status
            if ($status == 'accepted') {
                // Notify requester
                $this->createNotification($users['id'], "Donation Request Accepted", 
                    "Your request for donation has been accepted. You have 24 hours to confirm.");
                
                // Notify donor
                $this->createNotification($users['user_id'], "Donation Request Accepted", 
                    "You have accepted a donation request. The requester has 24 hours to confirm.");
            } elseif ($status == 'rejected') {
                // Notify requester
                $this->createNotification($users['id'], "Donation Request Rejected", 
                    "Your request for donation has been rejected.");
                
                // Make donation available again
                $query = "UPDATE donations SET status = 'available', reserved_until = NULL WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("i", $request['donation_id']);
                $stmt->execute();
            } elseif ($status == 'completed') {
                // Notify both parties
                $this->createNotification($users['id'], "Donation Completed", 
                    "Your donation request has been successfully completed.");
                
                $this->createNotification($users['user_id'], "Donation Completed", 
                    "Your donation has been successfully completed.");
                
                // Generate and send confirmation slip
                /*$this->generateAndSendConfirmationSlip($request['donation_id']);
                $this->createNotification($request['requested_by'], 
                    "Donation Request Status Update", 
                    "Your request for " . $request['title'] . " has been " . $status); */
            }
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getPublicRequests() {
        try {
            $query = "SELECT r.*, u.name as user_name 
                     FROM requests r 
                     JOIN users u ON r.user_id = u.id 
                     WHERE r.status = 'pending' AND r.id NOT IN (
                         SELECT request_id FROM donation_requests
                     )
                     ORDER BY r.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    // Get a single request by ID
    public function getRequestById($id) {
        $query = "SELECT * FROM requests WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update a request (admin)
    public function updateRequest($id, $title, $description, $category) {
        $query = "UPDATE requests SET title = ?, description = ?, category = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $title, $description, $category, $id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update request: " . $stmt->error);
        }
        return true;
    }

    // Delete a request (admin)
    public function deleteRequest($id) {
        // Optionally delete related donation_requests
        $query = "DELETE FROM donation_requests WHERE request_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        // Now delete the request
        $query = "DELETE FROM requests WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete request: " . $stmt->error);
        }
        return true;
    }
}
