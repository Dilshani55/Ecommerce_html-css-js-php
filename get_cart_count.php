<?php
require_once 'config.php';

header('Content-Type: application/json');

$count = count($_SESSION['cart']);
echo json_encode(['count' => $count]);
?>