<?php
session_start();
require './database/db.php'; // File kết nối database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate dữ liệu
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Email không hợp lệ";
        $_SESSION['old_email'] = $email;
        header("Location: login.php");
        exit();
    }
    
    // Kiểm tra thông tin đăng nhập
    $stmt = $conn->prepare("SELECT id,first_name, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Đăng nhập thành công
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name']; // Thêm dòng này để hiển thị tên

        // Nếu chọn "Remember me"
        if ($user && password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name']; // thêm tên người dùng
        
        // Nếu chọn "Remember me"
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30); // 30 ngày
                
        // Lưu token vào database
            $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
            $stmt->execute([$token, $expiry, $user['id']]);
                
        // Lưu cookie
            setcookie('remember_token', $token, time() + 60 * 60 * 24 * 30, '/');
        }
            header("Location: dashboard.php");
            exit();
        }        
    } else {
        // Đăng nhập thất bại
        $_SESSION['login_error'] = "Email hoặc mật khẩu không đúng";
        $_SESSION['old_email'] = $email;
        header("Location: login.php");
        exit();
    }
}
?>