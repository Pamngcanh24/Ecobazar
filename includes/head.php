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
            <input type="text" placeholder="Search">
            <button>Search</button>
        </div>

        <div class="cart-section">
            <a href="#"><img src="./assets/image/Vector.png" alt="Favourite"></a>
            <span class="divider"></span>
        
            <div class="cart-info">
                <div class="cart-icon">
                    <a href="#"><img src="./assets/image/Rectangle.png" alt="Shopping cart"></a>
                    <span class="cart-badge"></span>
                </div>
                <div class="cart-text">
                    <span>Shopping cart:</span>
                    <strong>$57.00</strong>
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
                    <li><a href="#">Product 1</a></li>
                    <li><a href="#">Product 2</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Shop <i class="fa-solid fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="#">Product 1</a></li>
                    <li><a href="#">Product 2</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Pages <i class="fa-solid fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Blog <i class="fa-solid fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
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
    
