<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: my_bookings.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

$booking_stmt = $conn->prepare("
    SELECT b.*, f.destination, f.departure_time, f.departure_date, f.price
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    WHERE b.id = ? AND b.user_id = ?
");

$booking_stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$booking = $booking_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وردەکاری گەشت | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
          background: linear-gradient(25deg,rgb(0, 10, 151),rgb(0, 0, 0));
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: -1;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .booking-details {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            animation: fadeIn 0.5s ease-out;
        }

        .booking-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .booking-header i {
            font-size: 2.5rem;
            animation: float 3s ease-in-out infinite;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .detail-item:hover {
            transform: translateY(-5px);
        }

        .detail-item i {
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgb(206, 202, 202);
            border-radius: 50%;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.2);
            margin-bottom: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-primary {
            background: linear-gradient(45deg, #1e3c72 0%, #000000 100%);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="booking-details">
            <div class="booking-header">
                <i class="fas fa-plane"></i>
                <h1><?php echo htmlspecialchars($booking['destination']); ?></h1>
            </div>

            <div class="status-badge">
                <?php echo htmlspecialchars($booking['status']); ?>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <h3>بەرواری گەشت</h3>
                        <p><?php echo htmlspecialchars($booking['departure_date']); ?></p>
                    </div>
                </div>

                <div class="detail-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>کاتی بەڕێکەوتن</h3>
                        <p><?php echo htmlspecialchars($booking['departure_time']); ?></p>
                    </div>
                </div>

                <!-- Remove this block -->
<div class="detail-item">
    <i class="fas fa-clock"></i>
    <div>
        <h3>کاتی گەیشتن</h3>
        <p><?php echo htmlspecialchars($booking['departure_time']); ?></p>
    </div>
</div>


                <div class="detail-item">
                    <i class="fas fa-users"></i>
                    <div>
                        <h3>ژمارەی گەشتیار</h3>
                        <p><?php echo $booking['passengers']; ?> کەس</p>
                    </div>
                </div>

                <div class="detail-item">
                    <i class="fas fa-dollar-sign"></i>
                    <div>
                        <h3>نرخی گەشت</h3>
                        <p>$<?php echo number_format($booking['total_price'], 2); ?></p>
                    </div>
                </div>

                <div class="detail-item">
                    <i class="fas fa-ticket-alt"></i>
                    <div>
                        <h3>کۆدی گەشت</h3>
                        <p>#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></p>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="user_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i>
                    گەڕانەوە
                </a>
                <?php if ($booking['status'] !== 'cancelled'): ?>
                <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-primary" onclick="return confirm('دڵنیای لە هەڵوەشاندنەوەی گەشتەکە؟')">
                    <i class="fas fa-times"></i>
                    هەڵوەشاندنەوەی گەشت
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
