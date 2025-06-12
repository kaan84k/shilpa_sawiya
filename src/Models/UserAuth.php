<?php
class UserAuth {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function register($name, $email, $password, $location, $is_admin = 0) {
        try {
            // Validate input
            if (empty($name) || empty($email) || empty($password) || empty($location)) {
                throw new Exception("All fields are required");
            }
            
            // Check if email already exists
            $query = "SELECT id FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                throw new Exception("Email already registered");
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $query = "INSERT INTO users (name, email, password, location, is_admin) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssi", $name, $email, $hashed_password, $location, $is_admin);
            
            if ($stmt->execute()) {
                return $this->conn->insert_id;
            }
            
            throw new Exception("Registration failed");
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function updateProfilePicture($userId, $profilePicture) {
        try {
            // Validate input
            if (empty($userId) || empty($profilePicture)) {
                throw new Exception("User ID and profile picture are required");
            }
            
            // Update user's profile picture
            $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $profilePicture, $userId);
            
            if ($stmt->execute()) {
                return true;
            }
            
            throw new Exception("Failed to update profile picture");
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function login($email, $password) {
        try {
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && password_verify($password, $user['password'])) {
                // Create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                if (!empty($user['profile_picture'])) {
                    $_SESSION['profile_picture'] = $user['profile_picture'];
                }
                
                return true;
            }
            
            throw new Exception("Invalid email or password");
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function updateProfile($user_id, $name = null, $mobile = null, $password = null) {
        try {
            $fields = [];
            $params = [];
            
            if ($name !== null) {
                $fields[] = "name = ?";
                $params[] = $name;
            }
            
            if ($mobile !== null) {
                $fields[] = "mobile = ?";
                $params[] = $mobile;
            }
            
            if ($password !== null) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $fields[] = "password = ?";
                $params[] = $hashed_password;
            }
            
            if (empty($fields)) {
                throw new Exception("No fields to update");
            }
            
            $query = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
            $params[] = $user_id;
            
            $stmt = $this->conn->prepare($query);
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
            
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
    

    
    public function getUserById($id) {
        try {
            $query = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            // Update session with profile picture if not set
            if ($user && !empty($user['profile_picture']) && (!isset($_SESSION['profile_picture']) || $_SESSION['profile_picture'] !== $user['profile_picture'])) {
                $_SESSION['profile_picture'] = $user['profile_picture'];
            }
            
            return $user;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateLocation($user_id, $location) {
        try {
            $query = "UPDATE users SET location = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $location, $user_id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }}
