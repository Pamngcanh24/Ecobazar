<?php
include 'includes/check_login.php';

// Hiển thị thông báo nếu có
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

$uri = $_SERVER['REQUEST_URI'];
$uri_parts = explode('?', $uri);
$uri_path = $uri_parts[0];
$url_exploded = explode('/', $uri_path);
$menu_item = end($url_exploded);
$menu_name = explode('.', $menu_item)[0];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Driver Panel</title>
  <link rel="stylesheet" href="assets/style.css" />
  <link rel="icon" href="assets/plantlogo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
      .alert {
          position: fixed;
          top: 20px;
          right: 20px;
          padding: 15px 30px;
          border-radius: 8px;
          font-family: 'Poppins', sans-serif;
          font-size: 15px;
          display: flex;
          align-items: center;
          gap: 10px;
          animation: slideIn 0.5s ease-out forwards, fadeOut 0.5s ease-out 2.5s forwards;
          z-index: 1000;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }
  
      .alert-success {
          background-color: #00b207;
          color: white;
      }
  
      .alert-error {
          background-color: #dc3545;
          color: white;
      }
  
      @keyframes slideIn {
          from {
              transform: translateX(100%);
              opacity: 0;
          }
          to {
              transform: translateX(0);
              opacity: 1;
          }
      }
  
      @keyframes fadeOut {
          from {
              transform: translateX(0);
              opacity: 1;
          }
          to {
              transform: translateX(100%);
              opacity: 0;
          }
      }
  </style>
</head>
<body>
    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo $success_message; ?>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>

    <div class="dashboard-container">
    <aside class="sidebar">
      <ul>
        <?php 
        $menus = [
            'dashboard' => [
                'icon' => 'fas fa-home',
                'name' => 'Dashboard'
            ],
            'order' => [
                'icon' => 'fas fa-shopping-cart',
                'name' => 'New Orders'
            ],
             'order_today' => [
                'icon' => 'fas fa-calendar-day',
                'name' => 'Today Orders'
            ],
            'delivery_history' => [
                'icon' => 'fas fa-history',
                'name' => 'Delivery history'
            ],
             'account_driver' => [
                'icon' => 'fas fa-user',
                'name' => 'Account'
            ],
        ];
        
        foreach ($menus as $key => $menu) {
            $active = $menu_name === $key ? 'active' : '';
            echo "<li class='$active'>";
            echo "<a href='$key.php'>";
            echo "<i class='{$menu['icon']}'></i>";
            echo "<span>{$menu['name']}</span>";
            echo "</a>";
            echo "</li>";
        }
        ?>
      </ul>
    </aside>