<?php
include 'includes/header.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit;
}

$driver_id = $_SESSION['driver_id'];

// ==================== XỬ LÝ NHẬN ĐƠN SAU KHI XÁC NHẬN POPUP ====================
if (isset($_POST['confirm_accept'])) {
    $order_id = intval($_POST['confirm_accept']);

    $check = $conn->prepare("SELECT status FROM orders WHERE id = ? AND driver_id = ?");
    $check->bind_param("is", $order_id, $driver_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (in_array($row['status'], ['pending', 'confirmed'])) {
            $update = $conn->prepare("UPDATE orders SET status = 'processing', accepted_at = NOW() WHERE id = ?");
            $update->bind_param("i", $order_id);
            $update->execute();
            $update->close();
            $_SESSION['success_message'] = "Nhận đơn thành công!";
        } else {
            $_SESSION['error_message'] = "Đơn hàng đã được nhận hoặc không còn khả dụng!";
        }
    } else {
        $_SESSION['error_message'] = "Không tìm thấy đơn hàng!";
    }
    $check->close();
    header("Location: order.php");
    exit;
}

// ==================== LẤY DANH SÁCH ĐƠN CHỜ NHẬN ====================
$limit = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

$sql = "SELECT o.*, COALESCE(o.order_date, o.created_at) AS display_date 
        FROM orders o 
        WHERE o.driver_id = ? 
          AND o.status IN ('pending', 'confirmed')
        ORDER BY display_date DESC 
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $driver_id, $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

$countSql = "SELECT COUNT(*) AS total FROM orders WHERE driver_id = ? AND status IN ('pending', 'confirmed')";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("s", $driver_id);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$countStmt->close();
?>

<style>
  .customer-phone { font-size: 0.9em; color: #666; }

  /* POPUP XÁC NHẬN NHẬN ĐƠN – ĐẸP & KHÔNG LỖI */
  #confirmModal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(5px);
  }
  #confirmModal.show { display: flex; }

  .modal-content {
      background: #fff;
      width: 90%;
      max-width: 420px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(0,0,0,0.3);
      text-align: center;
      animation: pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }
  @keyframes pop {
      from { transform: scale(0.7); opacity: 0; }
      to   { transform: scale(1); opacity: 1; }
  }

  .modal-icon {
      width: 90px; height: 90px;
      margin: 30px auto 15px;
      background: #ecfdf5;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
  }
  .modal-icon i { font-size: 44px; color: #22c55e; }

  .modal-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0 0 12px; }
  .modal-desc { color: #64748b; font-size: 15px; padding: 0 30px; line-height: 1.5; margin-bottom: 30px; }

  .modal-buttons {
      display: flex;
      gap: 12px;
      padding: 0 24px 32px;
  }
  .modal-buttons button {
      flex: 1;
      padding: 14px;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      transition: 0.3s;
  }
  .btn-cancel { background: #e2e8f0; color: #475569; }
  .btn-confirm { background: #22c55e; color: white; }
  .btn-confirm:hover { background: #16a34a; transform: translateY(-3px); }
</style>

<main class="main-content">
  <div class="header-row">
    <h2>Đơn hàng mới – Chờ nhận đơn</h2>
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
      </tr>
    </thead>
    <tbody>
  <?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr onclick="window.location='dr_order_detail.php?id=<?= $row['id'] ?>';" style="cursor:pointer;">
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['order_code']) ?></td>
        <td>
          <div class="customer-info">
            <div class="customer-name"><?= htmlspecialchars($row['billing_name']) ?></div>
            <div class="customer-phone"><?= htmlspecialchars($row['billing_phone']) ?></div>
            <div class="customer-email"><?= htmlspecialchars($row['billing_email']) ?></div>
            <div class="customer-address"><?= htmlspecialchars($row['shipping_address']) ?></div>
          </div>
        </td>
        <td><?= date('d M Y', strtotime($row['display_date'])) ?></td>
        <td>$<?= number_format($row['total'], 2) ?></td>
        <td>
          <span class="status-badge <?= strtolower($row['status']) ?>">
            <?= $row['status'] == 'pending' ? 'Chờ nhận' : 'Pending' ?>
          </span>
        </td>
        <td><?= ucfirst($row['payment_method']) ?></td>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr>
      <td colspan="8" style="text-align:center;padding:70px 20px;color:#94a3b8;">
        Không có đơn hàng mới nào đang chờ nhận
      </td>
    </tr>
  <?php endif; ?>
</tbody>
  </table>

  <!-- PHÂN TRANG GIỮ NGUYÊN -->
  <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" class="page-item"><i class="fa-solid fa-angle-left"></i></a>
    <?php else: ?>
        <span class="page-item disabled"><i class="fa-solid fa-angle-left"></i></span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
            <span class="page-item active"><?= $i ?></span>
        <?php else: ?>
            <a href="?page=<?= $i ?>" class="page-item"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>" class="page-item"><i class="fa-solid fa-angle-right"></i></a>
    <?php else: ?>
        <span class="page-item disabled"><i class="fa-solid fa-angle-right"></i></span>
    <?php endif; ?>
  </div>

  <div class="table-footer">
    <div>Showing <?= $totalRows == 0 ? 0 : $start + 1 ?> to <?= min($start + $limit, $totalRows) ?> of <?= $totalRows ?> results</div>
  </div>
</main>

<!-- POPUP XÁC NHẬN -->
<div id="confirmModal">
  <div class="modal-content">
    <div class="modal-icon">
      <i class="fas fa-truck"></i>
    </div>
    <h3 class="modal-title">Xác nhận nhận đơn</h3>
    <p class="modal-desc">
      Bạn có chắc chắn muốn nhận đơn hàng<br>
      <strong style="color:#22c55e;" id="modalOrderCode"></strong> không?
    </p>
    <div class="modal-buttons">
      <button type="button" class="btn-cancel" onclick="closeModal()">Hủy bỏ</button>
      <form method="POST" style="margin:0;flex:1;">
        <input type="hidden" name="confirm_accept" id="confirmInput">
        <button type="submit" class="btn-confirm">Xác nhận</button>
      </form>
    </div>
  </div>
</div>

<script>
function openConfirmModal(orderId, orderCode) {
    document.getElementById('modalOrderCode').textContent = orderCode;
    document.getElementById('confirmInput').value = orderId;
    document.getElementById('confirmModal').classList.add('show');
}

function closeModal() {
    document.getElementById('confirmModal').classList.remove('show');
}

// Đóng popup khi click ngoài
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php include 'includes/footer.php'; ?>