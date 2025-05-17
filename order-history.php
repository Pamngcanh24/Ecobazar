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

// Phân trang
$limit = 5; // Số đơn hàng mỗi trang
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Đếm tổng số đơn hàng
$count_result = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id = $user_id");
$total_orders = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $limit);

// Lấy danh sách đơn hàng có phân trang
$order_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

$pageTitle = "Order history";
include './includes/head.php';
?>

<link rel="stylesheet" href="style.css">
<style>
.pagination {
    margin-top: 20px;
    text-align: center;
}
.pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 4px;
    background-color: #f0f0f0;
    color: #333;
    text-decoration: none;
    border-radius: 4px;
}
.pagination a.active {
    background-color: #00a859;
    color: white;
    font-weight: bold;
}
.pagination a:hover {
    background-color: #00a859;
    color: white;
}
</style>

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
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    </div>
</div>
</div>


<?php include './includes/footer.php'; ?>

