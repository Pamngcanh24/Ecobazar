<?php
session_start();
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}

$user_id = $_SESSION['user_id'] ?? 1; // Tạm thời gán user_id = 1

// Lấy lịch sử đơn hàng của người dùng
$order_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

$pageTitle = "Order history";
include './includes/head.php';
?>

<link rel="stylesheet" href="style.css">


<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="homepage.php" title="Home"><i class="fas fa-home"></i></a>
        <span> &gt; </span>
        <a href="dashboard.php">Account</a>
        <span> &gt; </span>
        <a href="order-history.php" class="active">Order History</a>
    </div>
</div>

<?php include './includes/dash.php'; ?>
    <!-- Main Content -->
    <div class="main-content">
    <div class="card order-history">
        <div class="order-header">
            <h3>Order History</h3>
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


<?php include './includes/footer.php'; ?>

