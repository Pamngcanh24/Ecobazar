<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$mysqli = new mysqli("localhost", "root", "", "ecobazar");

// Lấy danh sách category
$categoryResult = $mysqli->query("SELECT id, name FROM categories");

// Tạo thư mục uploads nếu chưa có
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $old_price = $_POST['old_price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $image = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];
    $targetPath = $uploadDir . basename($image);

    if (move_uploaded_file($tmpName, $targetPath)) {
        $stmt = $mysqli->prepare("INSERT INTO products (category_id, name, price, old_price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddiss", $category_id, $name, $price, $old_price, $stock, $description, $image);

        if ($stmt->execute()) {
            echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href='product.php';</script>";
        } else {
            echo "Lỗi: " . $stmt->error;
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
  <title>Create Product</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    h1 { font-size: 24px; margin-bottom: 20px; }
    form { max-width: 600px; }
    label { display: block; margin-top: 15px; font-weight: bold; }
    input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    
  </style>
</head>
<body>
<div class="dashboard-container">
  <aside class="sidebar">
      <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="category.php"><i class="fas fa-th-large"></i> Categories</a></li>
        <li class="active"><i class="fas fa-box-open"></i> Products</li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
      </ul>
    </aside>
<main class="main-content-add">
<div class="breadcrumb">Product &gt; Create</div>
  <h1>Create Product</h1>
  <form action="" method="POST" enctype="multipart/form-data">

    <label>Category</label>
    <select name="category_id" required>
      <option value="">-- Select Category --</option>
      <?php while ($row = $categoryResult->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>"><?= $row['id'] . ' - ' . $row['name'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Name</label>
    <input type="text" name="name" required>

    <label>Price</label>
    <input type="number" name="price" step="0.01" required>

    <label>Old Price</label>
    <input type="number" name="old_price" step="0.01" required>

    <label>Stock</label>
    <input type="number" name="stock" required>

    <label>Description</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Image</label>
    <input type="file" name="image" accept="image/*" required>

    
    <div class="form-actions">
        <button type="submit" class="btn-create">Create</button>
        <button type="submit" class="btn-another">Create & create another</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='product.php'">Cancel</button>
    </div>
  </form>
</main>
</div>
</body>
</html>
