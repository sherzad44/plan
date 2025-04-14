<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    
    $conn->beginTransaction();
    
    try {
        // Get booking details
        $booking_stmt = $conn->prepare("
            SELECT flight_id, passengers, status 
            FROM bookings 
            WHERE id = ? AND user_id = ?
        ");
        $booking_stmt->execute([$booking_id, $_SESSION['user_id']]);
        $booking = $booking_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking && $booking['status'] === 'pending') {
            // Update flight available seats
            $update_flight = $conn->prepare("
                UPDATE flights 
                SET available_seats = available_seats + ? 
                WHERE id = ?
            ");
            $update_flight->execute([$booking['passengers'], $booking['flight_id']]);
            
            // Update booking status
            $update_booking = $conn->prepare("
                UPDATE bookings 
                SET status = 'cancelled' 
                WHERE id = ?
            ");
            $update_booking->execute([$booking_id]);
            
            $conn->commit();
            header("Location: my_bookings.php?msg=cancelled");
            exit();
        }
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: my_bookings.php?error=true");
        exit();
    }
}

header("Location: my_bookings.php");
exit();
