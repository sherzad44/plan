<?php
require_once 'config/Database.php';

// Get flight ID and validate
$flight_id = isset($_GET['flight_id']) ? (int)$_GET['flight_id'] : 0;

// Database connection
$database = new Database();
$conn = $database->getConnection();

// Fetch flight details
$sql = "SELECT * FROM flights WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();

if (!$flight) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $passengers = $_POST['passengers'];

    // Insert booking
    $sql = "INSERT INTO bookings (flight_id, passenger_name, email, phone, num_passengers) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $flight_id, $name, $email, $phone, $passengers);
    
    if ($stmt->execute()) {
        // Update available seats
        $sql = "UPDATE flights SET available_seats = available_seats - ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $passengers, $flight_id);
        $stmt->execute();
        
        header("Location: confirmation.php?booking_id=" . $conn->insert_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet">
    <title>تۆمارکردنی گەشت | سلێمانی</title>
    <style>
        /* Reuse existing styles from index.php */
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --primary-color: #3d5cb8;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --white: #ffffff;
            --background-light: #f8f9fa;
            --shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: '20_Sarchia_Banoka_1';
        }

        body {
            background: var(--background-light);
            padding-top: 80px;
        }

        .booking-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .flight-summary {
            background: var(--primary-color);
            color: var(--white);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .booking-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: grid;
            gap: 0.5rem;
        }

        .form-group label {
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-group input {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .submit-btn {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: var(--secondary-color);
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="nav__container">
            <div class="nav__logo">سلێمانی</div>
            <ul class="nav__links">
                <li><a href="index.php" class="nav__link">سەرەکی</a></li>
                <li><a href="#" class="nav__link">گەشتەکان</a></li>
                <li><a href="#" class="nav__link">خزمەتگوزارییەکان</a></li>
                <li><a href="#" class="nav__link">پەیوەندی</a></li>
            </ul>
        </div>
    </nav>

    <div class="booking-container">
        <div class="flight-summary">
            <h2>زانیاری گەشت</h2>
            <p>شوێن: <?php echo $flight['destination']; ?></p>
            <p>بەروار: <?php echo $flight['flight_date']; ?></p>
            <p>کات: <?php echo $flight['departure_time']; ?></p>
            <p>نرخ: $<?php echo $flight['price']; ?></p>
        </div>

        <form class="booking-form" method="POST">
            <div class="form-group">
                <label for="name">ناوی تەواو</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">ئیمەیڵ</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">ژمارەی مۆبایل</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="passengers">ژمارەی گەشتیار</label>
                <input type="number" id="passengers" name="passengers" min="1" max="<?php echo $flight['available_seats']; ?>" required>
            </div>

            <button type="submit" class="submit-btn">پشتڕاستکردنەوەی داواکاری</button>
        </form>
    </div>

    <script>
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.nav');
            if (window.scrollY > 50) {
                nav.style.background = 'rgba(255,255,255,0.95)';
            } else {
                nav.style.background = 'var(--white)';
            }
        });
    </script>
</body>
</html>
