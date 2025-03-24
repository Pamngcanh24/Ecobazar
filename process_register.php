<?php
// DÒNG ĐẦU TIÊN - luôn bắt đầu session trước mọi thứ
session_start();

// Kết nối database
$host = 'localhost';
$dbname = 'ecobazar';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validate dữ liệu
    $errors = [];
    
    // Kiểm tra email
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    // Kiểm tra mật khẩu
    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống";
    } elseif (strlen($password) < 8) {
        $errors[] = "Mật khẩu phải có ít nhất 8 ký tự";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Mật khẩu phải chứa ít nhất 1 chữ hoa";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Mật khẩu phải chứa ít nhất 1 số";
    }
    
    // Kiểm tra xác nhận mật khẩu
    if ($password !== $confirm_password) {
        $errors[] = "Mật khẩu xác nhận không khớp";
    }
    
    // Kiểm tra điều khoản
    if (!$terms) {
        $errors[] = "Bạn phải chấp nhận điều khoản";
    }
    
    // Kiểm tra email đã tồn tại chưa (chỉ khi email hợp lệ)
    if (empty($errors) || filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email này đã được đăng ký";
        }
    }
    
    // Nếu không có lỗi, thêm user vào database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$email, $hashed_password]);
            
            // Chuyển hướng với thông báo thành công
            $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
            header("Location: login.php");
            exit();
        } catch(PDOException $e) {
            $errors[] = "Có lỗi xảy ra khi đăng ký: " . $e->getMessage();
        }
    }
    
    // Nếu có lỗi, lưu vào session và chuyển hướng về trang đăng ký
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_email'] = $email;
        header("Location: register.php");
        exit();
    }
}
?>