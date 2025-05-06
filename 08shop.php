<?php
// Kết nối cơ sở dữ liệu
$host = "localhost"; // Máy chủ cơ sở dữ liệu
$username = "root"; // Tên người dùng cơ sở dữ liệu
$password = ""; // Mật khẩu cơ sở dữ liệu
$dbname = "ecobazar"; // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

include('components/head.php');
include('includes/head.php');
?>

<div class = "container">
     <!-- thanh điều hướng -->
     <div class="breadcrumb1-container1">
    <div class="breadcrumb1">
        <a href="#"><i class="fa-solid fa-house"></i></a>  
        <span>›</span>
        <a href="#">Categories</a>
        <span>›</span>
        <a href="#" class="active">Vegetables</a>
    </div>
    </div>

 <!-- Banner1 -->
 <div class="banner">
    <div class="banner-content">
        <span>BEST DEALS</span>
        <h2>Sale of the Month</h2>
        <p>00 : 02 : 18 : 46</p>
        <button>Shop Now <i class="fa-solid fa-arrow-right"></i></button>
    </div>
    <div class="discount-badge">56% OFF</div>
    </div>

<!-- bộ lọc sản phẩm -->
<div class="filter-container">
    <div class="filter-group">
        <select>
            <option>Select Category</option>
            <option>Vegetables</option>
            <option>Fruits</option>
            <option>Dairy</option>
        </select>
        <select>
            <option>Select Price</option>
            <option>Low to High</option>
            <option>High to Low</option>
        </select>
        <select>
            <option>Select Rating</option>
            <option>5 Stars</option>
            <option>4 Stars</option>
            <option>3 Stars</option>
        </select>
    </div>
    <div class="sort-group">
        <select>
            <option>Sort by: Latest</option>
            <option>Oldest</option>
            <option>Popular</option>
        </select>
        <select>
            <option>Show: 16</option>
            <option>32</option>
            <option>48</option>
        </select>
    </div>
</div>
<!-- Bộ lọc đang áp dụng -->
<div class="active-filters">
        <span>Active Filters: <strong>Wing Chair</strong> × Min $300 – Max $500 ×</span>
        <span class="result-count"><strong>2,547</strong> Results found.</span>
    </div>

<!-- Danh sách sản phẩm -->
<div class="product-grid">
    <?php
    // Lấy dữ liệu sản phẩm từ cơ sở dữ liệu
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Kiểm tra trạng thái tồn kho và giảm giá
            $stock_class = $row['stock'] == 0 ? 'out-of-stock' : '';
            $sale_class = !empty($row['old_price']) && $row['old_price'] > $row['price'] ? 'sale' : '';
            ?>
            <!-- Sản phẩm -->
            <div class="product <?php echo $stock_class; ?> <?php echo $sale_class; ?>">
                <?php if ($row['stock'] == 0) { ?>
                    <p class="stock-label">Out of Stock</p>
                <?php } elseif ($sale_class) { ?>
                    <p class="sale-label">Sale 50%</p>
                <?php } ?>
                <img src="assetsHG/images/shop/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                <h3><?php echo $row['name']; ?></h3>
                <p class="price">$<?php echo number_format($row['price'], 2); ?>
                    <?php if (!empty($row['old_price']) && $row['old_price'] > $row['price']) { ?>
                        <span class="old-price">$<?php echo number_format($row['old_price'], 2); ?></span>
                    <?php } ?>
                </p>
                <p class="rating">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-regular fa-star"></i> <!-- Ngôi sao rỗng (chưa full 5 sao) -->
                </p>
                <div class="product-icon">
                    <div class="product-icon-item heart-icon"><i class="fa-solid fa-heart"></i></div>
                    <div class="product-icon-item eye-icon"><i class="fa-solid fa-eye"></i></div>
                </div>
                <div class="cart-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            </div>
            <?php
        }
    } else {
        echo "<p>Không tìm thấy sản phẩm.</p>";
    }
    ?>
</div>

<!--danh sách trang sản phẩm-->
<div class="pagination">
    <span class="page-item disabled"><i class="fa-solid fa-angle-left"></i></span>
    <a href="#" class="page-item active">1</a>
    <a href="#" class="page-item">2</a>
    <a href="#" class="page-item">3</a>
    <a href="#" class="page-item">4</a>
    <a href="#" class="page-item">5</a>
    <span class="page-item">...</span>
    <a href="#" class="page-item">21</a>
    <a href="#" class="page-item"><i class="fa-solid fa-angle-right"></i></a>
</div>
</div>

<?php
// Đóng kết nối cơ sở dữ liệu
$conn->close();
include('components/footer.php');
?>
</body>
</html>