<?php
session_start();

$pageTitle = "Ecobazar - Shop";
// Khởi tạo biến $category_id ngay từ đầu
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

include('includes/head.php');
?>

<!-- Thêm CSS cho thông báo -->
<style>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    background-color: #4CAF50;
    color: white;
    border-radius: 4px;
    display: none;
    z-index: 1000;
    animation: slideIn 0.5s ease-out;
    transition: opacity 0.5s;
}

.notification.error {
    background-color: #f44336;
}

@keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

.cart-animation {
    animation: cartBounce 0.5s ease-out;
}

@keyframes cartBounce {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    25% { transform: scale(1.2); }
    50% { transform: scale(1); }
    75% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.heart-icon i {
    transition: color 0.3s;
}

.heart-icon i:hover {
    color: #FF8A00;
}
</style>

<div class = "container">
     <!-- thanh điều hướng -->
     <div class="breadcrumb1-container1">
    <div class="breadcrumb1">
        <a href="homepage.php"><i class="fa-solid fa-house"></i></a>
        <span>›</span>
        <?php if ($category_id > 0): ?>
            <a href="08shop.php">Shop</a>
            <span>›</span>
            <?php
            $category_name = ($category_id == 1) ? 'Vegetables' : 'Fruits';
            echo '<a href="08shop.php?category_id=' . $category_id . '" class="active">' . $category_name . '</a>';
        else: ?>
            <a href="08shop.php" class="active">Shop</a>
        <?php endif; ?>
    </div>
</div>
<!-- Banner1 -->
<div class="banner">
    <div class="banner-content">
        <span>BEST DEALS</span>
        <h2>Sale of the Month</h2>
        <p id="countdown">00 : 00 : 00 : 00</p>
        <button>Shop Now <i class="fa-solid fa-arrow-right"></i></button>
    </div>
    <div class="discount-badge">56% OFF</div>
</div>

<!-- bộ lọc sản phẩm -->
<div class="filter-container">
    <div class="filter-group">
        <select id="categoryFilter" onchange="filterProducts()">
            <option value="">Select Category</option>
            <option value="1">Vegetables</option>
            <option value="2">Fruits</option>
            <!-- <option>Dairy</option> -->
        </select>
        <select id="priceFilter" onchange="filterProducts()">
            <option value="">Select Price</option>
            <option value="asc">Low to High</option>
            <option value="desc">High to Low</option>
        </select>
    </div>
    <!-- <div class="sort-group">
        <select>
            <option>Sort by: Latest</option>
            <option>Oldest</option>
            <option>Popular</option>
        </select>
    </div> -->
</div>
<!-- Bộ lọc đang áp dụng -->
<div class="active-filters">
        <span>Active Filters: <strong>Wing Chair</strong> × Min $300 – Max $500 ×</span>
        <span class="result-count"><strong>10</strong> Results found.</span>
    </div>

<!-- Danh sách sản phẩm -->
<div class="product-grid" id="products">
    <?php
    // Số sản phẩm trên mỗi trang
    $items_per_page = 16;
    
    // Lấy các tham số từ URL
    $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
    $price_sort = isset($_GET['price']) ? $_GET['price'] : '';
    $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Tính offset cho LIMIT trong SQL
    $offset = ($current_page - 1) * $items_per_page;
    
    // Xây dựng câu truy vấn SQL cơ bản
    $sql = "SELECT * FROM products";
    $where_conditions = array();
    $params = array();
    $types = "";

    // Thêm điều kiện tìm kiếm
    if (!empty($search_term)) {
        $where_conditions[] = "name LIKE ?";
        $params[] = "%$search_term%";
        $types .= "s";
    }

    // Thêm điều kiện lọc theo danh mục
    if ($category_id > 0) {
        $where_conditions[] = "category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }

    // Kết hợp các điều kiện WHERE
    if (!empty($where_conditions)) {
        $sql .= " WHERE " . implode(" AND ", $where_conditions);
    }

    // Thêm sắp xếp theo giá
    if ($price_sort == 'asc' || $price_sort == 'desc') {
        $sql .= " ORDER BY price " . strtoupper($price_sort);
    }

    // Thêm LIMIT và OFFSET vào câu truy vấn
    $sql .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $items_per_page;
    $types .= "ii";

    // Chuẩn bị và thực thi truy vấn
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Lấy tổng số sản phẩm để tính số trang
    $count_sql = "SELECT COUNT(*) as total FROM products";
    if (!empty($where_conditions)) {
        $count_sql .= " WHERE " . implode(" AND ", $where_conditions);
    }
    $count_stmt = $conn->prepare($count_sql);
    if (!empty($params)) {
        // Bỏ 2 tham số cuối (LIMIT và OFFSET) khi đếm tổng số sản phẩm
        array_pop($params);
        array_pop($params);
        if (!empty($params)) {
            $count_stmt->bind_param(substr($types, 0, -2), ...$params);
        }
    }
    $count_stmt->execute();
    $total_result = $count_stmt->get_result()->fetch_assoc();
    $total_items = $total_result['total'];
    $total_pages = ceil($total_items / $items_per_page);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Kiểm tra trạng thái tồn kho và giảm giá
            $stock_class = $row['stock'] == 0 ? 'out-of-stock' : '';
            $sale_class = !empty($row['old_price']) && $row['old_price'] > $row['price'] ? 'sale' : '';
            ?>
            <!-- Sản phẩm -->
            <div class="product <?php echo $stock_class; ?> <?php echo $sale_class; ?>">
                <a href="10prd_details.php?id=<?= $row['id']; ?>">
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
                        <i class="fa-regular fa-star"></i>
                    </p>
                    
                    </a>
                    <div class="product-icon">
                        <div class="product-icon-item heart-icon" onclick="addToWishlist(event, <?php echo $row['id']; ?>)"><i class="fa-solid fa-heart"></i></div>
                        <a href="10prd_details.php?id=<?= $row['id']; ?>" class="product-icon-item eye-icon" title="Xem chi tiết">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>

                <?php if ($row['stock'] > 0): ?>
                    <div class="product-cart-icon" onclick="addToCart(<?php echo $row['id']; ?>)"><i class="fa-solid fa-cart-shopping"></i></div>
                <?php endif; ?>
                
                <script>
                function addToWishlist(event, productId) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    // Thêm hiệu ứng cho icon trái tim
                    const heartIcon = event.currentTarget;
                    heartIcon.style.animation = 'heartBeat 0.5s';
                    
                    // Gửi request để thêm sản phẩm vào wishlist
                    fetch('add_to_wishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'product_id=' + productId
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hiển thị thông báo
                        const notification = document.createElement('div');
                        notification.className = 'notification ' + (data.success ? 'success' : 'error');
                        notification.textContent = data.message;
                        document.body.appendChild(notification);
                        
                        // Hiển thị và ẩn thông báo
                        notification.style.display = 'block';
                        setTimeout(() => {
                            notification.style.opacity = '0';
                            setTimeout(() => {
                                notification.remove();
                            }, 500);
                        }, 2000);

                        // Nếu thêm thành công, đổi màu icon trái tim
                        if (data.success) {
                            heartIcon.querySelector('i').style.color = '#FF8A00';
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                        // Hiển thị thông báo lỗi
                        const notification = document.createElement('div');
                        notification.className = 'notification error';
                        notification.textContent = 'Có lỗi xảy ra khi thêm sản phẩm vào danh sách yêu thích';
                        document.body.appendChild(notification);
                        
                        notification.style.display = 'block';
                        setTimeout(() => {
                            notification.style.opacity = '0';
                            setTimeout(() => {
                                notification.remove();
                            }, 500);
                        }, 2000);
                    })
                    .finally(() => {
                        setTimeout(() => {
                            heartIcon.style.animation = '';
                        }, 500);
                    });
                }
                </script>
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
    <?php if ($current_page > 1): ?>
        <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : ''; ?><?php echo !empty($_GET['price']) ? '&price=' . $_GET['price'] : ''; ?>" class="page-item"><i class="fa-solid fa-angle-left"></i></a>
    <?php else: ?>
        <span class="page-item disabled"><i class="fa-solid fa-angle-left"></i></span>
    <?php endif; ?>

    <?php
    // Hiển thị các số trang
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            echo "<span class=\"page-item active\">$i</span>";
        } else {
            $params = $_GET;
            $params['page'] = $i;
            $query_string = http_build_query($params);
            echo "<a href=\"?$query_string\" class=\"page-item\">$i</a>";
        }
    }
    ?>

    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : ''; ?><?php echo !empty($_GET['price']) ? '&price=' . $_GET['price'] : ''; ?>" class="page-item"><i class="fa-solid fa-angle-right"></i></a>
    <?php else: ?>
        <span class="page-item disabled"><i class="fa-solid fa-angle-right"></i></span>
    <?php endif; ?>
