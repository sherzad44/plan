<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

// Get all flights
$flight_stmt = $conn->prepare("SELECT * FROM flights ORDER BY departure_date");
$flight_stmt->execute();
$flights = $flight_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all bookings with user and flight info
$booking_stmt = $conn->prepare("
    SELECT b.*, u.username, f.destination 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN flights f ON b.flight_id = f.id
    ORDER BY b.created_at DESC
");
$booking_stmt->execute();
$bookings = $booking_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all users
$user_stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
$user_stmt->execute();
$users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبۆردی بەڕێوەبەر | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-primary: linear-gradient(135deg, #1e3c72 0%,rgb(0, 0, 0) 100%);
            --gradient-dark: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            --shadow-primary: 0 10px 30px rgba(30, 60, 114, 0.3);
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(25deg,rgb(255, 255, 255),rgb(255, 255, 255));
            min-height: 100vh;
            padding: 2rem;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        .admin-header {
            color: white;
            text-align: center;
            margin-bottom: 3rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientFlow 8s ease infinite;
        }

        .admin-header i {
            font-size: 3rem;
            
            animation: float 3s ease-in-out infinite;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stats-card {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            text-align: center;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(31, 38, 135, 0.2);
        }

        .stats-card i {
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            background: var(--gradient-dark);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }

        .data-table {
            width: 100%;
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        .data-table h2 {
            margin-bottom: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #eee;
        }

        th {
            background: var(--gradient-primary);
            color: white;
        }

        tr {
            transition: all 0.3s ease;
        }

        tr:hover {
            background: rgba(107, 115, 255, 0.05);
            transform: translateX(-5px);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .status-pending {
            color:white;
            background: rgba(255, 0, 0, 0.1);
            background: linear-gradient(135deg,rgb(132, 90, 1) 0%,rgb(69, 32, 26) 100%);
        }

        .status-confirmed {
            color:white;
            background: rgba(255, 0, 0, 0.1);
            background: linear-gradient(135deg,rgb(16, 115, 7) 0%,rgb(69, 32, 26) 100%);
        }

        .status-cancelled {
            color:white;
            background: rgba(255, 0, 0, 0.1);
            background: linear-gradient(135deg,rgb(133, 0, 0) 0%,rgb(69, 32, 26) 100%);
        }

        .seats-none {
            color: white;
            background: linear-gradient(135deg,rgb(133, 0, 0) 0%,rgb(69, 32, 26) 100%);
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .seats-low {
            color: white;
            background: linear-gradient(135deg,rgb(255, 153, 0) 0%,rgb(255, 94, 0) 100%);
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .seats-available {
            color: white;
            background: linear-gradient(135deg,rgb(16, 115, 7) 0%,rgb(69, 32, 26) 100%);
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .seats-error {
            color: white;
            background: linear-gradient(135deg,rgb(0, 89, 255) 0%,rgb(0, 60, 172) 100%);
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .search-input {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #1e3c72;
            box-shadow: 0 0 0 2px rgba(30, 60, 114, 0.1);
        }
        .logout-btn {
            margin-right: auto;
            background: linear-gradient(135deg,rgb(133, 0, 0) 0%,rgb(69, 32, 26) 100%);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 65, 108, 0.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>
    
    <div class="admin-container">
    <div class="admin-header">
            <i class="fas fa-user-shield"></i>
            <h1 style="color:white">داشبۆردی بەڕێوەبەر</h1>
        </div>

        <div class="admin-grid">
            <div class="stats-card">
                <i class="fas fa-plane"></i>
                <h3>گەشتەکان</h3>
                <p class="counter"><?php echo count($flights); ?></p>
            </div>
            <div class="stats-card">
                <i class="fas fa-ticket-alt"></i>
                <h3>داواکارییەکان</h3>
                <p class="counter"><?php echo count($bookings); ?></p>
            </div>
            <div class="stats-card">
                <i class="fas fa-users"></i>
                <h3>بەکارهێنەران</h3>
                <p class="counter"><?php echo count($users); ?></p>
            </div>
        </div>

        <div class="action-buttons">
            <button class="action-btn" onclick="window.location.href='add_flight.php'">
                <i class="fas fa-plus"></i>
                زیادکردنی گەشت
            </button>
            <button class="action-btn" onclick="window.location.href='add_user.php'">
                <i class="fas fa-user-plus"></i>
                زیادکردنی بەکارهێنەر
            </button>
            <button class="action-btn logout-btn" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i>
                چونەدەرەوە
            </button>
        </div>


        <div class="data-table">
            <h2>گەشتەکان</h2>
            <input type="text" class="search-input" placeholder="گەڕان لە گەشتەکان..." data-table="flights-table">
            <table id="flights-table">
                <thead>
                    <tr>
                        <th>شوێن</th>
                        <th>بەروار</th>
                        <th>کات</th>
                        <th>نرخ</th>
                        <th>کورسی بەردەست</th>
                        <th>کردارەکان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($flights as $flight): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flight['destination']); ?></td>
                            <td><?php echo htmlspecialchars($flight['departure_date']); ?></td>
                            <td><?php echo htmlspecialchars($flight['departure_time']); ?></td>
                            <td><?php echo number_format($flight['price'], 2); ?>$</td>
                            <td>
                                <?php 
                                    $seats = $flight['available_seats'];
                                    if ($seats == 0) {
                                        echo "<span class='seats-none'>نەماوە</span>";
                                    } 
                                    elseif ($seats > 0 && $seats < 20) {
                                        echo "<span class='seats-low'>کەمە ($seats)</span>";
                                    }
                                    elseif ($seats >= 20 && $seats < 1000) {
                                        echo "<span class='seats-available'>بەردەستە ($seats)</span>";
                                    }
                                    else {
                                        echo "<span class='seats-error'>کێشە ($seats)</span>";
                                    }
                                ?>
                            </td>
                            <td>
                                <a href="edit_flight.php?id=<?php echo $flight['id']; ?>" class="action-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="data-table">
            <h2>داواکارییەکان</h2>
            <input type="text" class="search-input" placeholder="گەڕان لە داواکارییەکان..." data-table="bookings-table">
            <table id="bookings-table">
                <thead>
                    <tr>
                        <th>بەکارهێنەر</th>
                        <th>شوێن</th>
                        <th>بەروار</th>
                        <th>ژمارەی گەشتیار</th>
                        <th>نرخی گشتی</th>
                        <th>دۆخ</th>
                        <th>کردارەکان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['username']); ?></td>
                            <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            <td><?php echo $booking['passengers']; ?></td>
                            <td><?php echo number_format($booking['total_price'], 2); ?>$</td>
                            <td>
                                <?php
                                    $statusClass = '';
                                    switch($booking['status']) {
                                        case 'pending':
                                            $statusClass = 'status-pending';
                                            $statusText = 'چاوەروان';
                                            break;
                                        case 'confirmed':
                                            $statusClass = 'status-confirmed';
                                            $statusText = 'پەسەندکراو';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'status-cancelled';
                                            $statusText = 'رەتکراوە';
                                            break;
                                    }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_booking.php?id=<?php echo $booking['id']; ?>" class="action-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="data-table">
            <h2>بەکارهێنەران</h2>
            <input type="text" class="search-input" placeholder="گەڕان لە بەکارهێنەران..." data-table="users-table">
            <table id="users-table">
                <thead>
                    <tr>
                        <th>ناو</th>
                  
                        <th>ڕۆڵ</th>
                        <th>بەرواری دروستکردن</th>
                        <th>کردارەکان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                           
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn">
                                <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInputs = document.querySelectorAll('.search-input');
            searchInputs.forEach(input => {
                input.addEventListener('keyup', function() {
                    const tableId = this.getAttribute('data-table');
                    const table = document.getElementById(tableId);
                    const rows = table.getElementsByTagName('tr');
                    const filter = this.value.toLowerCase();

                    for (let i = 1; i < rows.length; i++) {
                        const row = rows[i];
                        const cells = row.getElementsByTagName('td');
                        let found = false;

                        for (let cell of cells) {
                            if (cell.textContent.toLowerCase().includes(filter)) {
                                found = true;
                                break;
                            }
                        }
                        row.style.display = found ? '' : 'none';
                    }
                });
            });

            // Stats counter animation
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.innerText);
                const duration = 1000;
                const increment = target / (duration / 16);
                let current = 0;

                const updateCounter = () => {
                    current += increment;
                    counter.innerText = Math.floor(current);

                    if (current < target) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.innerText = target;
                    }
                };

                updateCounter();
            });
        });
    </script>
</body>
</html>
