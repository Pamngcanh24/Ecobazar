<?php
session_start();

// Kiểm tra nếu đã đăng nhập thì chuyển hướng đến dashboard
if (isset($_SESSION['driver_id'])) {
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
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    // Sử dụng Prepared Statement để tránh SQL Injection
    $sql = "SELECT * FROM drivers WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $driver = $result->fetch_assoc();
        // Kiểm tra mật khẩu
        if (password_verify($password, $driver['password'])) {
            // Lưu thông tin driver vào session
            $_SESSION['driver_id'] = $driver['id'];
            $_SESSION['driver_phone'] = $driver['phone'];
            
            // Xử lý Remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30); // 30 ngày
                
                // Cập nhật token trong database
                $updateStmt = $conn->prepare("UPDATE drivers SET remember_token = ?, token_expiry = ? WHERE id = ?");
                $updateStmt->bind_param("ssi", $token, $expiry, $driver['id']);
                $updateStmt->execute();
                
                // Lưu cookie
                setcookie('driver_remember_token', $token, time() + 60 * 60 * 24 * 30, '/');
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
    <title>Driver Login</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" href="assets/plantlogo.png" type="image/png">
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
            Driver Sign In
            </h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            
            <input type="tel" name="phone" placeholder="Số điện thoại" required>
            
            <div class="password-wrapper">
                <input type="password" name="password" placeholder="Mật khẩu" id="password" required>
                <span class="toggle-password" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
            </div>
            
            <div class="remember-me">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Ghi nhớ đăng nhập</label>
            </div>
            
            <button type="submit" name="login">Đăng nhập</button>
        </form>
    </div>

    <script>
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var toggleIcon = document.querySelector(".toggle-password i");
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }
    </script>

</body>
</html>