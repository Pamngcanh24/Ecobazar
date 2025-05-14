<?php
session_start();
// Kiểm tra đăng nhập
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }
$pageTitle = "Shopping Cart";
include './includes/head.php';

// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Lấy thông tin sản phẩm trong giỏ hàng
$cart_items = array();
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    $stmt = $conn->prepare("SELECT *, stock FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($product = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $product['quantity'] = $quantity;
        $product['total'] = $quantity * $product['price'];
        $cart_items[] = $product;
        $subtotal += $product['total'];
    }
}
?>

<link rel="stylesheet" href="assetsHG/style.css">


<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="dashboard.php" title="Home"><i class="fas fa-home"></i></a>
        <span> &gt; </span>
        <a href="15shopping.php" class="active">Shopping Cart</a>
    </div>
</div>

<?php # include './includes/dash.php'; ?>

<!-- Main Content -->
<div class="wrapper">
    <div class="cart-container">
        <h2 class="cart-title text-center">Shopping Cart</h2>      
        <?php if (!empty($cart_items)): ?>
            <form action="update_cart.php" method="post">
                <div class="cart-box">
                    <div class="cart-left">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <img src="./assetsHG/images/shop/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                                                <span class="product-name"><?= htmlspecialchars($item['name']) ?></span>
                                            </div>
                                        </td>
                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <input type="number" 
                                                   name="quantity[<?= $item['id'] ?>]" 
                                                   value="<?= $item['quantity'] ?>" 
                                                   min="1" 
                                                   max="<?= $item['stock'] ?>" 
                                                   class="quantity-input"
                                                   data-stock="<?= $item['stock'] ?>"
                                                   oninput="validateQuantity(this)">
                                            <div class="stock-warning" style="display:none; color: red; font-size: 12px;">Vượt quá số lượng tồn kho!</div>
                                        </td>
                                        <td>$<?= number_format($item['total'], 2) ?></td>
                                        <td>
                                            <button type="submit" name="quantity[<?= $item['id'] ?>]" value="0" class="remove-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <a href="" class="btn btn-dark">Return to shop</a>
                                    </td>
                                    <td colspan="4" class="text-right">
                                        <button type="submit" class="btn btn-dark">Update Cart</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="cart-right">
                        <h3>Cart Total</h3>
                        <div class="cart-summary15">
                            <div class="cart-summary15-item"><span>Subtotal:</span><b> $<?= number_format($subtotal, 2) ?></b></div>
                            <div class="cart-summary15-item"><span>Shipping:</span><b> Free</b></div>
                            <div class="cart-summary15-item"><span>Total:</span><b> $<?= number_format($subtotal, 2) ?></b></div>
                            <a href="checkout.php" class="btn checkout-btn full-width text-center">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="empty-cart">
                <h3>Giỏ hàng trống</h3>
                <a href="08shop.php" class="checkout-btn">Thêm sản phẩm vào giỏ hàng</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.cart-container {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.cart-title {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

.cart-table {
    width: 100%;
    border-collapse: collapse;
}

.cart-table th,
.cart-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.cart-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
}

.product-name {
    font-weight: 500;
    color: #333;
}

.quantity-input {
    width: 60px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.remove-btn {
    color: #dc3545;
    background: none;
    border: none;
    cursor: pointer;
}

.cart-summary15 {
    margin-top: 20px;
}
.cart-summary15-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}


.checkout-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.checkout-btn:hover {
    background-color: #218838;
}

.empty-cart {
    text-align: center;
    padding: 40px;
    color: #666;
}
.cart-box {
    display: flex;
    margin-left: -15px;
    margin-right: -15px;
}
.cart-left {
    padding: 0 15px;
    flex-basis: 70%;
}
.cart-right {
    padding: 0 15px;
    flex-basis: 30%;
}
</style>

<?php include './includes/footer.php'; ?>

<script>
function validateQuantity(input) {
    const stock = parseInt(input.dataset.stock);
    const quantity = parseInt(input.value);
    const warning = input.parentElement.querySelector('.stock-warning');
    
    if (quantity > stock) {
        input.value = stock;
        warning.style.display = 'block';
        setTimeout(() => {
            warning.style.display = 'none';
        }, 3000);
    } else {
        warning.style.display = 'none';
    }
}

// Thêm kiểm tra khi form được submit
document.querySelector('form').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('.quantity-input');
    let isValid = true;
    
    inputs.forEach(input => {
        const stock = parseInt(input.dataset.stock);
        const quantity = parseInt(input.value);
        if (quantity > stock) {
            isValid = false;
            input.value = stock;
            input.parentElement.querySelector('.stock-warning').style.display = 'block';
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Một số sản phẩm vượt quá số lượng tồn kho. Số lượng đã được điều chỉnh.');
    }
});
</script>