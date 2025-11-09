<?php
include 'includes/header.php';

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
        $_SESSION['success_message'] = "Xóa sản phẩm thành công!";
        header("Location: product.php");
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi xóa sản phẩm!";
        header("Location: product.php");
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

    <main class="main-content">
      <div class="header-row">
        <h2>Products Management</h2>
        <a href="product_new.php" class="btn-new-category">New product</a>
      </div>

      <table class="category-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td>
                  <?php if ($row['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Product image" style="width: 60px; height: 60px; object-fit: cover;" />
                  <?php else: ?>
                    <div style="width: 50px; height: 50px; background: #eee;"></div>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo number_format($row['price'], 0, ',', ','); ?> $</td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                <td>
                  <a href="#" 
                     onclick="showConfirmModal('product.php?delete_id=<?php echo $row['id']; ?>&page=<?php echo $page; ?>'); return false;"
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

  <!-- Thêm modal xác nhận xóa -->
  <div id="confirmModal" class="confirm-modal">
    <div class="confirm-content">
      <h3>Xác nhận xóa</h3>
      <p>Bạn có chắc chắn muốn xóa sản phẩm này?</p>
      <div class="confirm-buttons">
        <button id="confirmDelete" class="btn-confirm">Xóa</button>
        <button onclick="closeModal()" class="btn-cancel">Hủy</button>
      </div>
    </div>
  </div>

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
<?php 

include 'includes/footer.php';
?>