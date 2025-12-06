<?php
include 'includes/header.php';

// Lấy ID đơn hàng từ URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin đơn hàng cần sửa
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
// Lưu lại trạng thái và tài xế hiện tại để đối chiếu sau khi cập nhật
$old_status = isset($order['status']) ? strtolower($order['status']) : null;
$order_driver_id = isset($order['driver_id']) ? $order['driver_id'] : null;

$drivers = [];
$drRes = $conn->query("SELECT id, name, phone FROM drivers ORDER BY name ASC");
if ($drRes) {
  while ($dr = $drRes->fetch_assoc()) { $drivers[] = $dr; }
}

// Xử lý khi người dùng gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $shipping_name = $_POST['shipping_name'];
  $shipping_phone = $_POST['shipping_phone'];
  $shipping_address = $_POST['shipping_address'];
  $status = $_POST['status'];
  $payment_method = $_POST['payment_method'];

  // Cập nhật thông tin đơn hàng
  $sql = "UPDATE orders SET 
          shipping_name = ?,
          shipping_phone = ?,
          shipping_address = ?,
          status = ?,
          payment_method = ?
          WHERE id = ?";
          
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssi", 
    $shipping_name,
    $shipping_phone,
    $shipping_address,
    $status,
    $payment_method,
    $order_id
  );

  if ($stmt->execute()) {
    // Sau khi cập nhật trạng thái, điều chỉnh current_orders của tài xế (nếu có)
    $new_status = strtolower($status);
    $active_statuses = ['pending', 'processing'];
    $inactive_statuses = ['completed', 'cancelled'];

    if (!empty($order_driver_id)) {
      // Trường hợp chuyển từ trạng thái hoạt động sang không hoạt động: giảm 1
      if (in_array($old_status, $active_statuses, true) && in_array($new_status, $inactive_statuses, true)) {
        $dec = $conn->prepare("UPDATE drivers SET current_orders = GREATEST(current_orders - 1, 0) WHERE id = ?");
        $dec->bind_param("s", $order_driver_id);
        $dec->execute();
        $dec->close();
      }
      // Trường hợp chuyển từ không hoạt động sang hoạt động: tăng 1
      if (in_array($old_status, $inactive_statuses, true) && in_array($new_status, $active_statuses, true)) {
        $inc = $conn->prepare("UPDATE drivers SET current_orders = current_orders + 1 WHERE id = ?");
        $inc->bind_param("s", $order_driver_id);
        $inc->execute();
        $inc->close();
      }
    }

    $new_driver_id = (isset($_POST['driver_id']) && $_POST['driver_id'] !== '') ? $_POST['driver_id'] : null;
    if ($new_status === 'pending' && $new_driver_id !== $order_driver_id) {
      if ($new_driver_id === null) {
        $ud = $conn->prepare("UPDATE orders SET driver_id = NULL WHERE id = ?");
        $ud->bind_param("i", $order_id);
        $ud->execute();
        $ud->close();
      } else {
        $ud = $conn->prepare("UPDATE orders SET driver_id = ? WHERE id = ?");
        $ud->bind_param("si", $new_driver_id, $order_id);
        $ud->execute();
        $ud->close();
      }
      if (!empty($order_driver_id)) {
        $dec2 = $conn->prepare("UPDATE drivers SET current_orders = GREATEST(current_orders - 1, 0) WHERE id = ?");
        $dec2->bind_param("s", $order_driver_id);
        $dec2->execute();
        $dec2->close();
      }
      if (!empty($new_driver_id)) {
        $inc2 = $conn->prepare("UPDATE drivers SET current_orders = current_orders + 1 WHERE id = ?");
        $inc2->bind_param("s", $new_driver_id);
        $inc2->execute();
        $inc2->close();
      }
    }

    echo "<script>alert('Cập nhật đơn hàng thành công!'); window.location.href='order.php';</script>";
  } else {
    echo "Lỗi: " . $stmt->error;
  }
