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

<link rel="stylesheet" href="style.css">


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
<div class="wishlist-container">
    <h2 class="wishlist-title">My Wishlist</h2>
    <table class="wishlist-table">
        <thead>
            <tr>
                <th>PRODUCT</th>
                <th>PRICE</th>
                <th>STOCK STATUS</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($wishlist_items as $item): ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="assetsHG/images/shop/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <span><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                    </td>
                    <td>
                        <?php if ($item['old_price'] > $item['price']): ?>
                            <strong>$<?= number_format($item['price'], 2) ?></strong>
                            <del>$<?= number_format($item['old_price'], 2) ?></del>
                        <?php else: ?>
                            <strong>$<?= number_format($item['price'], 2) ?></strong>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($item['stock'] > 0): ?>
                            <span class="stock-status in-stock">In Stock</span>
                        <?php else: ?>
                            <span class="stock-status out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($item['stock'] > 0): ?>
                            <button class="btn btn-add-cart" onclick="addToCart(<?php echo $item['id']; ?>)">Add to Cart</button>
                        <?php else: ?>
                            <button class="btn btn-disabled" disabled>Add to Cart</button>
                        <?php endif; ?>
                        <a href="remove_wishlist.php?id=<?= $item['wishlist_id'] ?>" class="btn-remove">✕</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
</div>

<?php include './includes/footer.php'; ?>

<script>
function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sản phẩm đã được thêm vào giỏ hàng');
            // Reload trang để cập nhật số lượng giỏ hàng
            window.location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
    });
}
</script>