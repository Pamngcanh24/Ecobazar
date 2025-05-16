<?php
session_start();

// Kiểm tra nếu đã đăng nhập thì chuyển hướng đến dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Kết nối database
$conn = new mysqli("localhost", "root", "", "ecobazar");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý đăng nhập
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    // Sử dụng Prepared Statement để tránh SQL Injection
    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        // Kiểm tra mật khẩu
        if (password_verify($password, $admin['password'])) {
            // Lưu thông tin admin vào session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Xử lý Remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30); // 30 ngày
                
                // Cập nhật token trong database
                $updateStmt = $conn->prepare("UPDATE admin SET remember_token = ?, token_expiry = ? WHERE id = ?");
                $updateStmt->bind_param("ssi", $token, $expiry, $admin['id']);
                $updateStmt->execute();
                
                // Lưu cookie
                setcookie('admin_remember_token', $token, time() + 60 * 60 * 24 * 30, '/');
            }
            
            // Chuyển hướng đến trang dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Sai mật khẩu!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <form method="post" class="login-form">

            <h2>
            <img src="assets/Group.png" alt="Dashboard Icon" style="vertical-align: middle; margin-right: 10px; height: 1em;">
            Admin Sign In
            </h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            
            <div class="password-wrapper">
                <input type="password" name="password" placeholder="Mật khẩu" id="password" required>
                <span class="toggle-password" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
            </div>
            
            <div class="options">
                <label><input type="checkbox" name="remember"> Remember me</label>
            </div>

            <button type="submit" name="login">Login</button>
        </form>
    </div>

    <script>
      function togglePassword(id = 'password') {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector("i");
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

    </script>
</body>
</html>
<?php $conn->close(); ?>
