<?php
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Tiêu đề mặc định'; ?></title>
    <link rel="stylesheet" href="./assets/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
   <!-- Top Bar -->
 <div class="top-bar">
    <div>
        <span>📍 Store location: Lincoln - 394, Illinois, Chicago, USA</span>
    </div>
    <div>
        <div class="dropdown">
            <span>Eng <i class="fa-solid fa-chevron-down"></i></span>
            <div class="dropdown-content">
                <a href="#">English</a>
                <a href="#">Vietnamese</a>
                <a href="#">French</a>
            </div>
        </div>
        <div class="dropdown">
            <span>USD <i class="fa-solid fa-chevron-down"></i></span>
            <div class="dropdown-content">
                <a href="#">USD</a>
                <a href="#">VND</a>
                <a href="#">EUR</a>
            </div>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" onclick="return confirmLogout()">LogOut</a>
        <?php else: ?>
            <a href="login.php">Sign In</a>
            <a href="register.php">Sign Up</a>
        <?php endif; ?>
    </div>
</div>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <a href="#"><img src="./assets/image/Logo.png" alt="Ecobazar Logo"></a>
        </div>

        <div class="search-box">
            <form action="08shop.php" method="GET">
                <input type="text" name="search" placeholder="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        
        <div class="cart-section">
            <a href="wishlist.php"><img src="./assets/image/Vector.png" alt="Favourite"></a>
            <span class="divider"></span>
        
            <div class="cart-info">
                <div class="cart-icon">
                    <a href="15shopping.php"><img src="./assets/image/Rectangle.png" alt="Shopping cart"></a>
                    <?php
                    $cartCount = 0;
                    $cartTotal = 0;
                    if (isset($_SESSION['cart'])) {
                        $cartCount = array_sum($_SESSION['cart']);
                        // Tính tổng giá trị giỏ hàng nếu có thông tin giá sản phẩm
                        if (isset($_SESSION['cart_total'])) {
                            $cartTotal = $_SESSION['cart_total'];
                        }
                    }
                    ?>
                    <span class="cart-badge"><?php echo $cartCount > 0 ? $cartCount : ''; ?></span>
                </div>
                <div class="cart-text">
                    <span>Shopping cart:</span>
                    <strong>$<?php echo number_format($cartTotal, 2); ?></strong>
                </div>
            </div>
        </div>        
    </div>

    <header class="navbar">
    <!-- Menu điều hướng chính -->
    <!-- Navigation -->
    <div class="nav-links">
        <ul class="menu">
            <li>
                <a href="homepage.php">Home <i class="fa-solid fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="settings.php">Account</a></li>
                    <!-- <li><a href="#">Category</a></li> -->
                </ul>
            </li>
            <li>
                <a href="08shop.php">Shop <i class="fa-solid fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="08shop.php?category_id=1">Vegetables</a></li>
                    <li><a href="08shop.php?category_id=2">Fruits</a></li>
                </ul>
            </li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
    </div>
     <!-- Phần thông tin liên hệ -->
     <div class="contact-info">
        <a href="#"><img src="./assets/image/phone.svg" alt="Phone-icon"></a>
        <span>(219) 555-0114</span>
    </div>
    </header>
    
    
    <script src="./assets/scrip.js"></script>
    