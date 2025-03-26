<?php


// Kết nối database
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}

// Giả sử user đã đăng nhập, lấy thông tin user từ session
session_start();
$user_id = $_SESSION['user_id'] ?? 1; // Test với user_id = 1

// Lấy thông tin người dùng
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Lấy lịch sử đơn hàng
$order_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5");
?>

<?php include 'head.php'; ?>
 
   <!-- Breadcrumb -->
   <div class="breadcrumb-container">
        <div class="breadcrumb">
            <a href="#" class="home-icon" title="Home">
                <i class="fas fa-home" aria-hidden="true"></i>
            </a>
            <span> &gt; </span>
            <a href="#">Account</a>
            <span> &gt; </span>
            <a href="#" class="active">Dashboard</a>
        </div>
    </div>

    <!--dashboard-->
    <div class="container-dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-chart-bar"></i> Dashboard</a></li>
                <li><a href="order-history.php"><i class="fa-solid fa-box"></i> Order History</a></li>
                <li><a href="#"><i class="fa-solid fa-heart"></i> Wishlist</a></li>
                <li><a href="#"><i class="fa-solid fa-cart-shopping"></i> Shopping Cart</a></li>
                <li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a></li>
                <li><a href="#"><i class="fa-solid fa-right-from-bracket"></i> Log-out</a></li>
            </ul>
        </div>

    <div class="main-content">
        <div class="card-container">
            <div class="card profile-info">
                 <img src="assets/image/Group.png" alt="Profile Picture">
                <p><?= isset($user['name']) ? htmlspecialchars($user['name']) : 'Chưa cập nhật' ?></p>
                <p><?php echo $user['email']; ?></p>
                <a href="#" class="edit-link">Edit Profile</a>
            </div>
       
        <div class="card address-info">
             <h3>Billing Address</h3>
             <p><?= htmlspecialchars($user['address'] ?? 'Chưa cập nhật') ?></p>
             <p><?= htmlspecialchars($user['email'] ?? 'Chưa cập nhật') ?></p>
             <p><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></p>
            <a href="#" class="edit-link">Edit Address</a>
        </div>
        </div>

            <div class="card order-history">
                <div class="order-header">
                <h3>Recent Order History</h3>
                <a href="#" class="edit-link">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $order_query->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo $order['created_at']; ?></td>
                            <td>$<?php echo $order['total']; ?></td>
                            <td><?php echo $order['status']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="./assets/scrip.js"></script>
</body>
</html>
