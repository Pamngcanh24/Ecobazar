<?php
session_start();
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Kết nối DB
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý xóa đơn hàng
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Xóa đơn hàng
    $delete_sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Xóa đơn hàng thành công!'); window.location.href='order.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa đơn hàng!'); window.location.href='order.php';</script>";
    }
    $stmt->close();
}

// Phân trang
$limit = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// Lấy dữ liệu đơn hàng có phân trang
$sql = "SELECT o.*, u.email as user_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC 
        LIMIT $start, $limit";
$result = $conn->query($sql);

// Đếm tổng số dòng để phân trang
$countSql = "SELECT COUNT(*) AS total FROM orders";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Orders Management</title>
  <link rel="stylesheet" href="assets/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="category.php"><i class="fas fa-th-large"></i> Categories</a></li>
        <li><a href="product.php"><i class="fas fa-box-open"></i> Products</a></li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
        <li class="active"><i class="fas fa-shopping-cart"></i> Orders</li>
      </ul>
    </aside>

    <main class="main-content">
      <div class="header-row">
        <h2>Orders Management</h2>
      </div>

      <table class="category-table">
        <thead>
          <tr>
            <th><input type="checkbox" /></th>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><input type="checkbox" /></td>
                <td>#<?php echo $row['id']; ?></td>
                <td>
                  <div class="customer-info">
                    <div class="customer-name"><?php echo htmlspecialchars($row['billing_name']); ?></div>
                    <div class="customer-email"><?php echo htmlspecialchars($row['billing_email']); ?></div>
                    <div class="customer-address"><?php echo htmlspecialchars($row['shipping_address']); ?></div>
                  </div>
                </td>
                <td><?php echo date('d M Y', strtotime($row['order_date'])); ?></td>
                <td>$<?php echo number_format($row['total'], 2); ?></td>
                <td>
                  <span class="status-badge <?php echo strtolower($row['status']); ?>">
                    <?php echo ucfirst($row['status']); ?>
                  </span>
                </td>
                <td><?php echo ucfirst($row['payment_method']); ?></td>
                <td>
                  <a href="order.php?delete_id=<?php echo $row['id']; ?>&page=<?php echo $page; ?>" 
                     onclick="return confirm('Bạn có chắc muốn xóa?')"
                     class="delete-link">
                    <i class="fas fa-trash-alt"></i> Delete
                  </a>
                  <a href="order_edit.php?id=<?php echo $row['id']; ?>" class="edit-link">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7">Không có đơn hàng nào</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Phân trang -->
      <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="page-item"><i class="fa-solid fa-angle-left"></i></a>
        <?php else: ?>
            <span class="page-item disabled"><i class="fa-solid fa-angle-left"></i></span>
        <?php endif; ?>

        <?php
        // Hiển thị các số trang
        for ($i = 1; $i <= ceil($totalRows / $limit); $i++) {
            if ($i == $page) {
                echo "<span class=\"page-item active\">$i</span>";
            } else {
                echo "<a href=\"?page=$i\" class=\"page-item\">$i</a>";
            }
        }
        ?>

        <?php if ($page < ceil($totalRows / $limit)): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="page-item"><i class="fa-solid fa-angle-right"></i></a>
        <?php else: ?>
            <span class="page-item disabled"><i class="fa-solid fa-angle-right"></i></span>
        <?php endif; ?>
      </div>

      <div class="table-footer">
        <div>Showing <?php echo min($start + 1, $totalRows); ?> to <?php echo min($start + $limit, $totalRows); ?> of <?php echo $totalRows; ?> results</div>
      </div>
    </main>
  </div>
</body>
</html>
<?php
$conn->close();
?>