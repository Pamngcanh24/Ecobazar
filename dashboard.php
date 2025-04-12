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

// Lấy thông tin đơn hàng gần nhất của user
$last_order_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1");
$last_order = $last_order_query->fetch_assoc();

$pageTitle = "Dashboard";
include 'head.php'; 
?>


 
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

    <?php include 'dash.php'; ?>

      <!-- Main Content -->
    <div class="main-content">
        <div class="card-container">
            <div class="card profile-info">
                 <img src="assets/image/Group.png" alt="Profile Picture">
                <p><?= isset($user['first_name']) ? htmlspecialchars($user['first_name']) : 'Chưa cập nhật' ?></p>
                <p>Customer</p>
                <!-- <p><?php echo $user['email']; ?></p> -->
                <a href="settings.php" class="edit-link">Edit Profile</a>
            </div>
       
        <div class="card address-info">
             <h3>Billing Address</h3>
             <p><?= htmlspecialchars($last_order['billing_address'] ?? 'Chưa cập nhật') ?></p>
             <p><?= htmlspecialchars($last_order['billing_email'] ?? 'Chưa cập nhật') ?></p>
             <p><?= htmlspecialchars($last_order['billing_phone'] ?? 'Chưa cập nhật') ?></p>

            <a href="settings.php" class="edit-link">Edit Address</a>
        </div>
        </div>

            <div class="card order-history">
                <div class="order-header">
                <h3>Recent Order History</h3>
                <a href="order-history.php" class="edit-link">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                <?php while ($order = $order_query->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo date('d M, Y', strtotime($order['created_at'])); ?></td>
                        <td>$<?php echo number_format($order['total'], 2); ?></td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                        <td><a href="order-detail.php?id=<?php echo $order['id']; ?>" class="edit-link">View Details</a></td>
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
