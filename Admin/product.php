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

// Xử lý xóa sản phẩm (nếu có request delete)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Lấy thông tin ảnh trước khi xóa
    $sql = "SELECT image FROM products WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $image = $row['image'];
        // Xóa file ảnh nếu tồn tại
        if ($image && file_exists("uploads/" . $image)) {
            unlink("uploads/" . $image);
        }
    }
    
    // Xóa record trong database
    if ($conn->query("DELETE FROM products WHERE id = $id")) {
        echo "<script>alert('Xóa sản phẩm thành công!'); window.location.href='product.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa sản phẩm!'); window.location.href='product.php';</script>";
    }
    exit;
}
// Phân trang
$limit = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// Lấy danh sách sản phẩm có phân trang
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC LIMIT $start, $limit";
$result = $conn->query($sql);

// Đếm tổng số sản phẩm để phân trang
$countSql = "SELECT COUNT(*) AS total FROM products";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Products</title>
  <link rel="stylesheet" href="assets/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="category.php"><i class="fas fa-th-large"></i> Categories</a></li>
        <li class="active"><i class="fas fa-box-open"></i> Products</li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
      </ul>
    </aside>

    <main class="main-content">
      <div class="header-row">
        <h2>Products</h2>
        <a href="product_new.php" class="btn-new-category">New product</a>
      </div>

      <table class="category-table">
        <thead>
          <tr>
            <th><input type="checkbox" /></th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><input type="checkbox" /></td>
                <td>
                  <?php if ($row['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Product image" style="width: 60px; height: 60px; object-fit: cover;" />
                  <?php else: ?>
                    <div style="width: 50px; height: 50px; background: #eee;"></div>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo number_format($row['price'], 0, ',', '.'); ?>.000đ</td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td>
                  <a href="product.php?delete_id=<?php echo $row['id']; ?>" 
                     onclick="return confirm('Bạn có chắc muốn xóa?')"
                     class="delete-link">
                    <i class="fas fa-trash-alt"></i> Delete
                  </a>
                  <a href="product_edit.php?id=<?php echo $row['id']; ?>" class="edit-link">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6">Không có dữ liệu</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <!-- Phân trang -->
      <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>
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