<?php
// Database configuration
$host = 'localhost';
$dbname = 'shilpa_sawiya';
$username = 'root';
$password = '';

// Initialize session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
