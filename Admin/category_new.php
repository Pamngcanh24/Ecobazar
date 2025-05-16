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

// Xử lý khi người dùng gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $image = $_FILES['image']['name'];
  
  // Tạo thư mục uploads nếu chưa tồn tại
  $upload_dir = "uploads";
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
  }
  
  $target = $upload_dir . "/" . basename($image);

  // Di chuyển file ảnh lên thư mục uploads 
  if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
    $sql = "INSERT INTO categories (name, image) VALUES ('$name', '$image')";
    if ($conn->query($sql) === TRUE) {
      echo "<script>alert('Thêm danh mục thành công!'); window.location.href='category.php';</script>";
    } else {
      echo "Lỗi: " . $conn->error;
    }
  } else {
    echo "Lỗi khi tải ảnh lên.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Category</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    h1 {
      margin: 10px 0 30px;
    }
    form label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input[type="text"],input[type="file"] {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid #ccc;
      border-radius: 8px;
      transition: 0.3s ease;
      outline: none;
      font-size: 16px;
    }

    input[type="text"]:focus,input[type="file"]:focus {
      border-color: #00b207;
      box-shadow: 0 0 5px rgba(20, 144, 86, 0.6);
      background-color: #fff;
    }

    .current {
      color: #00b207;
      font-weight: bold;
    }
  </style>  
</head>
  <body>
  <div class="dashboard-container">
  <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="active"><i class="fas fa-th-large"></i> Categories</li>
        <li><a href="product.php"><i class="fas fa-box-open"></i> Products</a></li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="order.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
      </ul>
    </aside>

  <main class="main-content-add">
    <nav class="breadcrumb">
      <a href="/categories">Categories</a>
      <span class="separator">›</span>
      <span class="current">Create</span>
    </nav>

<h1>Create Category</h1>
    <form method="POST" enctype="multipart/form-data">
      <label for="name">Name <span style="color: red">*</span></label>
      <input type="text" id="name" name="name" required>

      <label for="image">Image</label>
      <input type="file" id="image" name="image">

      <div class="form-actions">
        <button type="submit" class="btn-create">Create</button>
        <button type="submit" class="btn-another">Create & create another</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='category.php'">Cancel</button>
      </div>
    </form>
  </main>
  </div>
</body>
</html>
