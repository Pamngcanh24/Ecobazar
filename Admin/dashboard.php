<?php
session_start();

// Kiểm tra nếu chưa đăng nhập thì chuyển về trang login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Lấy thông tin admin từ session
$admin_username = $_SESSION['admin_username'];

// Thêm xử lý đăng xuất
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Xóa tất cả session
    session_unset();
    session_destroy();
    
    // Xóa cookie remember me nếu có
    if (isset($_COOKIE['admin_remember_token'])) {
        setcookie('admin_remember_token', '', time() - 3600, '/');
    }
    
    // Chuyển hướng về trang login
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <ul>
        <li class="active"><i class="fas fa-home"></i> Dashboard</li>
        <li><a href="category.php"><i class="fas fa-th-large"></i> Categories</a></li>
        <li><a href="product.php"><i class="fas fa-box-open"></i> Products</a></li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
      </ul>
    </aside>

    <main class="main-content">
      <h1>
        <img src="assets/Group.png" alt="Dashboard Icon" style="vertical-align: middle; margin-right: 10px; height: 1em;">
        Dashboard
      </h1>
      <p class="welcome-message">Chào mừng bạn đến với trang quản lý</p>

      <div class="card-container">
        <div class="card">
          <div class="avatar">A</div>
          <div class="user-info">
            <p class="greeting">Welcome</p>
            <p class="username">admin24</p>
          </div>
          <button class="sign-out-btn" onclick="confirmSignOut()"> <i class="fas fa-sign-out-alt"></i> Sign out</button>
        </div>
      </div>
  </div>
</main>
<script>
  function confirmSignOut() {
    const confirmBox = document.createElement('div');
    confirmBox.className = 'confirm-box';
    confirmBox.innerHTML = `
      <div class="confirm-dialog">
        <p>Bạn có muốn đăng xuất không?</p>
        <div class="confirm-actions">
          <button onclick="window.location.href='dashboard.php?logout=1'">Có</button>
          <button onclick="this.closest('.confirm-box').remove()">Không</button>
        </div>
      </div>
    `;
    document.body.appendChild(confirmBox);
  }
</script>

</body>
</html>
