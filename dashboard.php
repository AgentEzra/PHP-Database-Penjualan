<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();
date_default_timezone_set('Asia/Jakarta');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Food Shop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleDashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- New Header with Navigation -->
        <header class="main-header">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">
                        <i class="fas fa-utensils"></i>
                        <span>Rayhan's FoodShop</span>
                    </div>
                    <div class="tagline">Delicious Food, Anytime! 🍜</div>
                </div>
                
                <nav class="main-nav">
                    <div class="nav-links">
                        <a href="dashboard.php" class="nav-link active">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                        <!-- <a href="index.php" class="nav-link">
                            <i class="fas fa-receipt"></i>
                            <span>Orders</span>
                        </a>

                        <?php if (isUser()): ?>
                        <a href="contact.php" class="nav-link">
                            <i class="fas fa-envelope"></i>
                            <span>Contact</span>
                        </a>
                        <?php endif; ?> -->

                        <?php if (isAdmin()): ?>
                            <a href="index.php" class="nav-link">
                                <i class="fas fa-receipt"></i>
                                <span>Orders</span>

                            <a href="users.php" class="nav-link">
                                <i class="fas fa-users-cog"></i>
                                <span>Manage Users</span>
                            </a>
                            <a href="create.php" class="nav-link">
                                <i class="fas fa-plus-circle"></i>
                                <span>Create Order</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="user-section">
                        <div class="user-welcome">
                            <i class="fas fa-user-circle"></i>
                            <span>Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        </div>
                        <a href="logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Dashboard Content -->
        <main class="dashboard-main">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="banner-content">
                    <h1>Welcome to FoodShop Dashboard! 🍽️</h1>
                    <p>Manage your food orders and track business performance</p>
                    <div class="current-time">
                        <i class="fas fa-clock"></i>
                        <?= date('l, F j, Y • g:i A') ?>
                    </div>
                </div>
                <div class="banner-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <?php
                // Total Orders
                $totalOrdersQuery = "SELECT COUNT(*) as total FROM orders";
                $totalOrdersResult = mysqli_query($connect, $totalOrdersQuery);
                $totalOrders = mysqli_fetch_assoc($totalOrdersResult)['total'];

                // Total Revenue
                $totalRevenueQuery = "SELECT SUM(total_harga) as revenue FROM orders";
                $totalRevenueResult = mysqli_query($connect, $totalRevenueQuery);
                $totalRevenue = mysqli_fetch_assoc($totalRevenueResult)['revenue'] ?? 0;

                // Today's Orders
                $todayOrdersQuery = "SELECT COUNT(*) as today_orders FROM orders WHERE DATE(waktu_terjual) = CURDATE()";
                $todayOrdersResult = mysqli_query($connect, $todayOrdersQuery);
                $todayOrders = mysqli_fetch_assoc($todayOrdersResult)['today_orders'];

                // Today's Revenue
                $todayRevenueQuery = "SELECT SUM(total_harga) as today_revenue FROM orders WHERE DATE(waktu_terjual) = CURDATE()";
                $todayRevenueResult = mysqli_query($connect, $todayRevenueQuery);
                $todayRevenue = mysqli_fetch_assoc($todayRevenueResult)['today_revenue'] ?? 0;

                // Most Popular Food
                $popularFoodQuery = "SELECT p.nama_makanan, SUM(oi.quantity) as total_sold 
                                   FROM order_items oi 
                                   JOIN products p ON oi.product_id = p.id 
                                   GROUP BY p.nama_makanan 
                                   ORDER BY total_sold DESC 
                                   LIMIT 1";
                $popularFoodResult = mysqli_query($connect, $popularFoodQuery);
                $popularFood = mysqli_fetch_assoc($popularFoodResult);
                ?>

                <div class="stat-card card-primary">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Orders</h3>
                        <div class="stat-number"><?= $totalOrders ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-chart-line"></i>
                            All-time orders
                        </div>
                    </div>
                </div>

                <div class="stat-card card-success">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Revenue</h3>
                        <div class="stat-number">Rp <?= number_format($totalRevenue) ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-wallet"></i>
                            All-time revenue
                        </div>
                    </div>
                </div>

                <!-- <div class="stat-card card-warning">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Today's Orders</h3>
                        <div class="stat-number"><?= $todayOrders ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-bolt"></i>
                            Orders today
                        </div>
                    </div>
                </div> -->

                <div class="stat-card card-info">
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Today's Revenue</h3>
                        <div class="stat-number">Rp <?= number_format($todayRevenue) ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-trending-up"></i>
                            Revenue today
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Activity Section -->
            <div class="dashboard-content">
                <!-- Food Sales Chart -->
                <div class="chart-section">
                    <div class="section-header">
                        <h2><i class="fas fa-chart-bar"></i> Food Sales Overview</h2>
                        <span class="section-badge">Real-time</span>
                    </div>
                    <div class="chart-container">
                        <?php
                        $foodSalesQuery = "SELECT p.nama_makanan, SUM(oi.quantity) as total_sold, p.harga,
                                         SUM(oi.quantity * p.harga) as total_revenue
                                       FROM order_items oi 
                                       JOIN products p ON oi.product_id = p.id 
                                       GROUP BY p.nama_makanan, p.harga
                                       ORDER BY total_sold DESC";
                        $foodSalesResult = mysqli_query($connect, $foodSalesQuery);
                        
                        $maxSales = 0;
                        $foodData = [];
                        while($row = mysqli_fetch_assoc($foodSalesResult)) {
                            $foodData[] = $row;
                            if ($row['total_sold'] > $maxSales) {
                                $maxSales = $row['total_sold'];
                            }
                        }
                        
                        foreach($foodData as $food):
                            $percentage = $maxSales > 0 ? ($food['total_sold'] / $maxSales) * 100 : 0;
                        ?>
                        <div class="chart-item">
                            <div class="food-info">
                                <div class="food-name"><?= ucfirst($food['nama_makanan']) ?></div>
                                <div class="food-stats">
                                    <span class="sold-count"><?= $food['total_sold'] ?> portions</span>
                                    <span class="food-price">Rp <?= number_format($food['harga']) ?></span>
                                </div>
                            </div>
                            <div class="chart-bar-container">
                                <div class="chart-bar" style="width: <?= $percentage ?>%">
                                    <span class="bar-label"><?= $food['total_sold'] ?></span>
                                </div>
                            </div>
                            <div class="revenue">Rp <?= number_format($food['total_revenue']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="activity-section">
                    <div class="section-header">
                        <h2><i class="fas fa-bell"></i> Recent Activity</h2>
                        <span class="section-badge">Latest 5</span>
                    </div>
                    <div class="activity-list">
                        <?php
                        $recentOrdersQuery = "SELECT o.id, p.nama_makanan, o.total_harga, o.waktu_terjual, oi.quantity
                                            FROM orders o 
                                            JOIN order_items oi ON o.id = oi.order_id 
                                            JOIN products p ON oi.product_id = p.id 
                                            ORDER BY o.waktu_terjual DESC 
                                            LIMIT 5";
                        $recentOrdersResult = mysqli_query($connect, $recentOrdersQuery);
                        
                        while($order = mysqli_fetch_assoc($recentOrdersResult)):
                            $timeAgo = time_elapsed_string($order['waktu_terjual']);
                        ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    New order for <strong><?= ucfirst($order['nama_makanan']) ?></strong>
                                </div>
                                <div class="activity-details">
                                    <span class="quantity"><?= $order['quantity'] ?> portions</span>
                                    <span class="price">Rp <?= number_format($order['total_harga']) ?></span>
                                </div>
                                <div class="activity-time">
                                    <i class="fas fa-clock"></i>
                                    Order #<?= $order['id'] ?> • <?= $timeAgo ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        
                        <?php if (mysqli_num_rows($recentOrdersResult) == 0): ?>
                        <div class="activity-item empty-state">
                            <div class="activity-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">No recent activity</div>
                                <div class="activity-time">New orders will appear here</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions for Admin -->
            <?php if (isAdmin()): ?>
            <div class="quick-actions-section">
                <div class="section-header">
                    <h2><i class="fas fa-rocket"></i> Quick Actions</h2>
                </div>
                <div class="actions-grid">
                    <a href="create.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="action-content">
                            <h3>Create Order</h3>
                            <p>Add new food order</p>
                        </div>
                        <div class="action-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    
                    <a href="index.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="action-content">
                            <h3>View Orders</h3>
                            <p>Manage all orders</p>
                        </div>
                        <div class="action-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    
                    <a href="users.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="action-content">
                            <h3>Manage Users</h3>
                            <p>User administration</p>
                        </div>
                        <div class="action-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    
                    <a href="index.php?export_orders=1" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <div class="action-content">
                            <h3>Export Data</h3>
                            <p>Download reports</p>
                        </div>
                        <div class="action-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- User-specific content for regular users -->
            <?php if (!isAdmin()): ?>
            <div class="user-features">
                <div class="section-header">
                    <h2><i class="fas fa-user"></i> Your Dashboard</h2>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>View Orders</h3>
                        <p>Browse through all food orders in read-only mode</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Track Sales</h3>
                        <p>Monitor food shop performance and statistics</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Activity Feed</h3>
                        <p>Stay updated with recent orders and activities</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <?php
    // Function to display time in a friendly format
    function time_elapsed_string($datetime, $full = false) {
        if (empty($datetime)) {
            return "unknown time";
        }
        
        $now = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        if ($diff->invert === 0) {
            return "just now";
        }

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
    ?>
</body>
</html>