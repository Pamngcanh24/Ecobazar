<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <ul>
        <li class="active"><i class="fas fa-home"></i> Dashboard</li>
        <li><i class="fas fa-th-large"></i> Categories</li>
        <li><i class="fas fa-box-open"></i> Products</li>
        <li><i class="fas fa-users"></i> Users</li>
      </ul>
    </aside>

    <main class="main-content">
      <h1>Dashboard</h1>
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

        <!-- <div class="card filament-card">
          <p><strong><em>filament</em></strong><br><small>v3.0.0.0</small></p>
          <div class="links">
            <a href="#"><i class="fas fa-book"></i> Documentation</a>
            <a href="#"><i class="fab fa-github"></i> GitHub</a> -->
          </div>
        </div>
      </div>
    </main>
  </div>
<script>
  function confirmSignOut() {
    const confirmBox = document.createElement('div');
    confirmBox.className = 'confirm-box';
    confirmBox.innerHTML = `
      <div class="confirm-dialog">
        <p>Bạn có muốn đăng xuất không?</p>
        <div class="confirm-actions">
          <button onclick="window.location.href='login.php'">Có</button>
          <button onclick="document.body.removeChild(confirmBox)">Không</button>
        </div>
      </div>
    `;
    document.body.appendChild(confirmBox);
  }
</script>

</body>
</html>
