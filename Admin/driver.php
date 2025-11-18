<?php
include 'includes/header.php';

// Xử lý xóa user
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Kiểm tra user có tồn tại không
    $check_stmt = $conn->prepare("SELECT id FROM drivers WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Xóa user bằng prepared statement
        $delete_stmt = $conn->prepare("DELETE FROM drivers WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "Xóa người dùng thành công!";
            header("Location: driver.php");
        } else {
            $_SESSION['error_message'] = "Có lỗi xảy ra khi xóa người dùng!";
            header("Location: driver.php");
        }
        $delete_stmt->close();
    } else {
        $_SESSION['error_message'] = "Không tìm thấy người dùng!";
        header("Location: driver.php");
    }
    $check_stmt->close();
    exit;
}

// Phân trang
$limit = 8;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// Lấy dữ liệu user
$sql = "SELECT * FROM drivers ORDER BY id ASC LIMIT $start, $limit";
$result = $conn->query($sql);

// Tổng số user
$countSql = "SELECT COUNT(*) AS total FROM drivers";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>


    <main class="main-content">
      <div class="header-row">
        <h2>Drivers Management</h2>
        <a href="driver_new.php" class="btn-new-category">New driver</a>
      </div>

      <table class="category-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Bank Account (MBBANK)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr onclick="window.location='driver_detail.php?id=<?php echo urlencode($row['id']); ?>';" style="cursor: pointer;">
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['bank_account']); ?></td>
                <td>
                  <a href="#" 
                     onclick="event.stopPropagation(); showConfirmModal('driver.php?delete_id=<?php echo $row['id']; ?>&page=<?php echo $page; ?>'); return false;"
                     class="delete-link">
                    <i class="fas fa-trash-alt"></i> Delete
                  </a>
                  <a href="driver_edit.php?id=<?php echo $row['id']; ?>" class="edit-link" onclick="event.stopPropagation();">
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
        <div>Showing <?php echo min($start + 1, $totalRows); ?> to <?php echo min($start + $limit, $totalRows); ?> of <?php echo $totalRows; ?> drivers</div>
      </div>
    </main>


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
  /* Row hover */
  .category-table tbody tr:hover {
    background-color: #fff7e6;
  }
</style>

<!-- Thêm modal xác nhận xóa -->
<div id="confirmModal" class="confirm-modal">
  <div class="confirm-content">
    <h3>Xác nhận xóa</h3>
    <p>Bạn có chắc chắn muốn xóa người dùng này?</p>
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

<?php include 'includes/footer.php'; ?>