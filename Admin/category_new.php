<?php
include 'includes/header.php';


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
<style>
    h1 {
      margin: -40px 0 20px;
      color:rgb(42, 140, 45);
    }
    form { max-width: 600px; }
    form label {
      display: block;
      margin-top: 15px;
      margin-bottom: 12px;
      font-weight: bold;
    }
    input[type="text"],input[type="file"] {
      width: 100%; 
      padding: 8px; 
      margin-top: 5px; 
      border-radius: 5px; 
      border: 1px solid #ccc; 
      box-sizing: border-box; /* Thêm dòng này */
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

  <main class="main-content-add">
    <nav class="breadcrumb">
      <a href="category.php">Categories</a>
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
        <button type="button" class="btn-cancel" onclick="window.location.href='category.php'">Cancel</button>
      </div>
    </form>
  </main>
<?php
include 'includes/footer.php';
