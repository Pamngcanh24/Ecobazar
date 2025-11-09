<?php
session_start();
require_once './includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: checkout.php');
    exit();
}

// Hàm tạo order code
function generateOrderCode() {
    global $conn;
    $today = date('Ymd'); // Format: YYYYMMDD
    
    // Đếm số đơn trong ngày
    $sql = "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $orderNumber = $row['count'] + 1;
    
    // Format: ODyyyymmdd-XX (XX là số thứ tự đơn trong ngày)
    return 'OD' . $today . '-' . str_pad($orderNumber, 2, '0', STR_PAD_LEFT);
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
    // Tạo order code
    $order_code = generateOrderCode();
    
    // Thêm đơn hàng vào bảng orders (thêm trường order_code)
    $stmt = $conn->prepare("INSERT INTO orders (order_code, user_id, billing_name, billing_email, billing_phone, billing_address, shipping_name, shipping_email, shipping_phone, shipping_address, payment_method, subtotal, shipping_cost, total, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 'pending', NOW())");
    
    $user_id = $_SESSION['user_id'] ?? 1;
    $total = $subtotal;
    
    $stmt->bind_param("sisssssssssdd", 
        $order_code,
        $user_id, 
        $fullname, $email, $phone, $address,
        $fullname, $email, $phone, $address,
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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