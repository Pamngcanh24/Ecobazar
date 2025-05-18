<?php
include 'includes/header.php';

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

  <style>
    h1 {
      margin: -40px 0 20px;
      color:rgb(42, 140, 45);
    }
    label { 
      display: block;   
      margin-top: 15px;
      margin-bottom: 12px;
      font-weight: bold; 
    }
    form { max-width: 600px; }
    input, select, textarea { 
      width: 100%; 
      padding: 8px; 
      margin-top: 10px; 
      margin-bottom: 10px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-sizing: border-box; /* Thêm dòng này */
    }
    .form-actions {margin-top: 20px;}
    .form-actions button {
      padding: 8px 15px;
      margin-right: 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn-create {
      background-color: #00b207;
      color: white;
    }
    .btn-another, .btn-cancel {
      background-color: #f1f1f1;
    }
  </style>  

  <main class="main-content-add">
    <nav class="breadcrumb">
      <a href="category.php">Categories</a>
      <span class="separator">›</span>
      <span class="current">Edit</span>
    </nav>
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

  <?php
  include 'includes/footer.php';
  ?>