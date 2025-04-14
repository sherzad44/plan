<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

$flight_stmt = $conn->prepare("
    SELECT * FROM flights 

");
$flight_stmt->execute();
$flights = $flight_stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_id = $_POST['flight_id'];
    $passengers = $_POST['passengers'];
    
    $flight_stmt = $conn->prepare("SELECT price, available_seats FROM flights WHERE id = ?");
    $flight_stmt->execute([$flight_id]);
    $flight = $flight_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($flight && $flight['available_seats'] >= $passengers) {
        $total_price = $flight['price'] * $passengers;
        
        $conn->beginTransaction();
        
        try {
            $booking_stmt = $conn->prepare("
                INSERT INTO bookings (user_id, flight_id, passengers, total_price, status)
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $booking_stmt->execute([$_SESSION['user_id'], $flight_id, $passengers, $total_price]);
            
            $update_stmt = $conn->prepare("
                UPDATE flights 
                SET available_seats = available_seats - ? 
                WHERE id = ?
            ");
            $update_stmt->execute([$passengers, $flight_id]);
            
            $conn->commit();
            header("Location: my_bookings.php");
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "هەڵەیەک ڕوویدا لە کاتی تۆمارکردنی گەشتەکەت";
        }
    } else {
        $error = "ببورە، ژمارەی کورسییەکان بەردەست نین";
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داواکردنی گەشت | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-primary: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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

        .container {
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

        .booking-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1e3c72;
            font-weight: bold;
        }

        select, input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .submit-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }

        .error-message {
            background: #ff6b6b;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
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

        .booking-form form.submitting {
            opacity: 0.7;
            transform: scale(0.98);
            transition: all 0.3s ease;
        }

        #total-price {
            font-size: 1.2rem;
            color: #1e3c72;
            font-weight: bold;
            margin-top: 1rem;
            text-align: center;
            padding: 1rem;
            background: rgba(30, 60, 114, 0.1);
            border-radius: 8px;
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
                <a href="my_bookings.php" class="nav-link">
                    <i class="fas fa-ticket-alt"></i> گەشتەکانم
                </a>
            </div>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> چوونەدەرەوە
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <i class="fas fa-plane"></i>
            <h1>داواکردنی گەشت</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="booking-form">
            <form method="POST">
                <div class="form-group">
                    <label for="flight_id">گەشت هەڵبژێرە</label>
                    <select name="flight_id" id="flight_id" required>
                    <option value="">گەشتێک هەڵبژێرە</option>
<?php foreach ($flights as $flight): ?>
    <option value="<?php echo $flight['id']; ?>" 
            data-price="<?php echo $flight['price']; ?>" 
            class="flight-option">
        <?php 
            echo htmlspecialchars($flight['destination']) . ' | ' .
                 htmlspecialchars($flight['departure_date']) . ' | ' .
                 htmlspecialchars($flight['departure_time']) . ' | ' .
                 'نرخ: ' . number_format($flight['price'], 2) . ' | ' .
                 'کورسی: ' . $flight['available_seats'];
        ?>
    </option>
<?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label for="passengers">ژمارەی گەشتیار</label>
                    <input type="number" name="passengers" id="passengers" min="1" max="10" required>
                </div>

                <div id="total-price"></div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-check"></i> داواکردنی گەشت
                </button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const flightSelect = document.getElementById('flight_id');
        const passengersInput = document.getElementById('passengers');
        const bookingForm = document.querySelector('.booking-form form');

        function updateTotalPrice() {
            const selectedFlight = flightSelect.options[flightSelect.selectedIndex];
            const passengers = passengersInput.value;
            const priceDisplay = document.getElementById('total-price');
            
            if (selectedFlight.value && passengers) {
                const flightPrice = parseFloat(selectedFlight.dataset.price);
                const total = flightPrice * passengers;
                priceDisplay.textContent = `کۆی گشتی: $${total.toFixed(2)}`;
            }
        }

        flightSelect.addEventListener('change', updateTotalPrice);
        passengersInput.addEventListener('input', updateTotalPrice);

        bookingForm.addEventListener('submit', function(e) {
            if (!flightSelect.value) {
                e.preventDefault();
                alert('تکایە گەشتێک هەڵبژێرە');
            }
            
            const passengers = parseInt(passengersInput.value);
            if (passengers < 1 || passengers > 10) {
                e.preventDefault();
                alert('ژمارەی گەشتیار دەبێت لە نێوان ١ بۆ ١٠ بێت');
            }
        });

        bookingForm.addEventListener('submit', function() {
            this.classList.add('submitting');
        });
    });
    </script>
</body>
</html>
