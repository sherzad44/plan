<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

$stmt = $conn->prepare("SELECT * FROM flights WHERE available_seats > 0");
$stmt->execute();
$flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داواکردنی تکت | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-primary: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            --shadow-primary: 0 10px 30px rgba(107, 115, 255, 0.3);
            --radius-lg: 20px;
            --radius-md: 15px;
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f6f8ff 0%, #ffffff 100%);
            padding: 2rem;
        }

        .booking-container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #000DFF;
        }

        .form-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            color: #333;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e5ff;
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            border-color: #6B73FF;
            box-shadow: 0 0 0 3px rgba(107, 115, 255, 0.2);
            outline: none;
        }

        .flight-card {
            padding: 1rem;
            border-radius: var(--radius-md);
            border: 2px solid #e1e5ff;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .flight-card:hover {
            border-color: #6B73FF;
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }

        .flight-card.selected {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: var(--radius-md);
            background: var(--gradient-primary);
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .price-tag {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: #000DFF;
            font-weight: bold;
        }

        .seats-left {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="form-header">
            <i class="fas fa-plane-departure"></i>
            <h1>داواکردنی تکت</h1>
        </div>

        <form method="POST" action="process_booking.php">
            <div class="form-group">
                <label>گەشتەکان</label>
                <?php foreach($flights as $flight): ?>
                    <div class="flight-card" onclick="selectFlight(this, <?php echo $flight['id']; ?>)">
                        <div class="price-tag"><?php echo $flight['price']; ?>$</div>
                        
                        <p><i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars($flight['departure_date']); ?></p>
                       
                        <div class="seats-left">
                            <i class="fas fa-chair"></i>
                            <?php echo $flight['available_seats']; ?> کورسی ماوە
                        </div>
                    </div>
                <?php endforeach; ?>
                <input type="hidden" name="flight_id" id="selected_flight">
            </div>

            <div class="form-group">
                <label>ژمارەی تکت</label>
                <input type="number" name="passengers" class="form-control" min="1" max="5" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-ticket-alt"></i>
                داواکردنی تکت
            </button>
        </form>
    </div>

    <script>
        function selectFlight(element, flightId) {
            document.querySelectorAll('.flight-card').forEach(card => {
                card.classList.remove('selected');
            });
            element.classList.add('selected');
            document.getElementById('selected_flight').value = flightId;
        }
    </script>
</body>
</html>