// lấy ngày hiện tại dạng YYYYMMDD
$today = date('Ymd');

// đếm số đơn trong ngày
$sqlCount = "SELECT COUNT(*) AS total FROM orders WHERE DATE(order_date) = CURDATE()";
$resCount = mysqli_query($conn, $sqlCount);
$rowCount = mysqli_fetch_assoc($resCount);

$orderNumber = $rowCount['total'] + 1; // +1 cho đơn mới
$orderCode = $today . '-' . str_pad($orderNumber, 4, '0', STR_PAD_LEFT);

}

?>
 <style>
    h1 { 
      margin: -40px 0 20px;
      color:rgb(42, 140, 45);
    }
    form { max-width: 600px; }
    label {
      display: block;   
      margin-top: 15px;
      margin-bottom: 12px;
      font-weight: bold; 
      }
    input, select, textarea { 
      width: 100%; 
      padding: 8px; 
      margin-top: 10px; 
      margin-bottom: 10px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-sizing: border-box; /* Thêm dòng này */ 
    }
    .btn-submit, .btn-cancel {
      padding: 10px 20px; border: none; border-radius: 5px; margin-top: 20px; cursor: pointer;
    }
    .btn-submit { background-color: #00b207; color: white; }
    .main-content-add { padding: 40px; flex: 1;}
    .btn-cancel { background-color: #ddd; text-decoration: none; color: black; margin-left: 10px; }
  </style>
    <main class="main-content-add">
    <nav class="breadcrumb">
      <a href="order.php">Orders</a>
      <span class="separator">›</span>
      <span class="current">Edit</span>
    </nav>      
    <h1>Edit Order <?php echo $order_id; ?></h1>
    
      <form method="POST">
        <h2>Shipping Information</h2>
        <div class="form-group">
          <label for="shipping_name">Name <span style="color: red">*</span></label>
          <input type="text" id="shipping_name" name="shipping_name" value="<?php echo htmlspecialchars($order['shipping_name']); ?>" disabled>
        </div>
      <div class="form-group">
          <label for="order_code">Order Code</label>
          <input type="text" id="order_code" value="<?php echo htmlspecialchars($order['order_code']); ?>" disabled>
      </div>

      <div class="form-group">
          <label for="shipping_phone">Phone <span style="color: red">*</span></label>
          <input type="text" id="shipping_phone" name="shipping_phone" value="<?php echo htmlspecialchars($order['shipping_phone']); ?>" disabled>
        </div>

        <div class="form-group">
          <label for="shipping_address">Address <span style="color: red">*</span></label>
          <textarea id="shipping_address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($order['shipping_address']); ?></textarea>
        </div>

        <div class="form-group">
          <label for="driver_id">Driver</label>
          <select id="driver_id" name="driver_id" <?php echo strtolower($order['status']) !== 'pending' ? 'disabled' : ''; ?>>
            <option value="">No driver</option>
            <?php foreach ($drivers as $d): ?>
              <option value="<?php echo htmlspecialchars($d['id']); ?>" <?php echo ($order['driver_id'] === $d['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($d['name']) . ' (' . htmlspecialchars($d['phone']) . ')'; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (strtolower($order['status']) !== 'pending'): ?>
            <small>Chỉ sửa khi trạng thái Pending</small>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="status">Status <span style="color: red">*</span></label>
          <select id="status" name="status" required>
            <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
            <option value="Completed" <?php echo $order['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
          </select>
        </div>

        <div class="form-group">
          <label for="payment_method">Payment Method <span style="color: red">*</span></label>
          <select id="payment_method" name="payment_method" required>
            <option value="cod" <?php echo $order['payment_method'] == 'cod' ? 'selected' : ''; ?>>Cash on Delivery</option>
            <option value="bank_transfer" <?php echo $order['payment_method'] == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
          </select>
        </div>

        <div class="form-actions">
        <button type="submit" class="btn-create">Update</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='order.php'">Cancel</button>
      </div>
      </form>
    </main>
<?php 
include 'includes/footer.php';
?>