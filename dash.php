<div class="container-dashboard">
    
    <?php
    $menu_items = [
        "dashboard.php" => ["Dashboard", "fa-chart-bar"],
        "order-history.php" => ["Order History", "fa-box"],
        "wishlist.php" => ["Wishlist", "fa-heart"],
        "cart.php" => ["Shopping Cart", "fa-cart-shopping"],
        "settings.php" => ["Settings", "fa-gear"],
        "logout.php" => ["Log-out", "fa-right-from-bracket"]
    ];
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
        <?php foreach ($menu_items as $link => $item): ?>
                <li>
                    <a 
                        href="<?php echo $link; ?>" 
                        class="<?php echo ($current_page == $link) ? 'active' : ''; ?>"
                        <?php if ($link == "logout.php"): ?>
                            onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?');"
                        <?php endif; ?>
                    >
                        <i class="fa-solid <?php echo $item[1]; ?>"></i> <?php echo $item[0]; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
