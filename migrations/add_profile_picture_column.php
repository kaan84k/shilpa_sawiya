<?php
require_once __DIR__ . '/../config/database.php';

// Add profile_picture column to users table
$sql = "ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER email";

if (mysqli_query($conn, $sql)) {
    echo "Successfully added profile_picture column to users table\n";
    
    // Create uploads directory if it doesn't exist
    $uploadDir = '../uploads/profile_pictures/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        echo "Created uploads directory\n";
    }
} else {
    echo "Error adding profile_picture column: " . mysqli_error($conn) . "\n";
}
?>
