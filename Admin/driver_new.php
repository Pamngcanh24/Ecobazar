<?php
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $phone = $_POST['phone'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $name = $_POST['name'];
  $email = $_POST['email'];
  $address = $_POST['address'];
  $citizen_id = $_POST['citizen_id'];
  $bank_account = $_POST['bank_account'];

  // $created_at = date('Y-m-d H:i:s');
  // $remember_token = bin2hex(random_bytes(16));
      // Tạo ID tự động cho driver
    $result = $conn->query("SELECT id FROM drivers ORDER BY id DESC LIMIT 1");
    $lastId = $result->fetch_assoc()['id'] ?? null;

    if ($lastId) {
        $num = (int)str_replace('D-', '', $lastId); // Lấy số
        $num++; // Tăng 1
    } else {
        $num = 1; // Nếu chưa có dữ liệu
    }
    $newId = 'D-' . str_pad($num, 2, '0', STR_PAD_LEFT); // Tạo D-XX

    // Các thông tin khác
    $created_at = date('Y-m-d H:i:s');
    $remember_token = bin2hex(random_bytes(16));
    $token_expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Thêm dữ liệu
    $stmt = $conn->prepare("INSERT INTO drivers (id, phone, password, name, email, address, citizen_id, bank_account, created_at, remember_token, token_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $newId, $phone, $password, $name, $email, $address, $citizen_id, $bank_account, $created_at, $remember_token, $token_expiry);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm tài xế thành công!'); window.location.href='driver.php';</script>";
    } else {
        $message = "Lỗi: " . $stmt->error;
    }
    $stmt->close();
}
?>

<style>
    .form-container { max-width: 600px; margin: auto; }
    h2  {
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
    input { 
      width: 100%; 
      padding: 8px; 
      margin-top: 10px; 
      margin-bottom: 10px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-sizing: border-box;
    }
    .message {margin-top: 20px;color: green;}
    .main-content-add { padding: 40px; flex: 1;}
    .password-wrapper {position: relative;}
    .password-wrapper input { width: 100%;padding-right: 30px;}  
    .toggle-password { position: absolute;top: 50%;right: 10px;transform: translateY(-50%);cursor: pointer;color: #aaa;}
</style>

<main class="main-content-add">
    <nav class="breadcrumb">
      <a href="driver.php">Drivers</a>
      <span class="separator">›</span>
      <span class="current">New</span>
    </nav>    
    <h2>Create New Driver</h2>

    <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

    <form action="" method="POST">
      <label for="phone">Phone Number *</label>
      <input type="text" id="phone" name="phone" required>

      <label for="password">Password *</label>
      <div class="password-wrapper">
          <input type="password" id="password" name="password" required>
          <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
      </div>

      <label for="name">Full Name *</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email">

      <label for="address">Address</label>
      <input type="text" id="address" name="address">

      <label for="citizen_id">Citizen ID</label>
      <input type="text" id="citizen_id" name="citizen_id">

      <label for="bank_account">Bank Account</label>
      <input type="text" id="bank_account" name="bank_account">

      <div class="form-actions">
        <button type="submit" class="btn-create">Create</button>
        <button type="submit" class="btn-another">Create & create another</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='driver.php'">Cancel</button>
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