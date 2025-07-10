<?php
require_once 'config.php';

// Get all products
$conn = getConnection();
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini E-Commerce Store</title>
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
        <section class="hero">
            <div class="hero-content">
                <h2>Welcome to TechStore</h2>
                <p>Find the best tech products at amazing prices</p>
            </div>
        </section>

        <section class="products">
            <div class="container">
                <h2>Our Products</h2>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='images/placeholder.jpg'">
                            </div>
                            <div class="product-info">
                                <h3><?php echo $product['name']; ?></h3>
                                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                                <p class="stock">Stock: <?php echo $product['stock']; ?></p>
                                <div class="product-actions">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                                    <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-success">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 TechStore. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>