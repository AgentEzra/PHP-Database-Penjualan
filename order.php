<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Process the order
    $total_amount = 0;
    $order_items = [];
    
    // Calculate total and prepare order items
    foreach ($_POST['items'] as $product_id => $quantity) {
        if ($quantity > 0) {
            // Get product details
            $product_query = "SELECT * FROM products WHERE id = ?";
            $stmt = mysqli_prepare($connect, $product_query);
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $product_result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($product_result);
            
            if ($product) {
                $item_total = $product['harga'] * $quantity;
                $total_amount += $item_total;
                
                $order_items[] = [
                    'product_id' => $product_id,
                    'nama_kopi' => $product['nama_kopi'],
                    'quantity' => $quantity,
                    'harga' => $product['harga'],
                    'item_total' => $item_total
                ];
            }
        }
    }
    
    if (count($order_items) > 0) {
        // Store order data in session for payment page
        $_SESSION['pending_order'] = [
            'items' => $order_items,
            'total_amount' => $total_amount,
            'order_time' => date('Y-m-d H:i:s')
        ];
        
        // Redirect to payment page
        header('Location: payment.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Coffee - Cafe Ngoding</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleDashboard.css">
    <style>
        .menu-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .menu-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .menu-header h1 {
            color: #8B4513;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .menu-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .menu-categories {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .category-btn {
            padding: 10px 20px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .category-btn.active,
        .category-btn:hover {
            background: #8B4513;
            color: white;
            border-color: #8B4513;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .menu-item {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e1e1e1;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .menu-item-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #8B4513, #D2691E);
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }

        .menu-item h3 {
            color: #8B4513;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }

        .menu-item p {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .menu-item-price {
            font-size: 1.4rem;
            font-weight: bold;
            color: #2c5e2e;
            margin-bottom: 15px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .quantity-btn {
            width: 35px;
            height: 35px;
            border: 2px solid #8B4513;
            background: white;
            color: #8B4513;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: #8B4513;
            color: white;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            padding: 8px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
        }

        .order-summary {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
            border: 2px solid #8B4513;
        }

        .order-summary h2 {
            color: #8B4513;
            margin-bottom: 20px;
            text-align: center;
        }

        .order-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e1e1e1;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-name {
            flex: 1;
            font-weight: 600;
        }

        .order-item-quantity {
            margin: 0 15px;
            font-weight: bold;
        }

        .order-item-price {
            color: #2c5e2e;
            font-weight: bold;
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

        .checkout-btn {
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
            margin-top: 20px;
        }

        .checkout-btn:hover {
            background: #654321;
        }

        .checkout-btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }

        .empty-cart {
            text-align: center;
            color: #666;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .menu-container {
                padding: 10px;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
            }
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

        <div class="menu-container">
            <div class="menu-header">
                <h1>Our Coffee Menu</h1>
                <p>Select your favorite coffee and customize your order</p>
            </div>

            <div class="menu-categories">
                <button class="category-btn active" data-category="all">All Coffees</button>
                <button class="category-btn" data-category="hot">Hot Coffee</button>
                <button class="category-btn" data-category="cold">Cold Coffee</button>
                <button class="category-btn" data-category="special">Specialty</button>
            </div>

            <form method="POST" action="" id="order-form">
                <div class="content-wrapper" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                    <!-- Menu Items -->
                    <div class="menu-section">
                        <div class="menu-grid" id="menu-items">
                            <?php
                            // Fetch all coffee products
                            $query = "SELECT * FROM products WHERE status = 'available' ORDER BY nama_kopi";
                            $result = mysqli_query($connect, $query);
                            
                            while($coffee = mysqli_fetch_assoc($result)):
                                // Determine category based on name (you can add a category field to your products table)
                                $category = 'hot';
                                if (stripos($coffee['nama_kopi'], 'ice') !== false || stripos($coffee['nama_kopi'], 'cold') !== false) {
                                    $category = 'cold';
                                } elseif (stripos($coffee['nama_kopi'], 'special') !== false || stripos($coffee['nama_kopi'], 'premium') !== false) {
                                    $category = 'special';
                                }
                            ?>
                            <div class="menu-item" data-category="<?= $category ?>">
                                <div class="menu-item-image">
                                    ☕
                                </div>
                                <h3><?= htmlspecialchars(ucfirst($coffee['nama_kopi'])) ?></h3>
                                <p>Freshly brewed <?= strtolower($coffee['nama_kopi']) ?> with premium beans</p>
                                <div class="menu-item-price">Rp <?= number_format($coffee['harga']) ?></div>
                                
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn minus" data-product="<?= $coffee['id'] ?>">-</button>
                                    <input type="number" 
                                           name="items[<?= $coffee['id'] ?>]" 
                                           value="0" 
                                           min="0" 
                                           max="10" 
                                           class="quantity-input"
                                           data-price="<?= $coffee['harga'] ?>"
                                           data-name="<?= htmlspecialchars($coffee['nama_kopi']) ?>">
                                    <button type="button" class="quantity-btn plus" data-product="<?= $coffee['id'] ?>">+</button>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            
                            <?php if (mysqli_num_rows($result) == 0): ?>
                            <div class="empty-cart">
                                <h3>No coffee available at the moment</h3>
                                <p>Please check back later!</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-section">
                        <div class="order-summary">
                            <h2>Your Order</h2>
                            <div class="order-items" id="order-items">
                                <div class="empty-cart" id="empty-cart-message">
                                    <p>Your cart is empty</p>
                                    <p>Add items from the menu</p>
                                </div>
                            </div>
                            
                            <div class="order-total">
                                <div class="total-row">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">Rp 0</span>
                                </div>
                                <div class="total-row">
                                    <span>Tax (10%):</span>
                                    <span id="tax">Rp 0</span>
                                </div>
                                <div class="total-row grand-total">
                                    <span>Total:</span>
                                    <span id="grand-total">Rp 0</span>
                                </div>
                            </div>
                            
                            <button type="submit" name="place_order" class="checkout-btn" id="checkout-btn" disabled>
                                Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const minusButtons = document.querySelectorAll('.quantity-btn.minus');
            const plusButtons = document.querySelectorAll('.quantity-btn.plus');
            const orderItemsContainer = document.getElementById('order-items');
            const emptyCartMessage = document.getElementById('empty-cart-message');
            const subtotalElement = document.getElementById('subtotal');
            const taxElement = document.getElementById('tax');
            const grandTotalElement = document.getElementById('grand-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            const categoryButtons = document.querySelectorAll('.category-btn');
            const menuItems = document.querySelectorAll('.menu-item');

            let cart = {};

            // Category filtering
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Update active button
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter items
                    menuItems.forEach(item => {
                        if (category === 'all' || item.getAttribute('data-category') === category) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Quantity controls
            minusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product');
                    const input = document.querySelector(`input[name="items[${productId}]"]`);
                    if (parseInt(input.value) > 0) {
                        input.value = parseInt(input.value) - 1;
                        updateCart();
                    }
                });
            });

            plusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product');
                    const input = document.querySelector(`input[name="items[${productId}]"]`);
                    if (parseInt(input.value) < 10) {
                        input.value = parseInt(input.value) + 1;
                        updateCart();
                    }
                });
            });

            // Input change events
            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value < 0) this.value = 0;
                    if (this.value > 10) this.value = 10;
                    updateCart();
                });
            });

            function updateCart() {
                cart = {};
                let subtotal = 0;

                quantityInputs.forEach(input => {
                    const quantity = parseInt(input.value);
                    if (quantity > 0) {
                        const productId = input.name.match(/\[(.*?)\]/)[1];
                        const price = parseFloat(input.getAttribute('data-price'));
                        const name = input.getAttribute('data-name');
                        const itemTotal = price * quantity;
                        
                        cart[productId] = {
                            name: name,
                            quantity: quantity,
                            price: price,
                            total: itemTotal
                        };
                        
                        subtotal += itemTotal;
                    }
                });

                updateOrderSummary(subtotal);
            }

            function updateOrderSummary(subtotal) {
                const tax = subtotal * 0.10; // 10% tax
                const grandTotal = subtotal + tax;

                // Update totals
                subtotalElement.textContent = 'Rp ' + subtotal.toLocaleString();
                taxElement.textContent = 'Rp ' + tax.toLocaleString();
                grandTotalElement.textContent = 'Rp ' + grandTotal.toLocaleString();

                // Update order items
                orderItemsContainer.innerHTML = '';
                
                if (Object.keys(cart).length === 0) {
                    orderItemsContainer.appendChild(emptyCartMessage);
                    emptyCartMessage.style.display = 'block';
                    checkoutBtn.disabled = true;
                } else {
                    emptyCartMessage.style.display = 'none';
                    checkoutBtn.disabled = false;
                    
                    Object.values(cart).forEach(item => {
                        const orderItem = document.createElement('div');
                        orderItem.className = 'order-item';
                        orderItem.innerHTML = `
                            <div class="order-item-name">${item.name}</div>
                            <div class="order-item-quantity">${item.quantity}x</div>
                            <div class="order-item-price">Rp ${item.total.toLocaleString()}</div>
                        `;
                        orderItemsContainer.appendChild(orderItem);
                    });
                }
            }

            // Form submission validation
            document.getElementById('order-form').addEventListener('submit', function(e) {
                if (Object.keys(cart).length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to your order.');
                    return false;
                }
                
                // Additional validation can be added here
                return true;
            });

            // Initialize cart
            updateCart();
        });
    </script>
</body>
</html>