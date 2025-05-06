<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}

$products_per_page = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

$sql = "SELECT * FROM products ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $products_per_page);
$stmt->execute();
$result = $stmt->get_result();

while ($product = $result->fetch_assoc()): ?>
    <li>
        <img src="./img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <p><?php echo htmlspecialchars($product['name']); ?></p>
        
        <?php if (isset($product['old_price']) && $product['old_price'] > $product['price']): 
            $discount_percent = round(100 - ($product['price'] / $product['old_price'] * 100));
        ?>
            <span class="sale-badge">Sale <?php echo $discount_percent; ?>%</span>
            <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
            <span class="old-price">$<?php echo number_format($product['old_price'], 2); ?></span><br>
        <?php else: ?>
            <span class="price">$<?php echo number_format($product['price'], 2); ?></span><br>
        <?php endif; ?>
        
        <span class="rating"> ★★★★☆</span>
        
        <?php if ($product['stock'] > 0): ?>
            <a href="add-to-cart.php?id=<?php echo $product['id']; ?>" class="add-to-cart-btn">Add to Cart</a>
        <?php else: ?>
            <span class="out-of-stock">Out of Stock</span>
        <?php endif; ?>
    </li>
<?php endwhile; ?>