<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

$booking_stmt = $conn->prepare("
    SELECT b.*, f.destination, f.departure_time, f.departure_date 
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    WHERE b.user_id = ?
    ORDER BY f.departure_date DESC
");
$booking_stmt->execute([$_SESSION['user_id']]);
$bookings = $booking_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گەشتەکانم | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-primary: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --gradient-secondary: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            --shadow-primary: 0 10px 30px rgba(30, 60, 114, 0.3);
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(25deg,rgb(36, 197, 255),rgb(0, 0, 0));
            min-height: 100vh;
            padding: 2rem;
        }

        .bookings-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: var(--shadow-primary);
        }

        .page-header i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .booking-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border: 1px solid rgba(30, 60, 114, 0.1);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease-out forwards;
            opacity: 0;
            position: relative;
            overflow: hidden;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-primary);
        }

        .booking-status {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-size: 0.9rem;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 60, 114, 0.2);
            transition: all 0.3s ease;
        }

        .booking-status:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 60, 114, 0.3);
        }

        .booking-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .booking-header i {
            font-size: 2rem;
            color: #1e3c72;
            animation: pulse 2s infinite;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            background: rgba(255, 255, 255, 0.5);
            padding: 1.5rem;
            border-radius: 10px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .detail-item i {
            color: #1e3c72;
            font-size: 1.2rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #1e3c72;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .book-flight-btn {
            display: inline-block;
            background: var(--gradient-primary);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            margin-top: 1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-primary);
        }

        .book-flight-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(30, 60, 114, 0.4);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes slideIn {
            from { transform: translateX(50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .nav-menu {
            background: white;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .nav-list {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav-link {
            color: #1e3c72;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(30, 60, 114, 0.1);
        }
    </style>
</head>
<body>
    <nav class="nav-menu">
        <div class="nav-list">
            <div>
                <a href="user_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> سەرەکی
                </a>
            </div>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> چوونەدەرەوە
            </a>
        </div>
    </nav>

    <div class="bookings-container">
        <div class="page-header">
            <i class="fas fa-ticket-alt"></i>
            <h1>گەشتەکانم</h1>
            <a href="book_flight.php" class="book-flight-btn">
                <i class="fas fa-plus"></i> زیادکردنی گەشت
            </a>
        </div>

        <?php if ($bookings): ?>
            <?php foreach($bookings as $index => $booking): ?>
                <div class="booking-card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <span class="booking-status"><?php echo htmlspecialchars($booking['status']); ?></span>
                    
                    <div class="booking-header">
                        <i class="fas fa-plane"></i>
                        <h2><?php echo htmlspecialchars($booking['destination']); ?></h2>
                    </div>

                    <div class="booking-details">
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo htmlspecialchars($booking['departure_date']); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo htmlspecialchars($booking['departure_time']); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-users"></i>
                            <span><?php echo $booking['passengers']; ?> گەشتیار</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-dollar-sign"></i>
                            <span><?php echo number_format($booking['total_price'], 2); ?>$</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-plane-slash"></i>
                <h2>هیچ گەشتێکت نییە</h2>
                <p>دەتوانیت گەشتی نوێ داوا بکەیت</p>
                <a href="book_flight.php" class="book-flight-btn">
                    <i class="fas fa-plus"></i> زیادکردنی گەشت
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
