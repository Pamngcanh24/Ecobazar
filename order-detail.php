<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kết nối database
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die('Kết nối database thất bại: ' . $conn->connect_error);
}

// Lấy và validate ID đơn hàng
$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($order_id === false || $order_id <= 0) {
    include 'head.php';
    echo '<div class="error-container">';
    echo '<h3>Lỗi: ID đơn hàng không hợp lệ</h3>';
    echo '<p>Vui lòng kiểm tra lại liên kết hoặc thử các cách sau:</p>';
    echo '<ul>';
    echo '<li><a href="order-history.php" class="btn">Truy cập lịch sử đơn hàng</a></li>';
    echo '<li><a href="contact.php" class="btn">Liên hệ hỗ trợ</a></li>';
    echo '</ul>';
    echo '</div>';
    include 'footer.php';
    exit();
}

// Lấy thông tin đơn hàng
$order_stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$order_stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();

if (!$order) {
    include 'head.php';
    echo '<div class="error-container">';
    echo '<h3>Không tìm thấy đơn hàng</h3>';
    echo '<p>Đơn hàng #'.$order_id.' không tồn tại hoặc không thuộc về tài khoản của bạn</p>';
    echo '<div class="action-buttons">';
    echo '<a href="order-history.php" class="btn btn-primary">Quay lại lịch sử đơn hàng</a>';
    echo '<a href="contact.php" class="btn btn-secondary">Liên hệ hỗ trợ</a>';
    echo '</div>';
    echo '</div>';
    include 'footer.php';
    exit();
}

// Lấy sản phẩm trong đơn hàng
$items_stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image, p.description 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items_stmt->bind_param('i', $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = "Order Details";
include './includes/head.php';
?>

<link rel="stylesheet" href="style.css">

<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="dashboard.php" title="Home"><i class="fas fa-home"></i></a>
        <span> &gt; </span>
        <a href="dashboard.php">Account</a>
        <span> &gt; </span>
        <a href="order-history.php">Order History</a>
        <span> &gt; </span>
        <a href="order_detail.php?id=<?= $order_id ?>" class="active">Order Detail</a>
    </div>
</div>


<?php include './includes/dash.php'; ?>

<!-- Main Content -->
<div class="main-content">
        <div class="order-header">
            <h2>Order Details - <?= date('F j, Y', strtotime($order['created_at'])) ?> - <?= count($items) ?> Products</h2>
            <a href="order-history.php" class="back-link">Back to List</a>
        </div>

        <div class="order-info-grid">
            <div class="address-column">
                <div class="address-box">
                    <h3>BILLING ADDRESS</h3>
                    <p><strong><?= htmlspecialchars($order['billing_name']) ?></strong></p>
                    <p><?= htmlspecialchars($order['billing_address']) ?></p>
                    <p><?= htmlspecialchars($order['billing_email']) ?></p>
                    <p><?= htmlspecialchars($order['billing_phone']) ?></p>
                </div>
                
                <div class="address-box">
                    <h3>SHIPPING ADDRESS</h3>
                    <p><strong><?= htmlspecialchars($order['shipping_name']) ?></strong></p>
                    <p><?= htmlspecialchars($order['shipping_address']) ?></p>
                    <p><?= htmlspecialchars($order['shipping_email']) ?></p>
                    <p><?= htmlspecialchars($order['shipping_phone']) ?></p>
                </div>
            </div>
            
            <div class="summary-column">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>ORDER ID: #<?= $order['id'] ?></span>
                    </div>
                    <div class="summary-row">
                        <span>PAYMENT METHOD: <?= $order['payment_method'] ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($order['subtotal'], 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Discount:</span>
                        <span>-$<?= number_format($order['subtotal'] * $order['discount']/100, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span><?= $order['shipping_cost'] == 0 ? 'Free' : '$'.number_format($order['shipping_cost'], 2) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?= number_format($order['total'], 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-products-section">
            <table class="order-products-table">
    <thead>
        <tr>
            <th>PRODUCT</th>
            <th>PRICE</th>
            <th>QUANTITY</th>
            <th>SUBTOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <div class="product-info">
                    <?php if (!empty($item['image'])): ?>
                        <img src="assetsHG/images/shop/<?= htmlspecialchars(basename($item['image'])) ?>" 
                            alt="<?= htmlspecialchars($item['name']) ?>"
                        class="product-image">
                    <?php endif; ?>
                    <div class="product-details">
                        <div class="product-name"><?= htmlspecialchars($item['name']) ?></div>
                        <?php if (!empty($item['description'])): ?>
                            <div class="product-desc"><?= substr(htmlspecialchars($item['description']), 0, 50) ?>...</div>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td>$<?= number_format($item['price'], 2) ?></td>
            <td>x<?= $item['quantity'] ?></td>
            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
        </div>
    </div>
</div>

<?php include './includes/footer.php'; ?>
