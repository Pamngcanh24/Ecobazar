<?php
require_once './includes/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {


    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $quantity = (int)$quantity;
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    // Tính tổng giá trị giỏ hàng
    $_SESSION['cart_total'] = 0;
    if (!empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
        
        $stmt = $conn->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($product = $result->fetch_assoc()) {
            $quantity = $_SESSION['cart'][$product['id']];
            $_SESSION['cart_total'] += $quantity * $product['price'];
        }
        
        $stmt->close();
    }

    $conn->close();
}

header('Location: 15shopping.php');
exit();