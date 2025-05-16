<?php
// Kết nối DB
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý xóa user
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Kiểm tra user có tồn tại không
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Xóa user bằng prepared statement
        $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            echo "<script>alert('Xóa người dùng thành công!'); window.location.href='user.php';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi xóa người dùng!'); window.location.href='user.php';</script>";
        }
        $delete_stmt->close();
    } else {
        echo "<script>alert('Không tìm thấy người dùng!'); window.location.href='user.php';</script>";
    }
    $check_stmt->close();
    exit;
}

// Phân trang
$limit = 8;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// Lấy dữ liệu user
$sql = "SELECT * FROM users ORDER BY id ASC LIMIT $start, $limit";
$result = $conn->query($sql);

// Tổng số user
$countSql = "SELECT COUNT(*) AS total FROM users";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Users</title>
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
        <li class="active"><i class="fas fa-users"></i> Users</li>
      </ul>
    </aside>

    <main class="main-content">
      <div class="header-row">
        <h2>Users</h2>
        <a href="user_new.php" class="btn-new-category">New user</a>
      </div>

      <table class="category-table">
        <thead>
          <tr>
            <th><input type="checkbox" /></th>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><input type="checkbox" /></td>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                  <a href="user.php?delete_id=<?php echo $row['id']; ?>&page=<?php echo $page; ?>" 
                     onclick="return confirm('Bạn có chắc muốn xóa?')"
                     class="delete-link">
                    <i class="fas fa-trash-alt"></i> Delete
                  </a>
                  <a href="user_edit.php?id=<?php echo $row['id']; ?>" class="edit-link">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4">Không có người dùng nào.</td></tr>
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
        <div>Showing <?php echo min($start + 1, $totalRows); ?> to <?php echo min($start + $limit, $totalRows); ?> of <?php echo $totalRows; ?> users</div>
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
