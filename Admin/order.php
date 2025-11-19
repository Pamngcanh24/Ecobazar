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
// TÌM KIẾM ĐƠN HÀNG
$search = trim($_GET['search'] ?? '');
$where = "";

if ($search !== '') {
    $searchEsc = $conn->real_escape_string($search);
    $where = "WHERE o.order_code LIKE '%$searchEsc%' 
           OR o.billing_name LIKE '%$searchEsc%' 
           OR o.billing_phone LIKE '%$searchEsc%' 
           OR o.billing_email LIKE '%$searchEsc%'";
}

// Đếm tổng số có điều kiện tìm kiếm
$countSql = "SELECT COUNT(*) AS total FROM orders o $where";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Lấy dữ liệu đơn hàng có phân trang
$sql = "SELECT o.*, 
               u.email AS user_email,
               d.name AS driver_name,
               d.phone AS driver_phone
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN drivers d ON o.driver_id = d.id
        $where
        ORDER BY o.order_date DESC 
        LIMIT $start, $limit";

$result = $conn->query($sql);

// Đếm tổng số dòng để phân trang (có search)
$countSql = "SELECT COUNT(*) AS total FROM orders o $where";
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
.header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 28px;
    position: relative;
}

.header-row h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

/* Div bọc để căn giữa thanh tìm kiếm */
.search-center-wrapper {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

/* Form tìm kiếm */
.order-search-form {
    margin: 0;
}

.search-box {
    position: relative;
    width: 100%;
   max-width: 320px;  
}

.search-box input {
    width: 100%;
    height: 42px;                    /* nhỏ hơn */
    padding: 0 42px 0 42px;          /* đủ chỗ cho icon + nút X */
    border: 1.8px solid #e2e8f0;
    border-radius: 12px;
    font-size: 14px;                 /* chữ nhỏ hơn, gọn gàng */
    background: #ffffff;
    transition: all 0.3s ease;
    outline: none;
    box-shadow: 0 3px 10px rgba(0,0,0,0.06);
}

.search-box input::placeholder {
    color: #94a3b8;
    font-size: 14px;
}

.search-box input:focus {
   border-color: #4361ee;
    box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
}

/* Icon tìm kiếm */
.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #64748b;
    pointer-events: none;
}

/* Nút X xóa – đẹp, không đè chữ */
.search-clear {
    position: absolute;
    right: -50px;
    top: 50%;
    transform: translateY(-50%);
    width: 26px;
    height: 26px;
    background: #fee2e2;
    color: #ef4444;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    text-decoration: none;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease;
}

.search-clear:hover {
    background: #fca5a5;
    transform: translateY(-50%) scale(1.1);
}

.search-box input:not(:placeholder-shown) ~ .search-clear,
.search-box input:focus ~ .search-clear {
    opacity: 1;
    visibility: visible;
}

</style>

    <main class="main-content">
     <div class="header-row">
    <h2>Orders Management</h2>

    <!-- CĂN GIỮA THANH TÌM KIẾM -->
    <div class="search-center-wrapper">
        <form method="GET" action="order.php" class="order-search-form">
            <div class="search-box">
                <input type="text" 
                       name="search" 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                       placeholder="Tìm mã đơn, tên hoặc SĐT khách..." 
                       autocomplete="off">
                <i class="fas fa-search search-icon"></i>
                <?php if (!empty($_GET['search'])): ?>
                    <a href="order.php" class="search-clear" title="Xóa tìm kiếm">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
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
            <th>Driver</th>
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
                <td>
                  <?php if (!empty($row['driver_name'])): ?>
                    <?php echo htmlspecialchars($row['driver_name']); ?><br>
                    <span style="color:#666;"><?php echo htmlspecialchars($row['driver_phone']); ?></span>
                  <?php else: ?>
                    <span style="color:#999;">No driver</span>
                  <?php endif; ?>
                </td>
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