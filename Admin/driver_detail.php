<?php
include 'includes/header.php';

$driver_id = isset($_GET['id']) ? $_GET['id'] : '';
$driver = null;
$orders = [];

if ($driver_id !== '') {
    if ($stmt = $conn->prepare("SELECT id, name, email, phone, address, citizen_id, bank_account, current_orders, created_at FROM drivers WHERE id = ?")) {
        $stmt->bind_param("s", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();
        $stmt->close();
    }

    if ($stmt2 = $conn->prepare("SELECT id, order_code, status, order_date, total FROM orders WHERE driver_id = ? ORDER BY order_date DESC LIMIT 100")) {
        $stmt2->bind_param("s", $driver_id);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        while ($row = $res2->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt2->close();
    }

    $current_orders_count = 0;
    if ($stmt3 = $conn->prepare("SELECT COUNT(*) AS cnt FROM orders WHERE driver_id = ? AND LOWER(status) IN ('pending','processing')")) {
        $stmt3->bind_param("s", $driver_id);
        $stmt3->execute();
        $res3 = $stmt3->get_result();
        $row3 = $res3->fetch_assoc();
        $current_orders_count = isset($row3['cnt']) ? (int)$row3['cnt'] : 0;
        $stmt3->close();
    }
}
?>
<style>
*{font-family:'Poppins',sans-serif;}
.main-content{padding:32px;background:#f5f7fa;min-height:100vh;}

.order-box{
    background:#fff;
    border-radius:14px;
    padding:30px;
    margin-bottom:28px;
    box-shadow:0 4px 18px rgba(0,0,0,.06);
    border:1px solid #e8e8e8;
}

.order-title{
    font-size:22px;
    font-weight:700;
    margin-bottom:22px;
    color:#111;
    border-left:4px solid #00b207;
    padding-left:12px;
}

.order-row{display:flex;gap:60px;flex-wrap:wrap;}
.order-col{flex:1;min-width:300px;}
.order-col h4{margin:0 0 14px;font-size:17px;font-weight:700;color:#00b207;letter-spacing:.3px;}
.order-col p{margin:7px 0;font-size:15px;color:#333;}
.order-col p b{color:#000;}

.table-items{width:100%;border-collapse:collapse;font-size:15px;}
.table-items th{background:#eef3ee;padding:12px;text-align:left;font-weight:600;border-bottom:1px solid #dcdcdc;}
.table-items td{padding:12px;border-bottom:1px solid #f0f0f0;text-align:left;}
.table-items tr:hover td{background:#f8faf8;}

.btn-back{display:inline-block;margin-top:14px;padding:10px 18px;background:#00b207;color:#fff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:600;transition:.2s;}
.btn-back:hover{opacity:.85;transform:translateY(-1px);}
</style>

<main class="main-content">
  <div class="order-box">
    <div class="order-title">Chi tiết tài xế <?php echo $driver ? htmlspecialchars($driver['id']) : ''; ?></div>
    <?php if (!$driver): ?>
      <p>Không tìm thấy tài xế với ID đã cung cấp.</p>
    <?php else: ?>
    <div class="order-row">
      <div class="order-col">
        <h4>Thông tin cá nhân</h4>
        <p><b>Tên:</b> <?php echo htmlspecialchars($driver['name']); ?></p>
        <p><b>Email:</b> <?php echo htmlspecialchars($driver['email']); ?></p>
        <p><b>SĐT:</b> <?php echo htmlspecialchars($driver['phone']); ?></p>
        <p><b>Địa chỉ:</b> <?php echo htmlspecialchars($driver['address']); ?></p>
        <p><b>Citizen ID:</b> <?php echo htmlspecialchars($driver['citizen_id']); ?></p>
      </div>
      <div class="order-col">
        <h4>Thông tin công việc</h4>
        <p><b>Tài khoản MB Bank:</b> <?php echo htmlspecialchars($driver['bank_account']); ?></p>
        <p><b>Đơn hiện tại:</b> <?php echo htmlspecialchars($current_orders_count); ?></p>
        <p><b>Ngày tạo:</b> <?php echo htmlspecialchars($driver['created_at']); ?></p>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="order-box">
    <div class="order-title">Đơn hàng đã gán cho tài xế</div>
    <?php if ($driver && count($orders) > 0): ?>
      <table class="table-items">
        <thead>
          <tr>
            <th>Mã đơn</th>
            <th>Trạng thái</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr onclick="window.location='order_detail.php?id=<?php echo urlencode($o['id']); ?>';" style="cursor:pointer;">
              <td><?php echo htmlspecialchars($o['order_code']); ?></td>
              <td><?php echo htmlspecialchars($o['status']); ?></td>
              <td><?php echo htmlspecialchars($o['order_date']); ?></td>
              <td><?php echo htmlspecialchars(number_format($o['total'], 2)); ?> $</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Chưa có đơn hàng nào được gán cho tài xế này.</p>
    <?php endif; ?>
    <a href="driver.php" class="btn-back">Quay lại</a>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

