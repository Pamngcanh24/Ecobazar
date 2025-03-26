<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}

$user_id = $_SESSION['user_id'] ?? 1; // Tạm thời gán user_id = 1

// Lấy lịch sử đơn hàng của người dùng
$order_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

include 'head.php';
?>

<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="dashboard.php" title="Home"><i class="fas fa-home"></i></a>
        <span> &gt; </span>
        <a href="dashboard.php">Account</a>
        <span> &gt; </span>
        <a href="order-history.php" class="active">Order History</a>
    </div>
</div>

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
        <h3>Order History</h3>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $order_query->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                    <td>$<?php echo $order['total']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td><a href="order_detail.php?id=<?php echo $order['id']; ?>">View Details</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
