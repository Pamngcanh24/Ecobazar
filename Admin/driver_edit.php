<?php
include 'includes/header.php';

// Lấy ID tài xế từ URL
$driver_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin tài xế cần sửa
$stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$driver_data = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $phone = $_POST['phone'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $address = $_POST['address'];
  $citizen_id = $_POST['citizen_id'];
  $bank_account = $_POST['bank_account'];
  
  // Kiểm tra xem có nhập mật khẩu mới không
  if (!empty($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sql = "UPDATE drivers SET phone = ?, password = ?, name = ?, email = ?, address = ?, citizen_id = ?, bank_account = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $phone, $password, $name, $email, $address, $citizen_id, $bank_account, $driver_id);
  } else {
    // Không cập nhật mật khẩu
    $sql = "UPDATE drivers SET phone = ?, name = ?, email = ?, address = ?, citizen_id = ?, bank_account = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $phone, $name, $email, $address, $citizen_id, $bank_account, $driver_id);
  }

  if ($stmt->execute()) {
    echo "<script>alert('Cập nhật tài xế thành công!'); window.location.href='driver.php';</script>";
  } else {
    $message = "Lỗi: " . $stmt->error;
  }
  $stmt->close();
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
    input, select, textarea { 
      width: 100%; 
      padding: 8px; 
      margin-top: 10px; 
      margin-bottom: 10px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-sizing: border-box;
    }    
    .actions {margin-top: 20px; }
    form { max-width: 600px; }
    button, .btn-cancel { 
      padding: 8px 15px;
      margin-right: 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button {
      background-color: #00b207; 
      color: white; }
    .btn-cancel { 
      background-color: #f1f1f1;
      color: #444;
      text-decoration: none;
      border: 1px solid #ccc;}
    .message {margin-top: 20px;color: green;}
    .password-wrapper {position: relative;}
    .password-wrapper input { width: 100%;padding-right: 30px;}  
    .toggle-password { position: absolute;top: 50%;right: 10px;transform: translateY(-50%);cursor: pointer;color: #aaa;}
</style>

<main class="main-content-add">
    <nav class="breadcrumb">
      <a href="driver.php">Drivers</a>
      <span class="separator">›</span>
      <span class="current">Edit</span>
    </nav>    
    <h1>Edit Driver</h1>

    <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

    <form action="" method="POST">
      <label for="phone">Phone Number *</label>
      <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($driver_data['phone']); ?>" required>

      <label for="password">Password</label>
      <div class="password-wrapper">
          <input type="password" id="password" name="password">
          <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
      </div>

      <label for="name">Full Name *</label>
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($driver_data['name']); ?>" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($driver_data['email']); ?>">

      <label for="address">Address</label>
      <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($driver_data['address']); ?>">

      <label for="citizen_id">Citizen ID</label>
      <input type="text" id="citizen_id" name="citizen_id" value="<?php echo htmlspecialchars($driver_data['citizen_id']); ?>">

      <label for="bank_account">Bank Account</label>
      <input type="text" id="bank_account" name="bank_account" value="<?php echo htmlspecialchars($driver_data['bank_account']); ?>">

      <div class="form-actions">
        <button type="submit">Update</button>
        <a href="driver.php" class="btn-cancel">Cancel</a>
      </div>
    </form>
</main>

<script>
function togglePassword() {
  const passwordInput = document.getElementById('password');
  const icon = document.querySelector('.toggle-password');
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    passwordInput.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
</script>
<?php include 'includes/footer.php'; ?>

