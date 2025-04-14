<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->connect();
    
    $flight_id = $_POST['flight_id'];
    $user_id = $_SESSION['user_id'];
    $passengers = $_POST['passengers'];
    $booking_date = date('Y-m-d');
    
    try {
        $conn->beginTransaction();
        
        // Check flight availability
        $check_stmt = $conn->prepare("SELECT price, available_seats FROM flights WHERE id = ? AND available_seats >= ?");
        $check_stmt->execute([$flight_id, $passengers]);
        $flight = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($flight) {
            $total_price = $flight['price'] * $passengers;
            
            // Create booking
            $booking_stmt = $conn->prepare("INSERT INTO bookings (user_id, flight_id, booking_date, passengers, total_price, status) VALUES (?, ?, ?, ?, ?, 'confirmed')");
            $booking_stmt->execute([$user_id, $flight_id, $booking_date, $passengers, $total_price]);
            
            // Update available seats
            $update_stmt = $conn->prepare("UPDATE flights SET available_seats = available_seats - ? WHERE id = ?");
            $update_stmt->execute([$passengers, $flight_id]);
            
            $conn->commit();
            
            $_SESSION['success_message'] = "گەشتەکەت بە سەرکەوتوویی تۆمارکرا!";
            header("Location: my_bookings.php");
            exit();
        } else {
            throw new Exception("کورسی بەردەست نەماوە");
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: create_booking.php");
        exit();
    }
}
?>
