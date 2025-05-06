<head>
    <title>Product info</title>
</head>
<?php include('components/head.php') ?>
<?php include('includes/head.php') ?>
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
            <span>Brand: <img src="assetsHG/images/10prd-details/brand.png" alt="Brand Logo"></span>
        </div>
        <div class="description">
            Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nibh diam, blandit vel consequat nec, ultrices et ipsum. Nulla magna a consequat pulvinar.
        </div>
        <div class="product-add">
                <div class="quantity">
                    <button><i class="fas fa-minus"></i></button>
                    <input type="text" value="5" readonly>
                    <button><i class="fas fa-plus"></i></button>
                </div>

                <button class="add-to-cart">
                    Add to Cart <i class="fas fa-shopping-bag"></i>
                </button>

                <button class="wishlist-btn">
                    <i class="far fa-heart"></i>
                </button>
            </div>
        <div class="category">Category: Vegetables</div>
        <div class="tags">Tags: Healthy, Chinese Cabbage, Green Cabbage</div>
    </div>
</div>

<!-- Mo ta san pham -->
<div class="tabs"> <!-- Phần tab điều hướng -->
        <a href="./10prd_details.php" class="tab" data-tab="description">Description</a>
        <a href="11prd-info.php" class="tab" data-tab="additional-info">Additional Information</a>
        <a href="./12prd_feedback.php" class="tab" data-tab="customer-feedback">Customer Feedback</a>
    </div>

    <div class="content11">
        <div class="11description11 prd-description">
            <p><span>Weight:</span> 03</p>
            <p><span>Color:</span> Green</p>
            <p><span>Type:</span> Organic</p>
            <p><span>Category:</span> Vegetables</p>
            <p><span>Stock Status:</span> Available (5,413)</p>
            <p><span>Tags:</span> Vegetables, Healthy, Chinese Cabbage, Green Cabbage</p>
        </div>

        <div class="image-section"> <!-- Phần hình ảnh và giảm giá bên phải -->
            <img src="assetsHG/images/10prd-details/img.png" alt="Product Image"> <!-- Hình ảnh sản phẩm -->
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