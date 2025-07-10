<?php
require_once 'config.php';

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $image = trim($_POST['image']);
        
        if (empty($name) || empty($description) || $price <= 0 || $stock < 0) {
            $message = 'Please fill in all fields correctly.';
            $message_type = 'error';
        } else {
            $conn = getConnection();
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsi", $name, $description, $price, $image, $stock);
            
            if ($stmt->execute()) {
                $message = 'Product added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error adding product.';
                $message_type = 'error';
            }
            
            $conn->close();
        }
    }
    
    if (isset($_POST['delete_product'])) {
        $product_id = intval($_POST['product_id']);
        
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            $message = 'Product deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error deleting product.';
            $message_type = 'error';
        }
        
        $conn->close();
    }
}

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
    <title>Admin Panel - TechStore</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <h1 class="logo">TechStore Admin</h1>
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
            <h2>Admin Panel</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Add Product Form -->
            <div class="cart-container">
                <h3>Add New Product</h3>
                <form method="POST" action="admin.php">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image Filename</label>
                        <input type="text" id="image" name="image" placeholder="e.g., product.jpg">
                        <small>Note: Upload images to the 'images' folder</small>
                    </div>
                    
                    <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
                </form>
            </div>
            
            <!-- Product Management -->
            <div class="cart-container">
                <h3>Manage Products</h3>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='images/placeholder.jpg'">
                            </div>
                            <div class="product-info">
                                <h4><?php echo $product['name']; ?></h4>
                                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                                <p class="stock">Stock: <?php echo $product['stock']; ?></p>
                                <p style="font-size: 0.9rem; color: #666;">
                                    <?php echo substr($product['description'], 0, 100) . '...'; ?>
                                </p>
                                <form method="POST" action="admin.php" style="margin-top: 1rem;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($products)): ?>
                    <p>No products found.</p>
                <?php endif; ?>
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