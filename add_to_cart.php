<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    
    // Check if product exists
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT id, stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Check stock
        $current_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
        
        if ($current_quantity < $product['stock']) {
            // Add to cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]++;
            } else {
                $_SESSION['cart'][$product_id] = 1;
            }
            
            echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not enough stock']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>