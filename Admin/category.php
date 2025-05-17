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
        $_SESSION['success_message'] = "Xóa danh mục thành công!";
        header("Location: category.php");
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi xóa danh mục!";
        header("Location: category.php");
    }
    exit;
}

// Hiển thị thông báo nếu có
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

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
  <style>
      .alert {
          position: fixed;
          top: 20px;
          right: 20px;
          padding: 15px 30px;
          border-radius: 8px;
          font-family: 'Poppins', sans-serif;
          font-size: 15px;
          display: flex;
          align-items: center;
          gap: 10px;
          animation: slideIn 0.5s ease-out forwards, fadeOut 0.5s ease-out 2.5s forwards;
          z-index: 1000;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }
  
      .alert-success {
          background-color: #00b207;
          color: white;
      }
  
      .alert-error {
          background-color: #dc3545;
          color: white;
      }
  
      @keyframes slideIn {
          from {
              transform: translateX(100%);
              opacity: 0;
          }
          to {
              transform: translateX(0);
              opacity: 1;
          }
      }
  
      @keyframes fadeOut {
          from {
              transform: translateX(0);
              opacity: 1;
          }
          to {
              transform: translateX(100%);
              opacity: 0;
          }
      }
  </style>
</head>
<body>
    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo $success_message; ?>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>

    <div class="dashboard-container">
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="active"><i class="fas fa-th-large"></i> Categories</li>
        <li><a href="product.php"><i class="fas fa-box-open"></i> Products</a></li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="order.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        
      </ul>
    </aside>

    <main class="main-content">
      <div class="header-row">
        <h2>Categories Management</h2>
        <a href="category_new.php" class="btn-new-category">New category</a>
      </div>

      <table class="category-table">
        <thead>
          <tr>
            <th><input type="checkbox" /></th>
            <th>ID</th>
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
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td>
                  <?php if ($row['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Category image" style="width: 80px; height: 80px; object-fit: cover;" />
                  <?php else: ?>
                    <div style="width: 50px; height: 50px; background: #eee;"></div>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>
                  <a href="#" 
                     onclick="showConfirmModal('category.php?delete_id=<?php echo $row['id']; ?>&page=<?php echo $page; ?>'); return false;"
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

<style>
  .confirm-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    font-family: 'Poppins', sans-serif;
  }

  .confirm-content {
    background: white;
    padding: 35px;
    border-radius: 12px;
    text-align: center;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  }

  .confirm-content h3 {
    margin: 0 0 20px;
    color: #333;
    font-size: 24px;
    font-weight: 600;
  }

  .confirm-content p {
    color: #666;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 25px;
  }

  .confirm-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 25px;
  }

  .confirm-buttons button {
    padding: 10px 25px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    font-size: 15px;
    transition: all 0.3s ease;
  }

  .btn-confirm {
    background: #dc3545;
    color: white;
  }

  .btn-confirm:hover {
    background: #c82333;
  }

  .btn-cancel {
    background: #6c757d;
    color: white;
  }

  .btn-cancel:hover {
    background: #5a6268;
  }
</style>

<!-- Thêm modal xác nhận xóa -->
<div id="confirmModal" class="confirm-modal">
  <div class="confirm-content">
    <h3>Xác nhận xóa</h3>
    <p>Bạn có chắc chắn muốn xóa danh mục này?</p>
    <div class="confirm-buttons">
      <button id="confirmDelete" class="btn-confirm">Xóa</button>
      <button onclick="closeModal()" class="btn-cancel">Hủy</button>
    </div>
  </div>
</div>

<script>
function showConfirmModal(deleteUrl) {
  document.getElementById('confirmModal').style.display = 'flex';
  document.getElementById('confirmDelete').onclick = function() {
    window.location.href = deleteUrl;
  };
}

function closeModal() {
  document.getElementById('confirmModal').style.display = 'none';
}

// Đóng modal khi click ra ngoài
window.onclick = function(event) {
  let modal = document.getElementById('confirmModal');
  if (event.target == modal) {
    closeModal();
  }
}
</script>
