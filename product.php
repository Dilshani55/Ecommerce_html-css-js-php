<?php
require_once 'config.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id == 0) {
    header('Location: index.php');
    exit;
}

// Get product details
$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit;
}

$product = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - TechStore</title>
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
            <div class="product-detail">
                <div class="product-detail-grid">
                    <div class="product-detail-image">
                        <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='images/placeholder.jpg'">
                    </div>
                    <div class="product-detail-info">
                        <h1><?php echo $product['name']; ?></h1>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <p class="stock">Stock: <?php echo $product['stock']; ?> available</p>
                        <div class="description">
                            <h3>Description</h3>
                            <p><?php echo nl2br($product['description']); ?></p>
                        </div>
                        <div class="product-actions">
                            <?php if ($product['stock'] > 0): ?>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-success">Add to Cart</button>
                            <?php else: ?>
                                <button class="btn btn-danger" disabled>Out of Stock</button>
                            <?php endif; ?>
                            <a href="index.php" class="btn btn-primary">Back to Products</a>
                        </div>
                    </div>
                </div>
            </div>
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