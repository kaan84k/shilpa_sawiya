<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/database.php';
require_once 'includes/UserAuth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Log upload errors
ini_set('log_errors', 1);
ini_set('error_log', 'upload_errors.log');

error_log("Starting file upload process");

$userAuth = new UserAuth($conn);
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $userId = $_SESSION['user_id'];
    $file = $_FILES['profile_picture'];
    
    // Log file details
    error_log("File upload attempted. File info: " . print_r($file, true));
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
        ];
        $error = $uploadErrors[$file['error']] ?? 'Unknown upload error';
        error_log("Upload error: " . $error);
        $_SESSION['error'] = $error;
        header('Location: dashboard.php');
        exit();
    }
    
    // Validate file
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif'
    ];
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    
    // Check if file was uploaded without errors
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    
    if (!array_key_exists($mime, $allowedTypes)) {
        $error = 'Only JPG, PNG, and GIF files are allowed. Detected type: ' . $mime;
        error_log("Invalid file type: " . $mime);
    } 
    // Check file size
    elseif ($file['size'] > $maxFileSize) {
        $error = 'File size must be less than 2MB. Current size: ' . round($file['size'] / 1024 / 1024, 2) . 'MB';
        error_log("File too large: " . $file['size'] . " bytes");
    } else {
        // Generate unique filename with correct extension
        $fileExt = $allowedTypes[$mime];
        $fileName = 'user_' . $userId . '_' . time() . '.' . $fileExt;
        $uploadDir = 'uploads/profile_pictures/';
        $uploadPath = $uploadDir . $fileName;
        
        // Ensure upload directory exists and is writable
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                $error = 'Failed to create upload directory';
                error_log("Failed to create directory: " . $uploadDir);
            }
        }
        
        if (!is_writable($uploadDir)) {
            $error = 'Upload directory is not writable';
            error_log("Directory not writable: " . $uploadDir);
        } else {
            error_log("Attempting to move uploaded file to: " . $uploadPath);
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                error_log("File moved successfully. Updating database...");
                
                // Update user's profile picture in database
                if ($userAuth->updateProfilePicture($userId, $fileName)) {
                    error_log("Database update successful");
                    
                    // Delete old profile picture if it exists
                    if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
                        $oldFilePath = $uploadDir . $_SESSION['profile_picture'];
                        if (file_exists($oldFilePath)) {
                            if (!unlink($oldFilePath)) {
                                error_log("Warning: Failed to delete old profile picture: " . $oldFilePath);
                            }
                        }
                    }
                    
                    // Update session with new profile picture
                    $_SESSION['profile_picture'] = $fileName;
                    $success = 'Profile picture updated successfully!';
                    error_log("Profile picture updated successfully for user ID: " . $userId);
                } else {
                    $error = 'Failed to update profile picture in database.';
                    error_log("Database update failed: " . $conn->error);
                    
                    // Remove the uploaded file if database update failed
                    if (file_exists($uploadPath)) {
                        if (!unlink($uploadPath)) {
                            error_log("Warning: Failed to remove uploaded file after database error: " . $uploadPath);
                        }
                    }
                }
            } else {
                $error = 'Failed to move uploaded file. Check server permissions.';
                error_log("move_uploaded_file failed. Error: " . print_r(error_get_last(), true));
                error_log("Upload directory permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4));
            }
        }
    }
    
    // Store message in session to display after redirect
    if (!empty($error)) {
        $_SESSION['error'] = $error;
    } else {
        $_SESSION['success'] = $success;
    }
    
    header('Location: dashboard.php');
    exit();
}

// If not a POST request or no file was uploaded, redirect to dashboard
header('Location: dashboard.php');
exit();
