<?php include('components/head.php') ?>
<?php include('components/topbar.php') ?>
<?php include('components/header.php') ?>
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

        <!-- Sản phẩm 4 (Hết hàng) -->
        <div class="product out-of-stock">
            <p class="stock-label">Out of Stock</p>
            <img src="assetsHG/images/shop/corn.png" alt="Ladies Finger">
            <h3>Ladies Finger</h3>
            <p class="price">$14.99 <span class="old-price">$20.99</span></p>
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
        <!-- Sản phẩm 6 -->
        <div class="product">
            <img src="assetsHG/images/shop/eggplant.jpg" alt="Eggplant">
            <h3>Eggplant</h3>
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
        <!-- Sản phẩm 7 -->
        <div class="product">
            <img src="assetsHG/images/shop/cauliflower.jpg" alt="Fresh Cauliflower">
            <h3>Fresh Cauliflower</h3>
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
        <!-- Sản phẩm 8 (sale 50%) -->
        <div class="product">
            <img src="assetsHG/images/shop/apple.jpg" alt="Green Apple">
            <h3>Green Apple</h3>
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
        <!-- Sản phẩm 9 -->
        <div class="product">
            <img src="assetsHG/images/shop/mango.jpg" alt="Fresh Mango">
            <h3>Fresh Mango</h3>
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
        <!-- Sản phẩm 10 -->
        <div class="product">
            <img src="assetsHG/images/shop/capsicum.jpg" alt="Rresh Capsicum">
            <h3>Fresh Capsicum</h3>
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
        <!-- Sản phẩm 11 -->
        <div class="product">
            <img src="assetsHG/images/shop/chili2.jpg" alt="Green Chili">
            <h3>Green Chili</h3>
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
        <!-- Sản phẩm 12 (sale 50%) -->
        <div class="product sale">
            <p class="sale-label">Sale 50%</p>
            <img src="assetsHG/images/shop/cucumper.jpg" alt="Green Cucumper">
            <h3>Green Cucumper</h3>
            <p class="price">$14.99 <span class="old-price">$20.99</span></p>
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
        <!-- Sản phẩm 13 -->
        <div class="product">
            <img src="assetsHG/images/shop/corn.png" alt="Fresh Corn">
            <h3>Fresh Corn</h3>
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
        <!-- Sản phẩm 14 -->
        <div class="product">
            <img src="assetsHG/images/shop/lettuce.jpg" alt="Green Lettuce">
            <h3>Green Lettuce</h3>
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
        <!-- Sản phẩm 15 -->
        <div class="product">
            <img src="assetsHG/images/shop/finger.jpg" alt="Ladies Finger">
            <h3>Ladies Finger</h3>
            <p class="price">$14.99</p>
            <p class="rating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-regular fa-star"></i>
            </p>
            <div class="product-icon">
                <div class="product-icon-item heart-icon"><i class="fa-solid fa-heart"></i></div>
                <div class="product-icon-item eye-icon"><i class="fa-solid fa-eye"></i></div>
            </div>
            <div class="cart-icon"><i class="fa-solid fa-cart-shopping"></i></div>
        </div>
        <!-- Sản phẩm 16 -->
        <div class="product">
            <img src="assetsHG/images/shop/capsicum2.jpg" alt="Red Capsicum">
            <h3>Red Capsicum</h3>
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
<?php include('components/footer.php') ?>
</body>
</html>
