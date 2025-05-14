<?php
session_start();
$pageTitle = "Checkout";
include './includes/head.php';
?>

<link rel="stylesheet" href="assetsHG/style.css">
<link rel="stylesheet" href="assetsHG/css/checkout.css">


<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="dashboard.php" title="Home"><i class="fas fa-home"></i></a>
        <span> &gt; </span>
        <a href="15shopping.php" class="active">Checkout</a>
    </div>
</div>

<!-- Main Content -->
<div class="wrapper">
    <?php
    // Kiểm tra session giỏ hàng
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo '<div class="empty-cart text-center">';
        echo '<h3>Giỏ hàng trống</h3>';
        echo '<a href="index.php" class="btn checkout-btn">Thêm sản phẩm vào giỏ hàng</a>';
        echo '</div>';
    } else {
        // Tính tổng tiền từ giỏ hàng
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
    ?>
    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Thông tin giao hàng</h2>
            <form action="process_order.php" method="post">
                <div class="form-group">
                    <label for="fullname">Họ và tên</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ giao hàng</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label>Phương thức thanh toán</label>
                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <span>Thanh toán khi nhận hàng (COD)</span>
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="bank">
                            <span>Chuyển khoản ngân hàng</span>
                        </label>
                    </div>
                </div>
                <div id="bank-info" style="display: none; margin: 15px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                    <h4 style="margin-bottom: 10px; color: #333;">Thông tin chuyển khoản</h4>
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 5px 0;">Ngân hàng:</td>
                            <td style="font-weight: bold;">BIDV</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0;">Số tài khoản:</td>
                            <td style="font-weight: bold;">1123112311</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0;">Tên chủ tài khoản:</td>
                            <td style="font-weight: bold;">ECOBAZAR SHOP</td>
                        </tr>
                    </table>
                </div>
                <button type="submit" class="btn checkout-btn">Đặt hàng</button>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const bankInfo = document.getElementById('bank-info');
                    const paymentMethods = document.getElementsByName('payment_method');
                    
                    paymentMethods.forEach(method => {
                        method.addEventListener('change', function() {
                            bankInfo.style.display = this.value === 'bank' ? 'block' : 'none';
                        });
                    });
                });
                </script>
            </form>
        </div>

        <div class="order-summary">
            <h2>Đơn hàng của bạn</h2>
            <div class="order-items">
                <?php
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    $stmt = $conn->prepare("SELECT name, price, image FROM products WHERE id = ?");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $total = $row['price'] * $quantity;
                ?>
                <div class="order-item">
                    <img src="./assetsHG/images/shop/<?= htmlspecialchars($row['image']) ?>" alt="<?= $row['name'] ?>">
                    <div class="item-details">
                        <h4><?= $row['name'] ?></h4>
                        <p>Số lượng: <?= $quantity ?></p>
                        <p class="item-price">$<?= number_format($total, 2) ?></p>
                    </div>
                </div>
                <?php
                    }
                }
                ?>
            </div>
            <div class="order-total">
                <div class="total-row">
                    <span>Tạm tính:</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="total-row">
                    <span>Phí vận chuyển:</span>
                    <span>Miễn phí</span>
                </div>
                <div class="total-row grand-total">
                    <span>Tổng cộng:</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<?php include './includes/footer.php'; ?>