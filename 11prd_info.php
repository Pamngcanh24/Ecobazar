<?php
session_start();
$pageTitle = "Product Details";
include './includes/head.php';

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn thông tin sản phẩm và ảnh thumbnail
$sql = "SELECT p.*, c.name as category_name, GROUP_CONCAT(t.thumbnail_image) as thumbnails 
        FROM products p 
        LEFT JOIN thumbnails t ON p.id = t.product_id 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ? 
        GROUP BY p.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    // Chuyển hướng nếu không tìm thấy sản phẩm
    header("Location: 08shop.php");
    exit();
}
$prod_imgs = [];
if (!empty($product['image'])) {
    $prod_imgs[] = './assetsHG/images/shop/'. $product['image'];
}
$thumbnails = explode(',', $product['thumbnails']);
foreach ($thumbnails as $thumbnail) {
    if (!empty($thumbnail)) {
        $prod_imgs[] = './assetsHG/images/10prd-details/'. $thumbnail;
    }
}



?>

<link rel="stylesheet" href="assetsHG/style.css">
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

.notification.success {
    background-color: #4CAF50;
}

.notification.error {
    background-color: #f44336;
}
</style>
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="#" class="home-icon" title="Home">
            <i class="fas fa-home" aria-hidden="true"></i>
        </a>
        <span> &gt; </span>
        <a href="#">Category</a>
        <span> &gt; </span>
        <a href="#">Vegetables</a>
        <span> &gt; </span>
        <a href="#" class="active">Chinese Cabbage</a>
    </div>
</div>
<div class="container">
    <!-- Gioi thieu san pham -->
    <div class="product-container">
        <div class="product-images">
            <div class="thumbnail-gallery">
                <?php
                foreach ($prod_imgs as $index => $thumbnail) {
                    echo '<img src="' . htmlspecialchars($thumbnail) . '" alt="Thumbnail ' . ($index + 1) . '">';
                }
                ?>
            </div>
            <div class="main-image" style="width: 85%;">
                <img src="<?php echo !empty($prod_imgs) ? $prod_imgs[0] : ''; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
       
        </div>
        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['name']); ?> <span class="stock-status"><?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></span></h1>
            <div class="rating">
                <?php
                $rating = $product['rating'] ?? 5;
                for($i = 1; $i <= 5; $i++) {
                    echo '<i class="fas fa-star' . ($i <= $rating ? '"' : ' -o"') . '"></i>';
                }
                ?>
                <span><?php echo $product['reviews_count'] ?? 0; ?> Reviews • SKU: <?php echo htmlspecialchars($product['stock']); ?></span>
            </div>
            <div class="price">
                <?php if($product['old_price'] > $product['price']): ?>
                    <span class="original-price">$<?php echo number_format($product['old_price'], 2); ?></span>
                <?php endif; ?>
                $<?php echo number_format($product['price'], 2); ?>
                <?php if($product['old_price'] > $product['price']): ?>
                    <span class="discount"><?php echo round((($product['old_price'] - $product['price']) / $product['old_price']) * 100); ?>% Off</span>
                <?php endif; ?>
            </div>
            <div class="brand">
                <span>Brand: <img src="assetsHG/images/10prd-details/brand.png" alt="Brand Logo"></span>
            </div>
            <div class="description">
                <?php echo htmlspecialchars($product['description']); ?>
            </div>
            <div class="product-add">
                <div class="quantity">
                    <button class="minus-btn"><i class="fas fa-minus"></i></button>
                    <input type="text" id="quantity" value="1" readonly>
                    <button class="plus-btn"><i class="fas fa-plus"></i></button>
                </div>

                <button class="add-to-cart" onclick="addToCart(<?php echo $product_id; ?>, document.getElementById('quantity').value)">
                    Add to Cart <i class="fas fa-shopping-bag"></i>
                </button>

                <button class="wishlist-btn" onclick="addToWishlist(<?php echo $product_id; ?>)">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="category">Category: <?php echo htmlspecialchars($product['category_name']); ?></div>
            <!-- <div class="tags">Tags: <?php echo htmlspecialchars($product['tags']); ?></div> -->
        </div>
    </div>

    <!-- Mo ta san pham -->
    <style>
    .tabs .tab.active {
        color: #00B207;
        border-bottom: 2px solid #00B207;
    }
