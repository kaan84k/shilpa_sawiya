<?php
class Donation {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function createDonation($user_id, $title, $description, $category, $condition, $location, $image = null) {
        try {
            // Validate input
            if (empty($title) || empty($category) || empty($condition)) {
                throw new Exception("Title, category, and condition are required");
            }
            
            // Insert donation
            $query = "INSERT INTO donations (user_id, title, description, category, `condition`, location, status) VALUES (?, ?, ?, ?, ?, ?, 'available')";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("isssss", $user_id, $title, $description, $category, $condition, $location);
            
            if ($stmt->execute()) {
                $donation_id = $this->conn->insert_id;
                
                // If image is provided, save it
                if ($image !== null) {
                    $this->saveDonationImage($donation_id, $image);
                }
                
                return $donation_id;
            }
            
            throw new Exception("Failed to create donation");
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function saveDonationImage($donation_id, $image) {
        try {
            // Check if file was uploaded without errors
            if ($image['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Error uploading file. Error code: ' . $image['error']);
            }
            
            // Validate file size (max 2MB)
            $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
            if ($image['size'] > $maxFileSize) {
                throw new Exception('File is too large. Maximum allowed size is 2MB.');
            }
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($fileInfo, $image['tmp_name']);
            finfo_close($fileInfo);
            
            if (!in_array($detectedType, $allowedTypes)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, and PNG files are allowed.');
            }
            
            $upload_dir = "uploads/donations/";
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    throw new Exception('Failed to create upload directory');
                }
            }
            
            // Generate a unique filename
            $fileExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            $filename = "donation_" . $donation_id . "_" . uniqid() . "." . $fileExtension;
            $target_file = $upload_dir . $filename;
            
            // Move the uploaded file to the target directory
            if (!move_uploaded_file($image['tmp_name'], $target_file)) {
                throw new Exception('Failed to move uploaded file');
            }
            
            // Update donation record with image path
            $query = "UPDATE donations SET image = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $filename, $donation_id);
            
            if (!$stmt->execute()) {
                // If database update fails, delete the uploaded file
                if (file_exists($target_file)) {
                    unlink($target_file);
                }
                throw new Exception('Failed to update donation with image information');
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getUserDonations($user_id) {
        try {
            $query = "SELECT * FROM donations WHERE user_id = ? ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getAvailableDonations($category = null, $location = null, $date_range = null) {
        try {
            $query = "SELECT d.*, u.name as user_name FROM donations d 
                     JOIN users u ON d.user_id = u.id 
                     WHERE d.status = 'available'";
            
            $params = [];
            $types = "";
            
            // Add category filter if provided
            if ($category) {
                $query .= " AND d.category = ?";
                $params[] = $category;
                $types .= "s";
            }
            
            // Add location filter if provided
            if ($location) {
                $query .= " AND u.location LIKE ?";
                $params[] = "%" . $location . "%";
                $types .= "s";
            }
            
            // Add date range filter if provided
            if ($date_range) {
                list($start_date, $end_date) = explode("-", $date_range);
                $start_date = trim($start_date);
                $end_date = trim($end_date);
                $query .= " AND d.created_at BETWEEN ? AND ?";
                $params[] = $start_date . " 00:00:00";
                $params[] = $end_date . " 23:59:59";
                $types .= "ss";
            }
            
            $query .= " ORDER BY d.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getDonationById($id) {
        try {
            $query = "SELECT * FROM donations WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function updateDonation($id, $title, $description, $category, $condition, $location, $image = null) {
        try {
            // Validate required fields
            if (empty($title) || empty($category) || empty($condition) || empty($location)) {
                throw new Exception("Title, category, condition, and location are required");
            }
            
            // Update donation
            $query = "UPDATE donations SET title = ?, description = ?, category = ?, `condition` = ?, location = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssssi", $title, $description, $category, $condition, $location, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update donation");
            }
            
            // If new image is provided, update it
            if ($image !== null && $image['error'] == 0) {
                $this->saveDonationImage($id, $image);
            }
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get completed donations with user information for success stories
     * 
     * @param int $limit Number of donations to return
     * @return array Array of completed donations with user info
     */
    public function getCompletedDonationsWithUsers($limit = 6) {
        try {
            $query = "SELECT d.*, u.name as user_name, u.email as user_email, u.profile_picture 
                    FROM donations d 
                    JOIN users u ON d.user_id = u.id 
                    WHERE d.status = 'completed' 
                    ORDER BY d.updated_at DESC 
                    LIMIT ?";
                    
            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $limit);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if ($result === false) {
                throw new Exception("Get result failed: " . $this->conn->error);
            }
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error in getCompletedDonationsWithUsers: " . $e->getMessage());
            return [];
        }
    }
    
    public function deleteDonation($id) {
        try {
            // Start transaction
            $this->conn->begin_transaction();
            
            // First get the image path if exists
            $donation = $this->getDonationById($id);
            if ($donation && !empty($donation['image'])) {
                $image_path = "uploads/donations/" . $donation['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Delete any associated donation requests first
            $query = "DELETE FROM donation_requests WHERE donation_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Delete donation record
            $query = "DELETE FROM donations WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $this->conn->commit();
                return true;
            }
            
            $this->conn->rollback();
            throw new Exception("Failed to delete donation");
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
} // End of class Donation
