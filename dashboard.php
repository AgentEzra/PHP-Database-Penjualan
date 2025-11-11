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
    <title>Dashboard - Coffee Shop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleDashboard.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Cafe Ngoding</h1>
            <div class="user-info">
                Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="index.php">Orders</a>

            <?php if (isUser()): ?>
            <a href="contact.php">Contact</a>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <a href="users.php">Manage Users</a>
                <a href="create.php">Create Order</a>
            <?php endif; ?>
        </div>

        <!-- Statistics Cards -->
        <div class="dashboard">
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

            // Most Popular Coffee
            $popularCoffeeQuery = "SELECT p.nama_kopi, SUM(oi.quantity) as total_sold 
                                 FROM order_items oi 
                                 JOIN products p ON oi.product_id = p.id 
                                 GROUP BY p.nama_kopi 
                                 ORDER BY total_sold DESC 
                                 LIMIT 1";
            $popularCoffeeResult = mysqli_query($connect, $popularCoffeeQuery);
            $popularCoffee = mysqli_fetch_assoc($popularCoffeeResult);
            ?>

            <div class="stat-card card-primary">
                <h3>Total Orders</h3>
                <div class="stat-number"><?= $totalOrders ?></div>
                <div class="stat-description">All-time orders</div>
            </div>

            <div class="stat-card card-success">
                <h3>Total Revenue</h3>
                <div class="stat-number">Rp <?= number_format($totalRevenue) ?></div>
                <div class="stat-description">All-time revenue</div>
            </div>

            <div class="stat-card card-warning">
                <h3>Today's Orders</h3>
                <div class="stat-number"><?= $todayOrders ?></div>
                <div class="stat-description">Orders today</div>
            </div>

            <div class="stat-card card-info">
                <h3>Today's Revenue</h3>
                <div class="stat-number">Rp <?= number_format($todayRevenue) ?></div>
                <div class="stat-description">Revenue today</div>
            </div>
        </div>

        <!-- <?php if (isAdmin()): ?>
        //Quick Actions for Admin
        <div class="quick-actions">
            <a href="create.php" class="action-btn">
                <span>➕</span>
                <span>Create New Order</span>
            </a>
            <a href="users.php" class="action-btn">
                <span>👥</span>
                <span>Manage Users</span>
            </a>
            <a href="index.php" class="action-btn">
                <span>📋</span>
                <span>View All Orders</span>
            </a>
            <a href="#" class="action-btn" onclick="alert('Reports feature coming soon!')">
                <span>📊</span>
                <span>Generate Reports</span>
            </a>
        </div> -->

        <!-- Coffee Sales Chart -->
        <div class="chart-container">
            <h2>☕ Coffee Sales Overview</h2>
            <div class="simple-chart">
                <?php
                $coffeeSalesQuery = "SELECT p.nama_kopi, SUM(oi.quantity) as total_sold 
                                   FROM order_items oi 
                                   JOIN products p ON oi.product_id = p.id 
                                   GROUP BY p.nama_kopi 
                                   ORDER BY total_sold DESC";
                $coffeeSalesResult = mysqli_query($connect, $coffeeSalesQuery);
                
                $maxSales = 0;
                $coffeeData = [];
                while($row = mysqli_fetch_assoc($coffeeSalesResult)) {
                    $coffeeData[] = $row;
                    if ($row['total_sold'] > $maxSales) {
                        $maxSales = $row['total_sold'];
                    }
                }
                
                foreach($coffeeData as $coffee):
                    $percentage = $maxSales > 0 ? ($coffee['total_sold'] / $maxSales) * 100 : 0;
                ?>
                <div>
                    <div style="margin-bottom: 0.5rem; font-weight: 600; color: #8B4513;">
                        <?= ucfirst($coffee['nama_kopi']) ?>
                    </div>
                    <div class="chart-bar" style="height: <?= max(30, $percentage) ?>px; display: flex; align-items: center; justify-content: center;">
                        <?= $coffee['total_sold'] ?> cups
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h2>📈 Recent Activity</h2>
            <ul class="activity-list">
                <?php
                $recentOrdersQuery = "SELECT o.id, p.nama_kopi, o.total_harga, o.waktu_terjual 
                                    FROM orders o 
                                    JOIN order_items oi ON o.id = oi.order_id 
                                    JOIN products p ON oi.product_id = p.id 
                                    ORDER BY o.waktu_terjual DESC 
                                    LIMIT 5";
                $recentOrdersResult = mysqli_query($connect, $recentOrdersQuery);
                
                while($order = mysqli_fetch_assoc($recentOrdersResult)):
                    $timeAgo = time_elapsed_string($order['waktu_terjual']);
                ?>
                <li class="activity-item">
                    <div class="activity-icon">☕</div>
                    <div class="activity-content">
                        <div class="activity-title">
                            New order for <?= ucfirst($order['nama_kopi']) ?> - Rp <?= number_format($order['total_harga']) ?>
                        </div>
                        <div class="activity-time">Order #<?= $order['id'] ?> • <?= $timeAgo ?></div>
                    </div>
                </li>
                <?php endwhile; ?>
                
                <?php if (mysqli_num_rows($recentOrdersResult) == 0): ?>
                <li class="activity-item">
                    <div class="activity-content">
                        <div class="activity-title">No recent activity</div>
                        <div class="activity-time">Orders will appear here</div>
                    </div>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- User-specific content for regular users -->
        <?php if (!isAdmin()): ?>
        <div class="user-welcome">
            <h2>Welcome to Cafe Ngoding Website! ☕</h2>
            <p>Thanks for visiting our website. Here's what you can do :</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
                <div class="stat-card">
                    <h3>View Orders</h3>
                    <p>Browse through all orders (read-only)</p>
                </div>
                <div class="stat-card">
                    <h3>Track Sales</h3>
                    <p>See coffee shop performance</p>
                </div>
                <div class="stat-card">
                    <h3>Activity Feed</h3>
                    <p>Stay updated with recent orders</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php
    // Function to display time in a friendly format
    function time_elapsed_string($datetime, $full = false) {
    // Ensure the input is a valid datetime
    if (empty($datetime)) {
        return "unknown time";
    }
    
    $now = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // If the time is in future (shouldn't happen), handle it
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