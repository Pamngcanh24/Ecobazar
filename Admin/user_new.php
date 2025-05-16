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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // mã hóa password
  $phone = $_POST['phone'];

  $created_at = date('Y-m-d H:i:s');
  $remember_token = bin2hex(random_bytes(16));
  $token_expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

  $stmt = $conn->prepare("INSERT INTO users (email, password, phone, created_at, remember_token, token_expiry) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $email, $password, $phone, $created_at, $remember_token, $token_expiry);
  if ($stmt->execute()) {
    $message = "Thêm người dùng thành công!";
  } else {
    $message = "Lỗi: " . $stmt->error;
  }
  $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New User</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .form-container { max-width: 600px; margin: auto; }
    h2 {margin-bottom: 20px; }
    label { display: block; margin-top: 12px;font-weight: bold; }
    input { padding: 8px;width: 100%; margin-top: 5px;border: 1px solid #ccc;border-radius: 4px; }
    .message {margin-top: 20px;color: green;}
    .main-content-add { padding: 40px; flex: 1;}
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
<div class="breadcrumb">Categories &gt; Create</div>
    <h2>Create New User</h2>

    <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

    <form action="" method="POST">
      <label for="email">Email *</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Password *</label>
        <div class="password-wrapper">
            <input type="password" id="password" name="password" required>
            <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
        </div>

      <label for="phone">Phone Numbers *</label>
      <input type="text" id="phone" name="phone" required>

      <div class="form-actions">
        <button type="submit" class="btn-create">Create</button>
        <button type="submit" class="btn-another">Create & create another</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='user.php'">Cancel</button>
    </div>
    </form>
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