</div>
</div>

<script>
function filterProducts() {
    const categoryId = document.getElementById('categoryFilter').value;
    const priceSort = document.getElementById('priceFilter').value;

    let url = '08shop.php?';
    let params = [];

    if (categoryId) params.push('category_id=' + categoryId);
    if (priceSort) params.push('price=' + priceSort);

    window.location.href = url + params.join('&');
}

// Đặt giá trị các bộ lọc được chọn
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    const categoryId = urlParams.get('category_id');
    if (categoryId) {
        document.getElementById('categoryFilter').value = categoryId;
    }
    
    const priceSort = urlParams.get('price');
    if (priceSort) {
        document.getElementById('priceFilter').value = priceSort;
    }
    
}
</script>
<script>
    // Function to update the countdown timer
    function updateCountdown() {
        // Set the end date for the countdown (e.g., end of the current month)
        const now = new Date();
        const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
        // Alternatively, set a specific end date:
        // const endOfMonth = new Date('2025-05-31 23:59:59');

        // Calculate the time difference
        const timeDiff = endOfMonth - now;

        if (timeDiff <= 0) {
            document.getElementById('countdown').textContent = '00 : 00 : 00 : 00';
            clearInterval(countdownInterval);
            return;
        }

        // Calculate days, hours, minutes, seconds
        const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

        // Format the time with leading zeros
        const formattedTime = `${days.toString().padStart(2, '0')} : ${hours.toString().padStart(2, '0')} : ${minutes.toString().padStart(2, '0')} : ${seconds.toString().padStart(2, '0')}`;

        // Update the countdown display
        document.getElementById('countdown').textContent = formattedTime;
    }

    // Update the countdown immediately and then every second
    updateCountdown();
    const countdownInterval = setInterval(updateCountdown, 1000);
</script>
<!--danh sách trang sản phẩm-->
<!-- <div class="pagination">
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
</div> -->

<?php
include('components/footer.php'); // Nó ở đây
?>

<style>
.out-of-stock .product-cart-icon {
    display: none !important;
}

.out-of-stock img {
    opacity: 0.6;
}

.out-of-stock .stock-label {
    background-color: #ff0000;
    color: white;
    padding: 5px 10px;
    position: absolute;
    top: 10px;
    left: 10px;
    border-radius: 4px;
    z-index: 1;
}
</style>
