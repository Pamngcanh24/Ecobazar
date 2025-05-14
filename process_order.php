<?php
session_start();
require_once './includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: checkout.php');
    exit();
}

// Lấy thông tin từ form
$fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

// Tính tổng tiền đơn hàng
$subtotal = 0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $subtotal += $row['price'] * $quantity;
    }
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Thêm đơn hàng vào bảng orders
    $stmt = $conn->prepare("INSERT INTO orders (user_id, billing_name, billing_email, billing_phone, billing_address, shipping_name, shipping_email, shipping_phone, shipping_address, payment_method, subtotal, shipping_cost, total, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 'pending', NOW())");
    
    $user_id = $_SESSION['user_id'] ?? 1; // Nếu chưa đăng nhập thì gán user_id = 1
    $total = $subtotal; // Tổng tiền = subtotal vì shipping_cost = 0
    
    $stmt->bind_param("isssssssssdd", 
        $user_id, 
        $fullname, $email, $phone, $address,
        $fullname, $email, $phone, $address, // Thông tin shipping giống billing
        $payment_method,
        $subtotal,
        $total
    );
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Thêm chi tiết đơn hàng vào bảng order_items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // Lấy giá sản phẩm
        $price_stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $price_stmt->bind_param("i", $product_id);
        $price_stmt->execute();
        $price_result = $price_stmt->get_result();
        $price_row = $price_result->fetch_assoc();
        
        // Thêm vào order_items
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price_row['price']);
        $stmt->execute();
        
        // Cập nhật số lượng trong kho
        $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update_stock->bind_param("ii", $quantity, $product_id);
        $update_stock->execute();
    }

    // Commit transaction
    $conn->commit();
    
    // Xóa giỏ hàng
    unset($_SESSION['cart']);
    
    // Hiển thị thông báo thành công
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Đặt hàng thành công</title>
        <link rel="stylesheet" href="assetsHG/style.css">
        <style>
            .success-container {
                max-width: 600px;
                margin: 50px auto;
                text-align: center;
                padding: 30px;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .success-icon {
                color: #4CAF50;
                font-size: 60px;
                margin-bottom: 20px;
            }
            .success-message {
                margin-bottom: 30px;
                font-size: 24px;
                color: #333;
            }
            .order-number {
                font-size: 18px;
                color: #666;
                margin-bottom: 30px;
            }
            .back-to-shop {
                display: inline-block;
                padding: 12px 30px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .back-to-shop:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="success-message">Đặt hàng thành công!</h2>
            <p class="order-number">Mã đơn hàng của bạn: #<?php echo $order_id; ?></p>
            <a href="08shop.php" class="back-to-shop">Tiếp tục mua sắm</a>
        </div>
    </body>
    </html>
    <?php
    exit();
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    
    // Chuyển hướng về trang checkout với thông báo lỗi
    $_SESSION['error'] = "Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.";
    header('Location: checkout.php');
    exit();
}