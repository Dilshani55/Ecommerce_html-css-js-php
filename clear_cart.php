<?php
require_once 'config.php';

$_SESSION['cart'] = [];

header('Location: cart.php');
exit;
?>