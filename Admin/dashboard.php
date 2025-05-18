<?php
include 'includes/header.php';

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


    <main class="main-content">
      <h1>
        <img src="assets/Group.png" alt="Dashboard Icon" style="vertical-align: middle; margin-right: 10px; height: 1em;">
        Dashboard
      </h1>
      <p class="welcome-message">Chào mừng bạn đến với trang quản lý Ecobazar</p>

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

<?php include 'includes/footer.php'; ?>
