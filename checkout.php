<?php
require_once 'config.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $postal_code = trim($_POST['postal_code']);
    $payment_method = $_POST['payment_method'];
    
    // Validate required fields
    if (empty($customer_name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($postal_code)) {
        $message = 'Please fill in all required fields.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $message_type = 'error';
    } elseif (empty($_SESSION['cart'])) {
        $message = 'Your cart is empty.';
        $message_type = 'error';
    } else {
        // Process the order
        $conn = getConnection();
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Calculate total
            $total = 0;
            $order_items = [];
            
            // Get cart items and validate stock
            $product_ids = array_keys($_SESSION['cart']);
            $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
            
            $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($product = $result->fetch_assoc()) {
                $quantity = $_SESSION['cart'][$product['id']];
                
                // Check if enough stock
                if ($quantity > $product['stock']) {
                    throw new Exception("Not enough stock for {$product['name']}. Available: {$product['stock']}, Requested: {$quantity}");
                }
                
                $subtotal = $product['price'] * $quantity;
                $total += $subtotal;
                
                $order_items[] = [
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            }
            
            // Create orders table if it doesn't exist
            $conn->query("CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                customer_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                address TEXT NOT NULL,
                city VARCHAR(100) NOT NULL,
                postal_code VARCHAR(20) NOT NULL,
                payment_method VARCHAR(50) NOT NULL,
                total DECIMAL(10,2) NOT NULL,
                status VARCHAR(20) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Create order_items table if it doesn't exist
            $conn->query("CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                product_name VARCHAR(255) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                quantity INT NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            )");
            
            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (customer_name, email, phone, address, city, postal_code, payment_method, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssd", $customer_name, $email, $phone, $address, $city, $postal_code, $payment_method, $total);
            $stmt->execute();
            
            $order_id = $conn->insert_id;
            
            // Insert order items and update stock
            foreach ($order_items as $item) {
                // Insert order item
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iisdid", $order_id, $item['product_id'], $item['product_name'], $item['price'], $item['quantity'], $item['subtotal']);
                $stmt->execute();
                
                // Update product stock
                $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $message = "Order placed successfully! Order ID: #$order_id. You will receive a confirmation email shortly.";
            $message_type = 'success';
            
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();
            $message = 'Error placing order: ' . $e->getMessage();
            $message_type = 'error';
        }
        
        $conn->close();
    }
}

// Get cart items for display
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $conn = getConnection();
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($product = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TechStore</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }
        
        .order-summary {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            height: fit-content;
        }
        
        .order-summary h3 {
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-details h4 {
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .item-details p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .item-total {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .order-total {
            text-align: right;
            font-size: 1.3rem;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #dee2e6;
        }
        
        .checkout-form h3 {
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        
        .checkout-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            
            .checkout-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <h1 class="logo">TechStore</h1>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cart.php">Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="admin.php">Admin</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Checkout</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($cart_items)): ?>
                <div class="cart-container">
                    <p>Your cart is empty.</p>
                    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="checkout-container">
                    <div class="checkout-grid">
                        <!-- Order Summary -->
                        <div class="order-summary">
                            <h3>Order Summary</h3>
                            <?php foreach ($cart_items as $item): ?>
                                <div class="order-item">
                                    <div class="item-details">
                                        <h4><?php echo htmlspecialchars($item['product']['name']); ?></h4>
                                        <p>Price: $<?php echo number_format($item['product']['price'], 2); ?></p>
                                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <div class="item-total">
                                        $<?php echo number_format($item['subtotal'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="order-total">
                                <strong>Total: $<?php echo number_format($total, 2); ?></strong>
                            </div>
                        </div>
                        
                        <!-- Checkout Form -->
                        <div class="checkout-form">
                            <h3>Shipping Information</h3>
                            <form method="POST" action="checkout.php">
                                <div class="form-group">
                                    <label for="customer_name">Full Name *</label>
                                    <input type="text" id="customer_name" name="customer_name" required 
                                           value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" required 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" required 
                                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="address">Address *</label>
                                    <textarea id="address" name="address" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" id="city" name="city" required 
                                           value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="postal_code">Postal Code *</label>
                                    <input type="text" id="postal_code" name="postal_code" required 
                                           value="<?php echo isset($_POST['postal_code']) ? htmlspecialchars($_POST['postal_code']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="payment_method">Payment Method *</label>
                                    <select id="payment_method" name="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="credit_card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card') ? 'selected' : ''; ?>>Credit Card</option>
                                        <option value="debit_card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'debit_card') ? 'selected' : ''; ?>>Debit Card</option>
                                        <option value="paypal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                                        <option value="cash_on_delivery" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cash_on_delivery') ? 'selected' : ''; ?>>Cash on Delivery</option>
                                    </select>
                                </div>
                                
                                <div class="checkout-actions">
                                    <a href="cart.php" class="btn btn-primary">Back to Cart</a>
                                    <button type="submit" class="btn btn-success">Place Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 TechStore. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>