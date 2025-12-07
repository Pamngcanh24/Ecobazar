<?php
include 'includes/header.php';
if (!isset($_SESSION['driver_id'])) { header("Location: login.php"); exit; }
$driver_id = $_SESSION['driver_id']; // Chuỗi

$limit = 6;
$page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;
$today = date('Y-m-d');

$sql = "SELECT o.*, COALESCE(o.order_date, o.created_at) AS display_date
        FROM orders o 
        WHERE o.driver_id = ? 
          AND o.status = 'processing'
          AND (DATE(o.created_at) = ? OR DATE(o.order_date) = ?)
        ORDER BY display_date ASC
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $driver_id, $today, $today, $start, $limit); // "sssii"
$stmt->execute();
$result = $stmt->get_result();

$countSql = "SELECT COUNT(*) AS total FROM orders 
             WHERE driver_id = ? AND status = 'processing' 
               AND (DATE(created_at) = ? OR DATE(order_date) = ?)";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("sss", $driver_id, $today, $today); // "sss"
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$countStmt->close();
?>


<main class="main-content">
  <div class="header-row">
    <h2>Đơn hàng hôm nay</h2>
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
        <th>Hành động</th>
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
            <td><?= date('d M Y', strtotime($row['display_date'])) ?><br>
                <small><?= date('H:i', strtotime($row['display_date'])) ?></small>
            </td>
            <td style="color:#22c55e;font-weight:600;">$<?= number_format($row['total'], 2) ?></td>
            <td>
              <span class="status-badge <?= $row['status'] == 'picked_up' ? 'picked' : 'processing' ?>">
                <?= $row['status'] == 'picked_up' ? 'Đã lấy hàng' : 'Đang xử lý' ?>
              </span>
            </td>
            <td><?= ucfirst($row['payment_method']) ?></td>
            <td onclick="event.stopPropagation();">
              <?php if($row['status'] === 'processing'): ?>
                <form method="POST" style="margin:0;">
                  <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="new_status" value="picked_up">
                  <button type="submit" name="update_status" 
                          style="background:#f59e0b;color:white;border:none;padding:6px 12px;border-radius:4px;font-size:13px;margin:2px 0;width:100%;"
                          onclick="return confirm('Xác nhận đã lấy hàng?')">
                    Đã lấy hàng
                  </button>
                </form>
              <?php endif; ?>
              <form method="POST" style="margin:0;">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <input type="hidden" name="new_status" value="delivered">
                <button type="submit" name="update_status" 
                        style="background:#22c55e;color:white;border:none;padding:6px 12px;border-radius:4px;font-size:13px;margin:2px 0;width:100%;"
                        onclick="return confirm('Xác nhận đã giao xong?')">
                  Đã giao xong
                </button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" style="text-align:center;padding:80px 20px;color:#94a3b8;">
            <i class="fas fa-inbox" style="font-size:50px;opacity:0.3;"></i><br><br>
            Chưa có đơn hàng nào hôm nay<br>
            <small>Nhận đơn mới từ trang <a href="order.php" style="color:#22c55e;text-decoration:underline;">Đơn hàng mới</a></small>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- PHÂN TRANG – GIỮ NGUYÊN 100% NHƯ FILE order.php -->
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

<?php include 'includes/footer.php'; ?>