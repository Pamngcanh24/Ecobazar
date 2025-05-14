
<?php include('components/head.php') ?>
<?php include('components/topbar.php') ?>
<?php include('components/header.php') ?>
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
        <img src="assetsHG/images/10prd-details/icon1.png" alt="Thumbnail 1">
            <img src="assetsHG/images/10prd-details/icon2.png" alt="Thumbnail 2">
            <img src="assetsHG/images/10prd-details/icon3.png" alt="Thumbnail 3">
            <img src="assetsHG/images/10prd-details/icon4.png" alt="Thumbnail 4">
        </div>
        <div class="main-image">
            <img src="assetsHG/images/10prd-details/prd_img.png" alt="Chinese Cabbage">
        </div>
    </div>
    <div class="product-details">
        <h1>Chinese Cabbage <span class="stock-status">In Stock</span></h1>
        <div class="rating">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <span>4 Reviews • SKU: 251,594</span>
        </div>
        <div class="price">
            <span class="original-price">$49.00</span> $17.28 <span class="discount">64% Off</span>
        </div>
        <div class="brand">
            <span>Brand: <img src="assetsHG/images/12prd_feedback/brand.png" alt="Brand Logo"></span>
        </div>
        <div class="description">
            Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nibh diam, blandit vel consequat nec, ultrices et ipsum. Nulla magna a consequat pulvinar.
        </div>
        <div class="quantity">
            <button>-</button>
            <input type="text" value="5" readonly>
            <button>+</button>
        </div>
        <button class="add-to-cart">Add to Cart <i class="fas fa-shopping-cart"></i></button>
        <div class="category">Category: Vegetables</div>
        <div class="tags">Tags: Healthy, Chinese Cabbage, Green Cabbage</div>
    </div>
</div>

<!-- Mo ta san pham -->
<div class="tabs"> <!-- Phần tab điều hướng -->
        <a href="./10prd_details.php" class="tab" data-tab="description">Description</a>
        <a href="./11prd_info.php" class="tab" data-tab="additional-info">Additional Information</a>
        <a href="12prd-feedback.php" class="tab" data-tab="customer-feedback">Customer Feedback</a>
    </div>

    <div class="feedback-section">
        <div class="feedback">
            <img src="assetsHG/images/12prd_feedback/avt4.png" alt="User Avatar">
            <div class="feedback-content">
                <div class="feedback-header">
                    <h4>Kristin Watson</h4>
                    <span class="time">2 min ago</span>
                </div>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Duis at ullamcorper nulla, eu dictum eros.</p>
            </div>
        </div>

        <div class="feedback">
            <img src="assetsHG/images/12prd_feedback/avt1.png" alt="User Avatar">
            <div class="feedback-content">
                <div class="feedback-header">
                    <h4>Jane Cooper</h4>
                    <span class="time">30 Apr, 2021</span>
                </div>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Keep the soil evenly moist for the healthiest growth. If the sun gets too hot, Chinese cabbage tends to "bolt" or go to seed; in long periods of heat, some kind of shade may be helpful. Watch out for snails, as they will harm the plants.</p>
            </div>
        </div>

        <div class="feedback">
            <img src="assetsHG/images/12prd_feedback/avt2.png" alt="User Avatar">
            <div class="feedback-content">
                <div class="feedback-header">
                    <h4>Jacob Jones</h4>
                    <span class="time">2 min ago</span>
                </div>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Vivamus eget euismod magna. Nam sed lacinia nibh, et lacinia lacus.</p>
            </div>
        </div>

        <div class="feedback">
            <img src="assetsHG/images/12prd_feedback/avt3.png" alt="User Avatar">
            <div class="feedback-content">
                <div class="feedback-header">
                    <h4>Ralph Edwards</h4>
                    <span class="time">2 min ago</span>
                </div>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>200+ Canton Pak Choi Bok Choy Chinese Cabbage Seeds Heirloom NON-GMO Productive Brassica rapa VAR. chinensis, a.k.a. Canton Choice, Bok Choi, USA</p>
            </div>
        </div>
    </div>
<!-- sản phẩm thay thế -->
    <div class="related-products"> <!-- Phần tab điều hướng -->
        <a href="#" class="active">Related Products</a> <!-- Tab Descriptions, đang được chọn -->
    </div>
    <div class="product-grid">
        <!-- Sản phẩm 1 -->
        <div class="product">
            <img src="assetsHG/images/shop/chili.jpg" alt="Red Chili">
            <h3>Red Chili</h3>
            <p class="price">$14.99</p>
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

        <!-- Sản phẩm 2 -->
        <div class="product">
            <img src="assetsHG/images/shop/potato.png" alt="Big Potatoes">
            <h3>Big Potatoes</h3>
            <p class="price">$14.99</p>
            <p class="rating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-regular fa-star"></i> <!-- Ngôi sao rỗng (chưa full 5 sao) -->
            </p>
            </p>
            <div class="product-icon">
                <div class="product-icon-item heart-icon"><i class="fa-solid fa-heart"></i></div>
                <div class="product-icon-item eye-icon"><i class="fa-solid fa-eye"></i></div>
            </div>
            <div class="cart-icon"><i class="fa-solid fa-cart-shopping"></i></div>
        </div>

        <!-- Sản phẩm 3 (Được chọn) -->
        <div class="product">
            <img src="assetsHG/images/shop/cabbage.png" alt="Chinese Cabbage">
            <h3>Chinese Cabbage</h3>
            <p class="price">$14.99</p>
            <p class="rating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-regular fa-star"></i> <!-- Ngôi sao rỗng (chưa full 5 sao) -->
            </p>
            </p>
            <div class="product-icon">
                <div class="product-icon-item heart-icon"><i class="fa-solid fa-heart"></i></div>
                <div class="product-icon-item eye-icon"><i class="fa-solid fa-eye"></i></div>
            </div>
            <div class="cart-icon"><i class="fa-solid fa-cart-shopping"></i></div>
        </div>

        <!-- Sản phẩm 5 -->
        <div class="product">
            <img src="assetsHG/images/shop/tomato.jpg" alt="Red Tomato">
            <h3>Red Tomato</h3>
            <p class="price">$14.99</p>
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
    </div>
 </div>
<?php include('components/footer.php') ?>