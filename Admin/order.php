<?php
include 'includes/header.php';

// Hàm tạo order code
function generateOrderCode() {
    global $conn;
    $today = date('Ymd'); // Format: YYYYMMDD
    
    // Đếm số đơn trong ngày
    $sql = "SELECT COUNT(*) as count FROM orders WHERE DATE(order_date) = CURDATE()";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $orderNumber = $row['count'] + 1;
    
    // Format: ODyyyymmdd-XX (XX là số thứ tự đơn trong ngày)
    return 'OD' . date('Ymd') . '-' . str_pad($orderNumber, 2, '0', STR_PAD_LEFT);
}

// Xử lý xóa đơn hàng
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Xóa đơn hàng
    $delete_sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Xóa đơn hàng thành công!";
        header("Location: order.php");
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi xóa đơn hàng!";
        header("Location: order.php");
    }
    $stmt->close();
    exit;
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
  .customer-phone {
          font-size: 0.9em;
          color: #666;
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

    <main class="main-content">
      <div class="header-row">
        <h2>Orders Management</h2>
      </div>

      <table class="category-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Order Code</th>
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
                <tr onclick="window.location='order_detail.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer;">
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['order_code']); ?></td>
                <td>
                  <div class="customer-info">
                    <div class="customer-name"><?php echo htmlspecialchars($row['billing_name']); ?></div>
                    <div class="customer-phone"><?php echo htmlspecialchars($row['billing_phone']); ?></div>
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
                  <a href="#" 
                     onclick="showConfirmModal('order.php?delete_id=<?php echo $row['id']; ?>&page=<?php echo $page; ?>'); return false;"
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
  

    <!-- Modal xác nhận xóa -->
    <div id="confirmModal" class="confirm-modal">
        <div class="confirm-content">
            <h3>Xác nhận xóa</h3>
            <p>Bạn có chắc chắn muốn xóa đơn hàng này?</p>
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
<?php
include 'includes/footer.php';
?>