<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$mysqli = new mysqli("localhost", "root", "", "ecobazar");

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin sản phẩm cần sửa
$productStmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$productStmt->bind_param("i", $product_id);
$productStmt->execute();
$product = $productStmt->get_result()->fetch_assoc();

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
    
    // Kiểm tra xem có upload ảnh mới không
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];
        $targetPath = $uploadDir . basename($image);
        
        // Xóa ảnh cũ nếu có
        if ($product['image'] && file_exists($uploadDir . $product['image'])) {
            unlink($uploadDir . $product['image']);
        }
        
        move_uploaded_file($tmpName, $targetPath);
    } else {
        // Giữ nguyên ảnh cũ
        $image = $product['image'];
    }

    // Cập nhật thông tin sản phẩm
    $stmt = $mysqli->prepare("UPDATE products SET category_id=?, name=?, price=?, old_price=?, stock=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("isddissi", $category_id, $name, $price, $old_price, $stock, $description, $image, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật sản phẩm thành công!'); window.location.href='product.php';</script>";
    } else {
        echo "Lỗi: " . $stmt->error;
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
    label { display: block; margin-top: 12px;font-weight: bold; }
    input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    .btn-submit, .btn-cancel {
      padding: 10px 20px; border: none; border-radius: 5px; margin-top: 20px; cursor: pointer;
    }
    .btn-submit { background-color: #e67817; color: white; }
    .main-content-add { padding: 40px; flex: 1;}
    .btn-cancel { background-color: #ddd; text-decoration: none; color: black; margin-left: 10px; }
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
 <div class="breadcrumb">Categories &gt; Create</div>
  <h1>Edit Product</h1>
  <form action="" method="POST" enctype="multipart/form-data">

    <label>Category</label>
    <select name="category_id" required>
      <option value="">-- Select Category --</option>
      <?php while ($row = $categoryResult->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>" <?= ($product['category_id'] == $row['id']) ? 'selected' : '' ?>>
          <?= $row['id'] . ' - ' . $row['name'] ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label>Price</label>
    <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required>

    <label>Old Price</label>
    <input type="number" name="old_price" step="0.01" value="<?= $product['old_price'] ?>" required>

    <label>Stock</label>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required>

    <label>Description</label>
    <textarea name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>

    <label>Image</label>
    <?php if ($product['image']): ?>
      <div style="margin: 10px 0;">
        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="Current image" style="max-width: 200px;">
        <p>Current image: <?= htmlspecialchars($product['image']) ?></p>
      </div>
    <?php endif; ?>
    <input type="file" name="image" accept="image/*">
    <small>(Để trống nếu không muốn thay đổi ảnh)</small>

    <button type="submit" class="btn-submit">Update</button>
    <a href="product.php" class="btn-cancel">Cancel</a>
  </form>
</main>
</div>
</body>
</html>
