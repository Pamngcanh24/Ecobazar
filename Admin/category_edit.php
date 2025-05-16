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

// Lấy ID danh mục từ URL
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin danh mục cần sửa
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

// Xử lý khi người dùng gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  
  // Kiểm tra xem có upload ảnh mới không
  if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image']['name'];
    
    // Tạo thư mục uploads nếu chưa tồn tại
    $upload_dir = "uploads";
    if (!file_exists($upload_dir)) {
      mkdir($upload_dir, 0777, true);
    }
    
    $target = $upload_dir . "/" . basename($image);

    // Xóa ảnh cũ nếu có
    if ($category['image'] && file_exists($upload_dir . "/" . $category['image'])) {
      unlink($upload_dir . "/" . $category['image']);
    }

    // Upload ảnh mới
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
      $sql = "UPDATE categories SET name = ?, image = ? WHERE id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssi", $name, $image, $category_id);
    } else {
      echo "Lỗi khi tải ảnh lên.";
      exit;
    }
  } else {
    // Không có ảnh mới, chỉ cập nhật tên
    $sql = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $category_id);
  }

  if ($stmt->execute()) {
    echo "<script>alert('Cập nhật danh mục thành công!'); window.location.href='category.php';</script>";
  } else {
    echo "Lỗi: " . $stmt->error;
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
    label { display: block; margin-top: 12px;font-weight: bold; }
    form { max-width: 600px; }
    input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    .form-actions {margin-top: 20px;}
    .form-actions button {
      padding: 8px 15px;
      margin-right: 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn-create {
      background-color: #e78b27;
      color: white;
    }
    .btn-another, .btn-cancel {
      background-color: #f1f1f1;
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
      </ul>
    </aside>

  <main class="main-content-add">
    <div class="breadcrumb">Categories &gt; Edit</div>
    <h1>Edit Category</h1>
    <form method="POST" enctype="multipart/form-data">
      <label for="name">Name <span style="color: red">*</span></label>
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>

      <label for="image">Image</label>
      <?php if ($category['image']): ?>
        <div style="margin: 10px 0;">
          <img src="uploads/<?php echo htmlspecialchars($category['image']); ?>" alt="Current image" style="max-width: 200px;">
          <p>Current image: <?php echo htmlspecialchars($category['image']); ?></p>
        </div>
      <?php endif; ?>
      <input type="file" id="image" name="image">
      <small>(Để trống nếu không muốn thay đổi ảnh)</small>

      <div class="form-actions">
        <button type="submit" class="btn-create">Update</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='category.php'">Cancel</button>
      </div>
    </form>
  </main>
  </div>
</body>
</html>
