<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$booking_stmt = $conn->prepare("
    SELECT b.*, f.destination 
    FROM bookings b
    LEFT JOIN flights f ON b.flight_id = f.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$booking_stmt->execute([$_SESSION['user_id']]);
$bookings = $booking_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>داشبۆردی بەکارهێنەر | گەشتی ئاسمانی</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @keyframes slideIn {
            from { transform: translateX(50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        :root {
            --primary-color:rgb(0, 24, 96);
            --primary-color-dark: #334c99;
            --text-dark: #0f172a;
            --text-light: #64748b;
            --extra-light: #f1f5f9;
            --white: #ffffff;
            --max-width: 1200px;
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(25deg,rgb(36, 197, 255),rgb(0, 0, 0));
        }

        nav {
            border-radius: 15px;
            max-width: var(--max-width);
            margin: auto;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        nav:hover {
            box-shadow: 0 5px 20px rgba(61, 92, 184, 0.15);
        }

        .nav__logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: float 3s ease-in-out infinite;
        }

        .nav__logo i {
            color: var(--primary-color);
        }

        .nav__links {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav__links .link {
            animation: slideIn 0.5s ease-out forwards;
            opacity: 0;
        }

        .nav__links .link:nth-child(1) { animation-delay: 0.1s; }
        .nav__links .link:nth-child(2) { animation-delay: 0.2s; }
        .nav__links .link:nth-child(3) { animation-delay: 0.3s; }

        .link a {
            text-decoration: none;
            font-weight: 500;
            color: var(--text-light);
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
        }

        .link a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .link a:hover::after {
            width: 100%;
        }

        .btn {
            padding: 0.75rem 2rem;
            outline: none;
            border: none;
            font-size: 1rem;
            font-weight: 500;
            color: var(--white);
            background-color: var(--primary-color);
            border-radius: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(61, 92, 184, 0.3);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn:active::after {
            width: 200px;
            height: 200px;
        }

        .section__container {
            max-width: var(--max-width);
            margin: auto;
            padding: 5rem 1rem;
            animation: fadeUp 0.8s ease-out;
        }

        .booking-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.5s ease-out forwards;
            transition: all 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 30px rgba(61, 92, 184, 0.1);
        }

        .booking-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }

        .booking-details {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .booking-details i {
            color: var(--primary-color);
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .booking-card:hover .fas {
            transform: scale(1.2) rotate(5deg);
        }

        .profile-section {
            background: var(--white);
            padding: 2rem;
            border-radius: 1rem;
            margin-top: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            animation: fadeUp 1s ease-out;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--text-light);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            transform: scale(1.01);
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(61, 92, 184, 0.1);
            outline: none;
        }

        .section__header {
            font-size: 2.5rem;
            font-weight: 600;
            line-height: 3rem;
            color: var(--text-dark);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: fadeUp 0.8s ease-out;
        }

        .header__container img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 1rem;
            animation: fadeUp 1s ease-out;
        }
        a{
            text-decoration: none;
        }
        .gradient-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    background: linear-gradient(45deg, #1e3c72 0%, #000000 100%);
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 10px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.gradient-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
}

    </style>
</head>
<body><br>
    <nav>
        <div class="nav__logo">
            <i class="fas fa-plane"></i>
            سلێمانی
        </div>
        <ul class="nav__links">
        <li class="link"><a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> داشبۆرد</a></li>
<li class="link"><a href="my_bookings.php"><i class="fas fa-plane-departure"></i> گەشتەکانم</a></li>


        </ul>
        <a style=" background: linear-gradient(135deg,rgb(133, 0, 0) 0%,rgb(69, 32, 26) 100%);" href="logout.php" class="btn"><i class="fas fa-sign-out-alt"></i> چوونەدەرەوە</a>
    </nav>

    <header class="section__container header__container">
        <div class="carousel-item active">
            <img src="assets/header.jpg" alt="header" />
        </div>
    </header>
      <div class="section__container">
          <div class="welcome-banner" >
              <div class="welcome-content">
                
                  <h1 class="section__header" style="color: white;">
 <?php echo htmlspecialchars($user['username']); ?>
                  </h1>
              </div>
              <div class="welcome-stats">
                  <div class="stat-card">
                      <i class="fas fa-plane-departure"></i>
                      <span>گەشتەکان</span>
                      <h3><?php echo count($bookings); ?></h3>
                  </div>
              </div>
          </div>

          <div class="bookings glass-effect">
              <div class="section-title">
                  <i class="fas fa-ticket-alt rotating-icon"></i>
                  <h2>گەشتەکانی من</h2>
              </div>
        
              <div class="bookings-grid">
                  <?php if ($bookings): ?>
                      <?php foreach($bookings as $booking): ?>
                          <div class="booking-card hover-effect">
                              <div class="booking-status-badge">
                                  <?php echo htmlspecialchars($booking['status']); ?>
                              </div>
                              <div class="booking-header">
                                  <i class="fas fa-plane-departure fa-2x floating-icon"></i>
                                  <h3><?php echo htmlspecialchars($booking['destination']); ?></h3>
                              </div>
                              <div class="booking-details">
                                  <div class="detail-item">
                                      <i class="far fa-calendar-alt pulse-icon"></i>
                                      <p>بەروار: <?php echo htmlspecialchars($booking['booking_date']); ?></p>
                                  </div>
                              </div>
                              <a href="view_booking.php?id=<?php echo $booking['id']; ?>" class="gradient-btn">
    <i class="fas fa-eye"></i>
    <span>بینینی وردەکارییەکان</span>
</a>

                              
                          </div>
                          
                      <?php endforeach; ?>
                      
                  <?php else: ?>
                      <div class="empty-state">
                          <i class="fas fa-plane-slash"></i>
                          <p>هیچ گەشتێکت نییە</p>
                          <button class="gradient-btn" onclick="window.location.href='book_flight.php'">داواکردنی گەشت</button>

                      </div>
                  <?php endif; ?>
              </div>
          </div>

          
      </div>

      <style>
        :root {
    --gradient-primary: linear-gradient(135deg, #0F2027 0%, #203A43 50%, #2C5364 100%);
    --gradient-secondary: linear-gradient(45deg, #000000, #1e3c72);
    --primary-color: #1e3c72;
    --text-dark: #0f172a;
    --text-light: #64748b;
    --white: #ffffff;
    --max-width: 1200px;
}

.welcome-banner {
    background: var(--gradient-primary);
    box-shadow: 0 10px 30px rgba(15, 32, 39, 0.3);
}

.gradient-btn {
    background: var(--gradient-secondary);
    box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
}

.gradient-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 32, 39, 0.4);
}

.booking-status-badge {
    background: rgba(30, 60, 114, 0.1);
    color: #1e3c72;
}

.glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(15, 32, 39, 0.15);
}

.booking-card {
    border: 1px solid rgba(30, 60, 114, 0.1);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);
}

.booking-card:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 1) 100%);
    box-shadow: 0 15px 35px rgba(15, 32, 39, 0.2);
}

