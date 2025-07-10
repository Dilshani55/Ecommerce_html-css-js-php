<?php
require_once 'config.php';

// Get cart items
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
    <title>Shopping Cart - TechStore</title>
    <link rel="stylesheet" href="style.css">
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
            <h2>Shopping Cart</h2>
            
            <?php if (empty($cart_items)): ?>
                <div class="cart-container">
                    <p>Your cart is empty.</p>
                    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="item-info">
                                <img src="images/<?php echo $item['product']['image']; ?>" alt="<?php echo $item['product']['name']; ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;" onerror="this.src='images/placeholder.jpg'">
                                <div>
                                    <h4><?php echo $item['product']['name']; ?></h4>
                                    <p>Price: $<?php echo number_format($item['product']['price'], 2); ?></p>
                                </div>
                            </div>
                            <div class="item-controls">
                                <div class="quantity-controls">
                                    <button onclick="updateQuantity(<?php echo $item['product']['id']; ?>, <?php echo $item['quantity'] - 1; ?>)" class="btn btn-primary">-</button>
                                    <span style="margin: 0 1rem;">Qty: <?php echo $item['quantity']; ?></span>
                                    <button onclick="updateQuantity(<?php echo $item['product']['id']; ?>, <?php echo $item['quantity'] + 1; ?>)" class="btn btn-primary">+</button>
                                </div>
                                <p>Subtotal: $<?php echo number_format($item['subtotal'], 2); ?></p>
                                <button onclick="removeFromCart(<?php echo $item['product']['id']; ?>)" class="btn btn-danger">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="cart-total">
                        <strong>Total: $<?php echo number_format($total, 2); ?></strong>
                    </div>
                    
                    <div class="cart-actions" style="margin-top: 2rem;">
                        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                        <a href="clear_cart.php" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</a>
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