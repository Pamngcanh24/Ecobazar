<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kết nối database
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh sách sản phẩm yêu thích của user
$stmt = $conn->prepare("
    SELECT w.id AS wishlist_id, p.* 
    FROM wishlist w 
    JOIN products p ON w.product_id = p.id 
    WHERE w.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$wishlist_items = $result->fetch_all(MYSQLI_ASSOC);

$pageTitle = "My Wishlist";
include './includes/head.php';
?>

<link rel="stylesheet" href="assetsHG/style.css">


<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="dashboard.php" title="Home"><i class="fas fa-home"></i></a>
        <span> &gt; </span>
        <a href="dashboard.php">Account</a>
        <span> &gt; </span>
        <a href="wishlist.php" class="active">Wishlist</a>
    </div>
</div>

<?php include './includes/dash.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="cart-container">
        <h2 class="cart-title">My Shopping Cart</h2>

        <form action="update_cart.php" method="post">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>PRODUCT</th>
                        <th>PRICE</th>
                        <th>QUANTITY</th>
                        <th>SUBTOTAL</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $subtotal = 0; ?>
                    <?php foreach ($cart_items as $item): ?>
                        <?php $item_total = $item['price'] * $item['quantity']; ?>
                        <?php $subtotal += $item_total; ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <img src="/Ecobazar/assets/image/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <span><?= htmlspecialchars($item['name']) ?></span>
                                </div>
                            </td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <div class="quantity-15">
                                    <button type="submit" name="decrease" value="<?= $item['id'] ?>">−</button>
                                    <input type="text" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" readonly>
                                    <button type="submit" name="increase" value="<?= $item['id'] ?>">+</button>
                                </div>
                            </td>

                            <td>$<?= number_format($item_total, 2) ?></td>
                            <td>
                                <a href="remove_cart.php?id=<?= $item['id'] ?>" class="btn-remove">✕</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-buttons">
                <a href="shop.php" class="btn btn-light">Return to shop</a>
                <button type="submit" class="btn btn-dark">Update Cart</button>
            </div>
        </form>

        <!-- Cart Summary -->
        <div class="cart-summary">
            <h3>Cart Total</h3>
            <ul>
                <li><span>Subtotal:</span><span>$<?= number_format($subtotal, 2) ?></span></li>
                <li><span>Shipping:</span><span>Free</span></li>
                <li><strong>Total:</strong><strong>$<?= number_format($subtotal, 2) ?></strong></li>
            </ul>
            <a href="checkout.php" class="btn btn-green">Proceed to checkout</a>
        </div>

        <!-- Coupon Code -->
        <div class="coupon-container">
            <form action="apply_coupon.php" method="post">
                <input type="text" name="coupon_code" placeholder="Enter code">
                <button type="submit" class="btn btn-dark">Apply Coupon</button>
            </form>
        </div>
    </div>

</div>
</div>

<?php include './includes/footer.php'; ?>