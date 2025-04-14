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

// Get all flights
$flights_stmt = $conn->query("
    SELECT * FROM flights 
    ORDER BY departure_date DESC
");
$flights = $flights_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <title>بەڕێوەبردنی گەشتەکان | گەشتی ئاسمانی</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }
          :root {
              --primary-color: #1a237e;
              --primary-color-dark: #0d47a1;
              --text-dark: #0a192f;
              --text-light: #64748b;
              --extra-light: #f8fafc;
              --white: #ffffff;
              --sidebar-width: 250px;
              --gradient-start: #0a192f;
              --gradient-end: #1e3a8a;
          }

          * {
              font-family: '20_Sarchia_Banoka_1';
              margin: 0;
              padding: 0;
              box-sizing: border-box;
          }

          body {
              background-color: var(--extra-light);
          }

          .dashboard {
              display: flex;
              min-height: 100vh;
          }

          .sidebar {
              width: var(--sidebar-width);
              background: linear-gradient(to bottom, var(--gradient-start), var(--gradient-end));
              padding: 2rem 1.5rem;
              border-left: 1px solid rgba(0,0,0,0.05);
              position: fixed;
              height: 100vh;
          }

          .sidebar h2 {
              color: var(--white);
              margin-bottom: 2rem;
              font-size: 1.5rem;
          }

          .nav-links {
              list-style: none;
          }

          .nav-links li {
              margin-bottom: 0.875rem;
          }

          .nav-links a {
              display: flex;
              align-items: center;
              gap: 0.75rem;
              padding: 0.875rem;
              color: var(--white);
              text-decoration: none;
              border-radius: 0.5rem;
              transition: all 0.3s ease;
          }

          .nav-links a:hover {
              background-color: rgba(255, 255, 255, 0.1);
          }

          .nav-links i {
              font-size: 1.25rem;
          }

          .main-content {
              margin-right: var(--sidebar-width);
              padding: 2rem;
              width: calc(100% - var(--sidebar-width));
          }

          .header {
              display: flex;
              justify-content: space-between;
              align-items: center;
              margin-bottom: 2rem;
          }

          .data-table {
              background: var(--white);
              padding: 1.5rem;
              border-radius: 1rem;
              box-shadow: 0 2px 4px rgba(0,0,0,0.05);
          }

          .stat-card {
              background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
              color: var(--white);
          }

          .stat-card h3 {
              color: var(--extra-light);
          }

          .stat-card p {
              color: var(--white);
          }

          .btn {
              background: linear-gradient(to right, var(--gradient-start), var(--gradient-end));
          }

          .btn:hover {
              background: linear-gradient(to right, var(--gradient-end), var(--gradient-start));
          }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th {
            background-color: var(--extra-light);
            color: var(--text-light);
            font-weight: 500;
            text-align: right;
            padding: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: var(--text-dark);
        }

        .btn {
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: var(--primary-color-dark);
        }

        .btn-danger {
            background-color: #ef4444;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .status-available {
            color: #22c55e;
            font-weight: 500;
        }

        .status-full {
            color: #ef4444;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>بەڕێوەبەر</h2>
            <ul class="nav-links">
                <li><a href="admin_dashboard.php"><i class="ri-dashboard-line"></i> داشبۆرد</a></li>
                <li><a href="manage_users.php"><i class="ri-user-line"></i> بەکارهێنەران</a></li>
                <li><a href="manage_flights.php"><i class="ri-flight-takeoff-line"></i> گەشتەکان</a></li>
                <li><a href="settings.php"><i class="ri-settings-line"></i> ڕێکخستنەکان</a></li>
                <li><a href="logout.php"><i class="ri-logout-box-line"></i> چوونەدەرەوە</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <h2>بەڕێوەبردنی گەشتەکان</h2>
                <a href="add_flight.php" class="btn">زیادکردنی گەشت</a>
            </div>

            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>ژمارەی گەشت</th>
                            <th>لە</th>
                            <th>بۆ</th>
                            <th>بەرواری بەڕێکەوتن</th>
                            <th>نرخ</th>
                            <th>شوێنی بەردەست</th>
                            <th>کردار</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($flights as $flight): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($flight['flight_number']); ?></td>
                                <td><?php echo htmlspecialchars($flight['departure_city']); ?></td>
                                <td><?php echo htmlspecialchars($flight['arrival_city']); ?></td>
                                <td><?php echo htmlspecialchars($flight['departure_date']); ?></td>
                                <td>$<?php echo number_format($flight['price'], 2); ?></td>
                                <td class="status-<?php echo $flight['available_seats'] > 0 ? 'available' : 'full'; ?>">
                                    <?php echo htmlspecialchars($flight['available_seats']); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_flight.php?id=<?php echo $flight['id']; ?>" class="btn">دەستکاری</a>
                                        <a href="delete_flight.php?id=<?php echo $flight['id']; ?>" class="btn btn-danger" onclick="return confirm('دڵنیای لە سڕینەوەی ئەم گەشتە؟')">سڕینەوە</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
