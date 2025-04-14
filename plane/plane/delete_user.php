<?php
session_start();
require_once 'config/Database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Prevent admin from deleting themselves
    if ($user_id != $_SESSION['user_id']) {
        // Delete user's bookings first (foreign key constraint)
        $stmt = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    }
}

header("Location: manage_users.php");
exit();