@keyframes float {
    0% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(5deg); }
    100% { transform: translateY(0) rotate(0deg); }
}

@keyframes pulse {
    0% { transform: scale(1); filter: brightness(100%); }
    50% { transform: scale(1.05); filter: brightness(110%); }
    100% { transform: scale(1); filter: brightness(100%); }
}

.nav__logo i, .booking-header i {
    background: var(--gradient-secondary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: pulse 3s infinite;
}

.btn {
    background: var(--gradient-secondary);
}

.btn:hover {
    background: linear-gradient(45deg, #1e3c72, #000000);
}

      .welcome-banner {
          background: linear-gradient(135deg,rgb(0, 0, 0) 0%,rgb(0, 6, 125) 100%);
          border-radius: 20px;
          padding: 2rem;
          color: white;
          margin-bottom: 2rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
      }

      .glass-effect {
          background: rgba(255, 255, 255, 0.95);
          backdrop-filter: blur(10px);
          border-radius: 20px;
          padding: 2rem;
          box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
      }

      .bookings-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
          gap: 1.5rem;
          padding: 1rem;
      }

      .gradient-btn {
        background: linear-gradient(135deg,rgb(0, 0, 0) 0%,rgb(0, 6, 125) 100%);
          color: white;
          border: none;
          padding: 0.8rem 1.5rem;
          border-radius: 10px;
          cursor: pointer;
          transition: transform 0.3s ease;
          display: flex;
          align-items: center;
          gap: 0.5rem;
      }

      .gradient-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 5px 15px rgba(107, 115, 255, 0.3);
      }

      .floating-icon {
          animation: float 3s ease-in-out infinite;
      }

      .pulse-icon {
          animation: pulse 2s ease-in-out infinite;
      }

      .rotating-icon {
          animation: rotate 4s linear infinite;
      }

      @keyframes float {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-10px); }
      }

      @keyframes pulse {
          0%, 100% { transform: scale(1); }
          50% { transform: scale(1.1); }
      }

      @keyframes rotate {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
      }

      .modern-form .input-icon {
          position: relative;
          margin-bottom: 1.5rem;
      }

      .modern-form .input-icon i {
          position: absolute;
          left: 1rem;
          top: 50%;
          transform: translateY(-50%);
          color: #6B73FF;
      }

      .modern-form input {
          width: 100%;
          padding: 1rem 2.5rem;
          border: 2px solid #eee;
          border-radius: 10px;
          transition: all 0.3s ease;
      }

      .modern-form input:focus {
          border-color: #6B73FF;
          box-shadow: 0 0 0 3px rgba(107, 115, 255, 0.2);
      }

      .booking-status-badge {
          position: absolute;
          top: 1rem;
          left: 1rem; /* Changed from right to left */
          padding: 0.5rem 1rem;
          border-radius: 20px;
          background: rgba(30, 60, 114, 0.1);
          color: #1e3c72;
          transition: all 0.3s ease;
      }

      .booking-status-badge:hover {
          transform: translateY(-2px);
          background: rgba(30, 60, 114, 0.2);
      }
      </style>
</html>
