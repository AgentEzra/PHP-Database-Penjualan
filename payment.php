<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();

// Check if there's a pending order
if (!isset($_SESSION['pending_order']) || empty($_SESSION['pending_order']['items'])) {
    header('Location: order.php');
    exit();
}

$pending_order = $_SESSION['pending_order'];

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
    $payment_method = mysqli_real_escape_string($connect, $_POST['payment_method']);
    
    // Start transaction
    mysqli_begin_transaction($connect);
    
    try {
        // Insert into orders table
        $insert_order_query = "INSERT INTO orders (total_harga, waktu_terjual) VALUES (?, NOW())";
        $stmt = mysqli_prepare($connect, $insert_order_query);
        mysqli_stmt_bind_param($stmt, "d", $pending_order['total_amount']);
        mysqli_stmt_execute($stmt);
        
        $order_id = mysqli_insert_id($connect);
        
        // Insert order items
        foreach ($pending_order['items'] as $item) {
            $insert_item_query = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connect, $insert_item_query);
            mysqli_stmt_bind_param($stmt, "iii", $order_id, $item['product_id'], $item['quantity']);
            mysqli_stmt_execute($stmt);
        }
        
        // Commit transaction
        mysqli_commit($connect);
        
        // Clear pending order
        unset($_SESSION['pending_order']);
        
        // Redirect to success page or dashboard
        $_SESSION['order_success'] = true;
        header('Location: dashboard.php');
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($connect);
        $error = "Failed to process order. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Cafe Ngoding</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleDashboard.css">
    <style>
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .payment-header h1 {
            color: #8B4513;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .order-summary {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border: 2px solid #8B4513;
        }

        .order-summary h2 {
            color: #8B4513;
            margin-bottom: 20px;
            text-align: center;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e1e1e1;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-total {
            border-top: 2px solid #8B4513;
            padding-top: 15px;
            margin-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .grand-total {
            font-size: 1.4rem;
            font-weight: bold;
            color: #8B4513;
        }

        .payment-methods {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .payment-methods h2 {
            color: #8B4513;
            margin-bottom: 20px;
            text-align: center;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: #8B4513;
            background: #f9f5f0;
        }

        .payment-option.selected {
            border-color: #8B4513;
            background: #f9f5f0;
        }

        .payment-option input[type="radio"] {
            margin-right: 15px;
            transform: scale(1.2);
        }

        .payment-icon {
            font-size: 2rem;
            margin-right: 15px;
        }

        .payment-details {
            flex: 1;
        }

        .payment-details h3 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .payment-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .process-payment-btn {
            width: 100%;
            padding: 15px;
            background: #8B4513;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .process-payment-btn:hover {
            background: #654321;
        }

        .back-to-order {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-order a {
            color: #8B4513;
            text-decoration: none;
            font-weight: 600;
        }

        .back-to-order a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
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
            <a href="order.php">Order</a>
            
            <?php if (isUser()): ?>
                <a href="contact.php">Contact</a>
            <?php endif; ?>
                
            <?php if (isAdmin()): ?>
                <a href="index.php">Manage Orders</a>
                <a href="users.php">Manage Users</a>
                <a href="create.php">Create Order</a>
            <?php endif; ?>
        </div>

        <div class="payment-container">
            <div class="payment-header">
                <h1>Payment</h1>
                <p>Complete your order by selecting a payment method</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Order Summary -->
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <?php foreach ($pending_order['items'] as $item): ?>
                    <div class="order-item">
                        <div class="item-name"><?= htmlspecialchars($item['nama_kopi']) ?></div>
                        <div class="item-quantity"><?= $item['quantity'] ?>x</div>
                        <div class="item-price">Rp <?= number_format($item['item_total']) ?></div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="order-total">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>Rp <?= number_format($pending_order['total_amount']) ?></span>
                        </div>
                        <div class="total-row">
                            <span>Tax (10%):</span>
                            <span>Rp <?= number_format($pending_order['total_amount'] * 0.10) ?></span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Total:</span>
                            <span>Rp <?= number_format($pending_order['total_amount'] * 1.10) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="payment-methods">
                    <h2>Select Payment Method</h2>
                    
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cash" required>
                        <div class="payment-icon">💵</div>
                        <div class="payment-details">
                            <h3>Cash</h3>
                            <p>Pay with cash when you pick up your order</p>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="qris">
                        <div class="payment-icon">📱</div>
                        <div class="payment-details">
                            <h3>QRIS</h3>
                            <p>Scan QR code with your mobile banking app</p>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="gopay">
                        <div class="payment-icon">⚡</div>
                        <div class="payment-details">
                            <h3>GoPay</h3>
                            <p>Pay using your GoPay wallet</p>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="ovo">
                        <div class="payment-icon">💜</div>
                        <div class="payment-details">
                            <h3>OVO</h3>
                            <p>Pay using your OVO wallet</p>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="dana">
                        <div class="payment-icon">💙</div>
                        <div class="payment-details">
                            <h3>DANA</h3>
                            <p>Pay using your DANA wallet</p>
                        </div>
                    </label>
                </div>

                <button type="submit" name="process_payment" class="process-payment-btn">
                    Complete Order
                </button>
            </form>

            <div class="back-to-order">
                <a href="order.php">← Back to Order</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentOptions = document.querySelectorAll('.payment-option');
            
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    
                    // Remove selected class from all options
                    paymentOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to current option
                    this.classList.add('selected');
                });
            });
        });
    </script>
</body>
</html>