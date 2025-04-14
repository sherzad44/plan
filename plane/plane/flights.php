<?php
session_start();
require_once 'config/Database.php';

$database = new Database();
$conn = $database->connect();

// Get all available flights
$stmt = $conn->query("
    SELECT * FROM flights 
    WHERE departure_date > NOW() 
    AND available_seats > 0 
    ORDER BY departure_date ASC
");
$flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <title>گەشتەکان | گەشتی ئاسمانی</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-start: #0a192f;
            --gradient-end: #1e3a8a;
            --text-dark: #0a192f;
            --text-light: #64748b;
            --extra-light: #f8fafc;
            --white: #ffffff;
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            color: var(--white);
        }

        .flights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .flight-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .flight-card:hover {
            transform: translateY(-5px);
        }

        .flight-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--extra-light);
        }

        .flight-number {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .flight-price {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gradient-end);
        }

        .flight-details {
            margin-bottom: 1.5rem;
        }

        .flight-details p {
            margin: 0.5rem 0;
            color: var(--text-dark);
        }

        .flight-details i {
            margin-left: 0.5rem;
            color: var(--gradient-end);
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(to right, var(--gradient-start), var(--gradient-end));
            color: var(--white);
            text-align: center;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(to right, var(--gradient-end), var(--gradient-start));
            transform: translateY(-1px);
        }

        .nav-links {
            display: flex;
            gap: 1rem;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .seats-available {
            color: #22c55e;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>گەشتەکان</h1>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="my_bookings.php">گەشتەکانم</a>
                    <a href="logout.php">چوونەدەرەوە</a>
                <?php else: ?>
                    <a href="login.php">چوونەژوورەوە</a>
                    <a href="register.php">خۆتۆمارکردن</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="flights-grid">
            <?php foreach($flights as $flight): ?>
                <div class="flight-card">
                    <div class="flight-header">
                        <span class="flight-number"><?php echo htmlspecialchars($flight['flight_number']); ?></span>
                        <span class="flight-price">$<?php echo number_format($flight['price'], 2); ?></span>
                    </div>
                    <div class="flight-details">
                        <p><i class="ri-flight-takeoff-line"></i> <?php echo htmlspecialchars($flight['departure_city']); ?></p>
                        <p><i class="ri-flight-land-line"></i> <?php echo htmlspecialchars($flight['arrival_city']); ?></p>
                        <p><i class="ri-time-line"></i> <?php echo htmlspecialchars($flight['departure_date']); ?></p>
                        <p class="seats-available">
                            <i class="ri-user-line"></i> 
                            شوێنی بەردەست: <?php echo htmlspecialchars($flight['available_seats']); ?>
                        </p>
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="book_flight.php?flight_id=<?php echo $flight['id']; ?>" class="btn">داواکردن</a>
                    <?php else: ?>
                        <a href="login.php" class="btn">چوونەژوورەوە بۆ داواکردن</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
