<?php
require_once './includes/connect.php';
session_start();

// Kiểm tra yêu cầu AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Lấy thông tin sản phẩm từ database
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
        exit;
    }
    
    // Khởi tạo giỏ hàng nếu chưa tồn tại
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
        $_SESSION['cart_total'] = 0;
    }
    
    // Thêm sản phẩm vào giỏ hàng hoặc tăng số lượng nếu đã tồn tại
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    
    // Tính tổng số sản phẩm và tổng giá trị giỏ hàng
    $total_items = array_sum($_SESSION['cart']);
    $_SESSION['cart_total'] = 0;
    
    // Tính tổng giá trị giỏ hàng
    $_SESSION['cart_total'] = 0;
    if (!empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
        
        $cart_total_stmt = $conn->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        $cart_total_stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
        $cart_total_stmt->execute();
        $cart_total_result = $cart_total_stmt->get_result();
        
        while ($product = $cart_total_result->fetch_assoc()) {
            $quantity = $_SESSION['cart'][$product['id']];
            $_SESSION['cart_total'] += $quantity * $product['price'];
        }
        
        $cart_total_stmt->close();
    }

    $stmt->close();
    $conn->close();
    
    // Trả về kết quả
    echo json_encode([
        'success' => true,
        'message' => 'Đã thêm sản phẩm vào giỏ hàng',
        'total_items' => $total_items,
        'cart_total' => $_SESSION['cart_total']
    ]);
    exit;
}

// Nếu không phải yêu cầu AJAX hợp lệ
echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);