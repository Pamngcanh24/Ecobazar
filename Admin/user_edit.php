<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$host = 'localhost';
$db = 'ecobazar';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy ID người dùng từ URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin người dùng cần sửa
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  
  // Kiểm tra xem có nhập mật khẩu mới không
  if (!empty($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sql = "UPDATE users SET email = ?, password = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $email, $password, $phone, $user_id);
  } else {
    // Không cập nhật mật khẩu
    $sql = "UPDATE users SET email = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $email, $phone, $user_id);
  }

  if ($stmt->execute()) {
    echo "<script>alert('Cập nhật người dùng thành công!'); window.location.href='user.php';</script>";
  } else {
    $message = "Lỗi: " . $stmt->error;
  }
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    h2 {margin-bottom: 20px; }
    label { display: block; margin-top: 12px;font-weight: bold; }
    input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    .actions {margin-top: 20px; }
    form { max-width: 600px; }
    button, .btn-cancel { padding: 10px 16px; margin-right: 10px;border: none; cursor: pointer;border-radius: 4px;}
    button {background-color: #00b207; color: white; }
    .btn-cancel { background-color: #f1f1f1;color: #444;text-decoration: none;border: 1px solid #ccc;}
    .message {margin-top: 20px;color: green;}
    .password-wrapper {position: relative;}
    .password-wrapper input { width: 100%;padding-right: 30px;}  
    .toggle-password { position: absolute;top: 50%;right: 10px;transform: translateY(-50%);cursor: pointer;color: #aaa;}
  </style>
</head>
<body>
<div class="dashboard-container">
  <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="category.php"><i class="fas fa-th-large"></i> Categories</a></li>
        <li><a href="product.php"><i class="fas fa-box-open"></i> Products</a></li>
        <li class="active"><i class="fas fa-users"></i> Users</li>
        <li><a href="order.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
      </ul>
    </aside>
<main class="main-content-add">
<div class="breadcrumb">Users &gt; Edit</div>
    <h2>Edit User</h2>

    <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

    <form action="" method="POST">
      <label for="email">Email *</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>

      <label for="password">Mật khẩu</label>
      <div class="password-wrapper">
          <input type="password" id="password" name="password">
          <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
      </div>
      <small>(Để trống nếu không muốn thay đổi mật khẩu)</small>

      <label for="phone">Số điện thoại *</label>
      <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required>

      <div class="form-actions">
        <button type="submit" class="btn-create">Update</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='user.php'">Cancel</button>
      </div>
    </form>
  </main>
</div>
  <script>
  function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.querySelector('.toggle-password');
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    } else {
      passwordInput.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    }
  }
</script>

</body>
</html>

