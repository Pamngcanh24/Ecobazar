
<link rel="stylesheet" href="./assetsHG/style.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<!-- Newsletter Section -->
<div class="newsletter">
        <div class="text">
            <h3>Subscribe our Newsletter</h3>
            <p>Stay updated with our latest news and offers.</p>
        </div>
        <div class="subscribe-form">
            <input type="email" placeholder="Your email address">
            <button>Subscribe</button>
        </div>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-pinterest"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-column">
            <img src="assets/images/footer/Group.png" alt="Ecobazar Logo" class="footer-logo">
            <span class="logo-text">Ecobazar</span>

            <p>Morbi cursus porttitor enim lobortis molestie. Duis gravida turpis dui, eget bibendum magna congue nec.</p>
            <div class="contact-info">
                <div class="contact-info">
                    <span class="custom-underline">(219) 555-0114</span>
                    <span class="gray-text">or</span>
                    <span class="custom-underline">Proxy@gmail.com</span>
                </div>
            </div>
        </div>
        <div class="footer-column">
            <h3>My Account</h3>
            <ul>
                <li><a href="#">My Account</a></li>
                <li><a href="#">Order History</a></li>
                <li><a href="#">Shopping Cart</a></li>
                <li><a href="#">Wishlist</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Helps</h3>
            <ul>
                <li><a href="#">Contact</a></li>
                <li><a href="#">FAQs</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Proxy</h3>
            <ul>
                <li><a href="#">About</a></li>
                <li><a href="#">Shop</a></li>
                <li><a href="#">Product</a></li>
                <li><a href="#">Track Order</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Categories</h3>
            <ul>
                <li><a href="#">Fruit & Vegetables</a></li>
                <li><a href="#">Meat & Fish</a></li>
                <li><a href="#">Bread & Bakery</a></li>
                <li><a href="#">Beauty & Health</a></li>
            </ul>
        </div>
    <div class="footer-bottom">
        <p>Ecobazar eCommerce © 2021. All Rights Reserved</p>
        <div class="payment-icons">
            <i class="fa-brands fa-cc-apple-pay"></i>
            <i class="fa-brands fa-cc-visa"></i>
            <i class="fa-brands fa-cc-mastercard"></i>
            <i class="fa-brands fa-cc-discover"></i>
            <i class="fa-solid fa-lock"></i>
        </div>
        
    </div>
    </footer>
<!-- Thông báo -->
<div id="notification" class="notification"></div>

<!-- Thêm JavaScript để xử lý thêm vào giỏ hàng -->
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
            // Hiển thị thông báo
            const notification = document.getElementById('notification');
            notification.textContent = data.message;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);

            // Cập nhật số lượng trong giỏ hàng
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                cartBadge.textContent = data.total_items;
                cartBadge.style.display = data.total_items > 0 ? 'block' : 'none';
            }

            // Cập nhật tổng tiền giỏ hàng
            const cartTotal = document.querySelector('.cart-text strong');
            if (cartTotal) {
                cartTotal.textContent = '$' + parseFloat(data.cart_total).toFixed(2);
            }

            // Thêm hiệu ứng animation cho icon giỏ hàng
            const cartIcon = document.querySelector('.cart-icon');
            cartIcon.classList.add('cart-animation');
            setTimeout(() => {
                cartIcon.classList.remove('cart-animation');
            }, 500);
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
    });
}
</script>
<?php 
$conn->close();
?>
</body>
</html>