</style>
<div class="tabs"> <!-- Phần tab điều hướng -->
        <a href="10prd_details.php?id=<?php echo $product_id; ?>" class="tab <?php echo basename($_SERVER['PHP_SELF']) == '10prd_details.php' ? 'active' : ''; ?>" data-tab="description">Description</a>
        <a href="./11prd_info.php?id=<?php echo $product_id; ?>" class="tab <?php echo basename($_SERVER['PHP_SELF']) == '11prd_info.php' ? 'active' : ''; ?>" data-tab="additional-info">Additional Information</a>
        <!-- <a href="./12prd_feedback.php?id=<?php echo $product_id; ?>" class="tab <?php echo basename($_SERVER['PHP_SELF']) == '12prd_feedback.php' ? 'active' : ''; ?>" data-tab="customer-feedback">Customer Feedback</a> -->
    </div>


    <div class="prd-container"> <!-- Khung chính chứa nội dung -->
    <div class="prd-description">
            <p><span>Weight:</span> 03</p>
            <p><span>Color:</span> Green</p>
            <p><span>Type:</span> Organic</p>
            <p><span>Category:</span> Vegetables</p>
            <p><span>Stock Status:</span> Available (5,413)</p>
            <p><span>Tags:</span> Vegetables, Healthy, Chinese Cabbage, Green Cabbage</p>
        </div>

        <div class="image-section"> <!-- Phần hình ảnh và giảm giá bên phải -->
            <img class="img-section" src="assetsHG/images/10prd-details/img.png" alt="Product Image"> <!-- Hình ảnh sản phẩm -->
            <div class="prd-discount"> <!-- Khung thông tin giảm giá -->
                <img src="assetsHG/images/10prd-details/feature.png" alt="Discount Banner"> <!-- Hình ảnh thay thế văn bản -->
            </div>
        </div>
    </div>
    <!-- sản phẩm thay thế -->
    <div class="related-products"> <!-- Phần tab điều hướng -->
        <a href="#" class="active">Related Products</a> <!-- Tab Descriptions, đang được chọn -->
    </div>
    <div class="product-grid">
    <?php
    // Lấy dữ liệu sản phẩm từ cơ sở dữ liệu - chỉ lấy sản phẩm còn hàng
    $sql = "SELECT * FROM products WHERE id <> ? AND stock > 0 ORDER BY RAND() LIMIT 4";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Kiểm tra giảm giá
            $sale_class = !empty($row['old_price']) && $row['old_price'] > $row['price'] ? 'sale' : '';
            ?>
            <!-- Sản phẩm -->
            <div class="product <?php echo $sale_class; ?>">
                <a href="10prd_details.php?id=<?= $row['id']; ?>">
                    <?php if ($sale_class) { ?>
                        <p class="sale-label">Sale <?php echo round((($row['old_price'] - $row['price']) / $row['old_price']) * 100); ?>%</p>
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
                    
                <div class="product-cart-icon" onclick="addToCart(<?php echo $row['id']; ?>)"><i class="fa-solid fa-cart-shopping"></i></div>
            </div>
            <?php
        }
    } else {
        echo "<p>Không tìm thấy sản phẩm liên quan.</p>";
    }
    ?>
    </div>
</div>
<?php include('components/footer.php') ?>

<script src="./assetsHG/javascript.js"></script>
<script>
// Xử lý nút tăng giảm số lượng
document.querySelector('.minus-btn').addEventListener('click', function() {
    let quantity = parseInt(document.getElementById('quantity').value);
    if (quantity > 1) {
        document.getElementById('quantity').value = quantity - 1;
    }
});

document.querySelector('.plus-btn').addEventListener('click', function() {
    let quantity = parseInt(document.getElementById('quantity').value);
    let maxStock = <?php echo $product['stock']; ?>; // Lấy số lượng tồn kho từ PHP
    if (quantity < maxStock) {
        document.getElementById('quantity').value = quantity + 1;
    } else {
        // Hiển thị thông báo khi vượt quá số lượng tồn kho
        const notification = document.createElement('div');
        notification.className = 'notification error';
        notification.textContent = 'Số lượng đã đạt giới hạn tồn kho!';
        document.body.appendChild(notification);
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 2000);
    }
});

// Thêm kiểm tra khi người dùng nhập trực tiếp số lượng
document.getElementById('quantity').addEventListener('input', function() {
    let quantity = parseInt(this.value);
    let maxStock = <?php echo $product['stock']; ?>;
    
    if (isNaN(quantity) || quantity < 1) {
        this.value = 1;
    } else if (quantity > maxStock) {
        this.value = maxStock;
        // Hiển thị thông báo
        const notification = document.createElement('div');
        notification.className = 'notification error';
        notification.textContent = 'Số lượng đã đạt giới hạn tồn kho!';
        document.body.appendChild(notification);
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 2000);
    }
});

// Xử lý active tab
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tabs .tab');
    const currentPath = window.location.pathname;
    
    tabs.forEach(tab => {
        const tabPath = new URL(tab.href).pathname;
        if (tabPath === currentPath) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
});

// Thêm xử lý sự kiện cho nút yêu thích
document.querySelector('.heart-icon').addEventListener('click', function(e) {
    e.preventDefault();
    const productId = <?php echo $product_id; ?>;
    
    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        const notification = document.createElement('div');
        notification.className = 'notification ' + (data.success ? 'success' : 'error');
        notification.textContent = data.message;
        document.body.appendChild(notification);
        notification.style.display = 'block';
        
        if (data.success) {
            // Thay đổi màu icon trái tim
            const heartIcon = document.querySelector('.wishlist-btn i');
            heartIcon.style.color = '#FF8A00';
            heartIcon.style.animation = 'heartBeat 0.5s';
        }
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

function addToWishlist(productId) {
    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        const notification = document.createElement('div');
        notification.className = 'notification ' + (data.success ? 'success' : 'error');
        notification.textContent = data.message;
        document.body.appendChild(notification);
        notification.style.display = 'block';
        
        if (data.success) {
            // Thay đổi màu icon trái tim
            const heartIcon = document.querySelector('.wishlist-btn i');
            heartIcon.style.color = '#FF8A00';
            heartIcon.style.animation = 'heartBeat 0.5s';
        }
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm vào danh sách yêu thích');
    });
}
</script>








