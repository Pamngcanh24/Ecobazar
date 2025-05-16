<?php
// Kết nối DB
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý xóa category
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Lấy thông tin ảnh trước khi xóa
    $sql = "SELECT image FROM categories WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $image = $row['image'];
        // Xóa file ảnh nếu tồn tại
        if ($image && file_exists("uploads/" . $image)) {
            unlink("uploads/" . $image);
        }
    }
    
    // Xóa record trong database
    if ($conn->query("DELETE FROM categories WHERE id = $id")) {
        echo "<script>alert('Xóa danh mục thành công!'); window.location.href='category.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa danh mục!'); window.location.href='category.php';</script>";
    }
    exit;
}

// Phân trang
$limit = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// Lấy dữ liệu có phân trang
$sql = "SELECT * FROM categories ORDER BY id ASC LIMIT $start, $limit";
$result = $conn->query($sql);

// Đếm tổng số dòng để phân trang
$countSql = "SELECT COUNT(*) AS total FROM categories";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Categories</title>
  <link rel="stylesheet" href="assets/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="active"><i class="fas fa-th-large"></i> Categories</li>
        <li><a href="product.php"><i class="fas fa-box-open"></i> Products</a></li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
        
      </ul>
    </aside>

    <main class="main-content">
      <div class="header-row">
        <h2>Categories</h2>
        <a href="category_new.php" class="btn-new-category">New category</a>
      </div>

      <table class="category-table">
        <thead>
          <tr>
            <th><input type="checkbox" /></th>
            <th>Image</th>
            <th>Name</th>
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
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Category image" style="width: 80px; height: 80px; object-fit: cover;" />
                  <?php else: ?>
                    <div style="width: 50px; height: 50px; background: #eee;"></div>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>
                  <a href="category.php?delete_id=<?php echo $row['id']; ?>&page=<?php echo $page; ?>" 
                     onclick="return confirm('Bạn có chắc muốn xóa?')"
                     class="delete-link">
                    <i class="fas fa-trash-alt"></i> Delete
                  </a>
                  <a href="category_edit.php?id=<?php echo $row['id']; ?>" class="edit-link">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="3">Không có dữ liệu</td></tr>
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
        <div>
          Per page
          <select disabled>
            <option selected>8</option>
            <option>20</option>
            <option>50</option>
          </select>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
<?php
$conn->close();
?>
