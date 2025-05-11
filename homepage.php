<?php session_start();


// Thiết lập thời gian kết thúc (ví dụ: 3 tiếng kể từ bây giờ)
$end_time = strtotime("+3 hours") * 1000; // JavaScript dùng milliseconds

$price = 79.99;

$discount = 64;
require './database/db.php'; // Kết nối đến cơ sở dữ liệu
$stmt = $conn->query("SELECT * FROM products LIMIT 10"); // Lấy 8 sản phẩm đầu tiên từ bảng products

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./css/homepage.css">
    <title>Ecobazar</title>
</head>

<body>
    <?php include './includes/head.php'; ?>

    <div class="wrapper">
        <div id="banner1">
            <div class="banner1-1">
                <div class="banner1-1-content">
                    <p class="Fresh">Fresh & Healthy<br>Organic Food</p>
                    <span class="saleup">Sale up to</span>
                    <span class="discount">30% OFF</span>
                    <p style="opacity: 80%;">Free shipping on all your order.</p>
                    <a href="#" class="shop-btn">Shop now →</a>
                </div>
            </div>

            <div class="banner1-2">
                <div class="banner1-2-content">
                    <h4>SUMMER SALE</h4>
                    <h1>75% OFF</h1>
                    <p style="opacity: 60%;">Only Fruit & Vegetable</p>
                    <a href="#" class="shop-now2">Shop Now</a>
                </div>
            </div>

            <div class="banner1-3">
                <div class="overlay">
                    <h4>BEST DEAL</h4>
                    <h1>Special Products<br>Deal of the Month</h1>
                    <a href="#" class="shop-now3">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="clear"></div>

        <!-- featured -->
        <div class="featured">

            <div class="fearured-1">
                <div class="fearured-1-text">
                    <img src="./img/freeship.png" alt="Shipping Icon">
                </div>
                <div class="shipping-text">
                    <strong>Free Shipping</strong><br>
                    <span>Free shipping on all your order</span>
                </div>
            </div>

            <div class="fearured-2">
                <div class="fearured-2-text">
                    <img src="./img/customer.png" alt="customer Icon">
                </div>
                <div class="customer-text">
                    <strong>Customer Support 24/7</strong><br>
                    <span>Instant access to Support</span>
                </div>
            </div>

            <div class="fearured-3">
                <div class="fearured-3-text">
                    <img src="./img/shopping-bag.png" alt="shopping Icon">
                </div>
                <div class="paying-text">
                    <strong>100% Secure Payment</strong><br>
                    <span>We ensure your money is save</span>
                </div>
            </div>

            <div class="fearured-4">
                <div class="fearured-4-text">
                    <img src="./img/package.png" alt="packe Icon">
                </div>
                <div class="packe-text">
                    <strong>Money-Back Guarantee</strong><br>
                    <span>30 Days Money-Back Guarantee</span>
                </div>
            </div>


        </div>
        <!-- <div class="clear2"></div> -->

        <div class="category">

            <div class="title">
                <h2>Popular Categories</h2>
                <a href="08shop.php"> View All <span class="arrow">→</span></a>
            </div>
            <ul class="category_list">
                <li>
                    <img src="./img/category1.png" alt="Fruit">
                    <p>Fresh Fruit</p>
                </li>

                <li>
                    <img src="./img/category2.png" alt="Fruit">
                    <p>Fresh Vegetables</p>
                </li>

                <li>
                    <img src="./img/category3.png" alt="Fruit">
                    <p>Meat & Fish</p>
                </li>

                <li>
                    <img src="./img/category4.png" alt="Fruit">
                    <p>Snacks</p>
                </li>

                <li>
                    <img src="./img/category5.png" alt="Fruit">
                    <p>Beverages</p>
                </li>

                <li>
                    <img src="./img/category6.png" alt="Fruit">
                    <p>Beauty & Health</p>
                </li>

                <li>
                    <img src="./img/category7.png" alt="Fruit">
                    <p>Bread & Bakery</p>
                </li>

                <li>
                    <img src="./img/category8.png" alt="Fruit">
                    <p>Baking Needs</p>
                </li>

                <li>
                    <img src="./img/category9.png" alt="Fruit">
                    <p>Cooking</p>
                </li>

                <li>
                    <img src="./img/category10.png" alt="Fruit">
                    <p>Diabetic Food</p>
                </li>

                <li>
                    <img src="./img/category11.png" alt="Fruit">
                    <p>Dish Detergents</p>
                </li>

                <li>
                    <img src="./img/category12.png" alt="Fruit">
                    <p>Oil</p>
                </li>
            </ul>

        </div>
        <!-- Product -->
        <div class="product">
            <div class="title">
                <h2>Popular Products</h2>
                <a href="08shop.php"> View All <span class="arrow">→</span></a>
            </div>
            <ul class="product_list">
               
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <li>
                        <!-- Hiển thị hình ảnh sản phẩm (nếu có) -->
                        <?php if (!empty($row['image'])): ?>
                            <img src="./img/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-image">
                        <?php endif; ?>

                        <!-- Hiển thị tên sản phẩm -->
                        <span class="product-name"><?= htmlspecialchars($row['name']) ?></span><br>
                        <span class="price"><?= htmlspecialchars($row['price'])?></span>
                        <span class="old-price"><?= htmlspecialchars($row['old_price'])?></span><br>
                        <span class="rating"> ★★★★☆</span>
                       
                            
                <?php endwhile; ?>

            </ul>
        </div>



        <!-- Sale -->
        <div class="banner2">
            <!-- banner2-1 -->
            <div class="banner2-1">

                <h2>BEST DEALS</h2>
                <h1>Sale of the Month</h1>

                <div class="countdown">
                    <div><span id="days">00</span>DAYS</div>
                    <div class="colon">:</div>
                    <div><span id="hours">00</span>HOURS</div>
                    <div class="colon">:</div>
                    <div><span id="minutes">00</span>MINS</div>
                    <div class="colon">:</div>
                    <div><span id="seconds">00</span>SECS</div>
                </div>


                <a href="#" class="shop-button">Shop Now →</a>

                <script>
                    const endTime = <?php echo $end_time; ?>;

                    function updateCountdown() {
                        const now = new Date().getTime();
                        const timeLeft = endTime - now;

                        if (timeLeft <= 0) {
                            document.querySelector(".countdown").innerHTML = "<strong>Sale Ended</strong>";
                            return;
                        }

                        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                        document.getElementById("days").textContent = String(days).padStart(2, '0');
                        document.getElementById("hours").textContent = String(hours).padStart(2, '0');
                        document.getElementById("minutes").textContent = String(minutes).padStart(2, '0');
                        document.getElementById("seconds").textContent = String(seconds).padStart(2, '0');
                    }

                    setInterval(updateCountdown, 1000);
                    updateCountdown();
                </script>

            </div>

            <!-- banner2-2 -->
            <div class="banner2-2">
                <div class="promo-box">
                    <div class="small-text">85% FAT FREE</div>
                    <div class="main-title">Low-Fat Meat</div>
                    <div class="price">Started at <span class="highlight">$<?php echo number_format($price, 2); ?></span></div>
                    <a href="#" class="btn">Shop Now <span class="arrow">→</span></a>
                </div>
            </div>

            <!-- banner2-3 -->
            <div class="banner2-3">
                <div class="promo-box2">
                    <div class="small-text">SUMMER SALE</div>
                    <div class="main-title">100% Fresh Fruit</div>
                    <div class="discount-row">Up to
                        <span class="discount-badge"><?php echo $discount; ?>% OFF</span>
                    </div>
                    <a href="#" class="btn">Shop Now <span class="arrow">→</span></a>
                </div>
            </div>

        </div>

    </div>
    <?php include './includes/footer.php'; ?>
</body>

</html>