<?php
require_once 'config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $user_message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($user_message)) {
        $message = 'Please fill in all required fields.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $message_type = 'error';
    } else {
        // In a real application, you would send an email or store in database
        // For this demo, we'll just show a success message
        $message = 'Thank you for your message! We will get back to you soon.';
        $message_type = 'success';
        
        // Clear form data
        $_POST = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TechStore</title>
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
            <h2>Contact Us</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="cart-container">
                <form method="POST" action="contact.php">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" required value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Send Message</button>
                </form>
            </div>
            
            <div class="cart-container" style="margin-top: 2rem;">
                <h3>Our Store Information</h3>
                <p><strong>Address:</strong> 123 Tech Street, Digital City, Sri Lanka</p>
                <p><strong>Phone:</strong> (+94) 11123-4567</p>
                <p><strong>Email:</strong> info@techstore.com</p>
                <p><strong>Hours:</strong> Monday - Friday: 9AM - 6PM, Saturday: 10AM - 4PM</p>
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