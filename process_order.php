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

// Hàm tách quận từ địa chỉ
function extractDistrictFromAddress($address) {
    // Danh sách các quận trong Hà Nội
    $hanoiDistricts = [
        'Ba Đình', 'Hoàn Kiếm', 'Tây Hồ', 'Long Biên', 'Cầu Giấy',
        'Đống Đa', 'Hai Bà Trưng', 'Hoàng Mai', 'Thanh Xuân', 'Nam Từ Liêm',
        'Bắc Từ Liêm', 'Hà Đông', 'Thanh Trì', 'Gia Lâm', 'Đông Anh',
        'Sóc Sơn', 'Mê Linh', 'Đan Phượng', 'Hoài Đức', 'Quốc Oai',
        'Thạch Thất', 'Chương Mỹ', 'Thanh Oai', 'Thường Tín', 'Phú Xuyên',
        'Ứng Hòa', 'Mỹ Đức', 'Ba Vì', 'Sơn Tây', 'Phúc Thọ',
        'Thạch Thất', 'Mê Linh', 'Đông Anh', 'Gia Lâm'
    ];
    
    // Chuẩn hóa địa chỉ
    $address = mb_strtolower($address, 'UTF-8');
    
    foreach ($hanoiDistricts as $district) {
        $districtLower = mb_strtolower($district, 'UTF-8');
        if (strpos($address, $districtLower) !== false) {
            return $district;
        }
    }
    
    // Nếu không tìm thấy quận cụ thể, thử tìm theo pattern "quận + tên"
    if (preg_match('/quận\s+([\p{L}\s]+)/u', $address, $matches)) {
        return ucwords(trim($matches[1]));
    }
    
    // Nếu vẫn không tìm thấy, trả về null
    return null;
}

// Hàm tự động phân công tài xế theo quận
function assignDriverByDistrict($district) {
    global $conn;
    
    if (!$district) {
        return null; // Không thể phân công nếu không xác định được quận
    }
    
    // Tìm tài xế trong cùng quận với số đơn hàng hiện tại ít nhất
    $stmt = $conn->prepare("
        SELECT id, name, phone, current_orders 
        FROM drivers 
        WHERE LOWER(address) LIKE LOWER(CONCAT('%', ?, '%')) 
        AND current_orders < 10  -- Giới hạn số đơn tối đa mỗi tài xế
        ORDER BY current_orders ASC, RAND()  -- Ưu tiên tài xế có ít đơn, random nếu bằng nhau
        LIMIT 1
    ");
    
    $stmt->bind_param("s", $district);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($driver = $result->fetch_assoc()) {
        // Tăng số đơn của tài xế này
        $updateStmt = $conn->prepare("UPDATE drivers SET current_orders = current_orders + 1 WHERE id = ?");
        $updateStmt->bind_param("s", $driver['id']);
        $updateStmt->execute();
        
        return $driver['id'];
    }
    
    // Nếu không tìm thấy tài xế trong quận, tìm tài xế có ít đơn nhất
    $fallbackStmt = $conn->prepare("
        SELECT id, name, phone, current_orders 
        FROM drivers 
        WHERE current_orders < 10
        ORDER BY current_orders ASC, RAND()
        LIMIT 1
    ");
    $fallbackStmt->execute();
    $fallbackResult = $fallbackStmt->get_result();
    
    if ($fallbackDriver = $fallbackResult->fetch_assoc()) {
        // Tăng số đơn của tài xế này
        $updateStmt = $conn->prepare("UPDATE drivers SET current_orders = current_orders + 1 WHERE id = ?");
        $updateStmt->bind_param("s", $fallbackDriver['id']);
        $updateStmt->execute();
        
        return $fallbackDriver['id'];
    }
    
    return null; // Không tìm thấy tài xế phù hợp
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
    
    // Tách quận từ địa chỉ và phân công tài xế
    $district = extractDistrictFromAddress($address);
    $driver_id = assignDriverByDistrict($district);
    
    // Thêm đơn hàng vào bảng orders (bao gồm driver_id)
    $stmt = $conn->prepare("INSERT INTO orders (order_code, user_id, billing_name, billing_email, billing_phone, billing_address, shipping_name, shipping_email, shipping_phone, shipping_address, payment_method, subtotal, shipping_cost, total, status, driver_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 'pending', ?, NOW())");
    
    $user_id = $_SESSION['user_id'] ?? 1;
    $total = $subtotal;
    
    $stmt->bind_param("sisssssssssdds", 
        $order_code,
        $user_id, 
        $fullname, $email, $phone, $address,
        $fullname, $email, $phone, $address,
        $payment_method,
        $subtotal,
        $total,
        $driver_id
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
    
    // Lấy thông tin tài xế được phân công để hiển thị
    $assignedDriver = null;
    if ($driver_id) {
        $driverStmt = $conn->prepare("SELECT name, phone FROM drivers WHERE id = ?");
        $driverStmt->bind_param("s", $driver_id);
        $driverStmt->execute();
        $driverResult = $driverStmt->get_result();
        $assignedDriver = $driverResult->fetch_assoc();
    }
    
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
            <?php if ($assignedDriver): ?>
            <div class="driver-info" style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <h3 style="color: #333; margin-bottom: 10px;">Thông tin tài xế</h3>
                <p style="margin: 5px 0;"><strong>Tên:</strong> <?php echo htmlspecialchars($assignedDriver['name']); ?></p>
                <p style="margin: 5px 0;"><strong>SĐT:</strong> <?php echo htmlspecialchars($assignedDriver['phone']); ?></p>
                <p style="margin: 5px 0; color: #666; font-size: 14px;">Tài xế sẽ liên hệ với bạn để giao hàng</p>
            </div>
            <?php else: ?>
            <div class="driver-info" style="margin: 20px 0; padding: 15px; background: #fff3cd; border-radius: 8px; color: #856404;">
                <p style="margin: 0;">Đang tìm kiếm tài xế phù hợp cho đơn hàng của bạn</p>
            </div>
            <?php endif; ?>
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