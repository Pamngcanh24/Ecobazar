<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kết nối database
$conn = new mysqli("localhost", "root", "", "ecobazar");

// Kiểm tra kết nối
if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}

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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Order</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    h1 { 
      margin: -40px 0 20px;
      color:rgb(42, 140, 45);    
    }
    label { 
      display: block;   
      margin-top: 15px;
      margin-bottom: 12px;
      font-weight: bold; 
    }
    form { max-width: 600px; } /* Giảm max-width xuống */
    input, select, textarea { 
      width: 100%; 
      padding: 8px; 
      margin-top: 10px; 
      margin-bottom: 10px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-sizing: border-box; /* Thêm dòng này */
    }
    .form-group { margin-bottom: 20px; }
    .form-actions { margin-top: 20px; }
    .form-actions button {
      padding: 8px 15px;
      margin-right: 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="category.php"><i class="fas fa-th-large"></i> Categories</a></li>
        <li><a href="product.php"><i class="fas fa-box-open"></i> Products</a></li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
        <li class="active"><i class="fas fa-shopping-cart"></i> Orders</li>
      </ul>
    </aside>

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
  </div>
</body>
</html>