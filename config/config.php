<?php
require_once __DIR__ . '/../bootstrap.php';

// Database configuration from environment variables
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'shilpa_sawiya';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

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
