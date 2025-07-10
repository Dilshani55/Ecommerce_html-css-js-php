<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity <= 0) {
        // Remove from cart
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
    } else {
        // Check stock
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            
            if ($quantity <= $product['stock']) {
                $_SESSION['cart'][$product_id] = $quantity;
                echo json_encode(['success' => true, 'message' => 'Cart updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Not enough stock']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
        
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>