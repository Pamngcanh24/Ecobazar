<?php
include 'includes/header.php';

// Lấy ID đơn hàng từ URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin đơn hàng cần sửa
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

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
    echo "<script>alert('Cập nhật đơn hàng thành công!'); window.location.href='order.php';</script>";
  } else {
    echo "Lỗi: " . $stmt->error;
  }
}
?>

    <main class="main-content-add">
    <nav class="breadcrumb">
      <a href="order.php">Orders</a>
      <span class="separator">›</span>
      <span class="current">Edit</span>
    </nav>      
    <h1>Edit Order #<?php echo $order_id; ?></h1>
      
      <form method="POST">
        <h2>Shipping Information</h2>
        <div class="form-group">
          <label for="shipping_name">Name <span style="color: red">*</span></label>
          <input type="text" id="shipping_name" name="shipping_name" value="<?php echo htmlspecialchars($order['shipping_name']); ?>" required>
        </div>

        <div class="form-group">
          <label for="shipping_phone">Phone <span style="color: red">*</span></label>
          <input type="text" id="shipping_phone" name="shipping_phone" value="<?php echo htmlspecialchars($order['shipping_phone']); ?>" required>
        </div>

        <div class="form-group">
          <label for="shipping_address">Address <span style="color: red">*</span></label>
          <textarea id="shipping_address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($order['shipping_address']); ?></textarea>
        </div>

        <div class="form-group">
          <label for="status">Status <span style="color: red">*</span></label>
          <select id="status" name="status" required>
            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
            <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
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