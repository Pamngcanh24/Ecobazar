<?php
$conn = new mysqli("localhost", "root", "", "ecobazar");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = 'admin@gmail.com'; // thay đổi username nếu cần
$password = '123456'; // thay đổi password nếu cần
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
$stmt->bind_param("ss", $hashed_password, $username);

if ($stmt->execute()) {
    echo "Cập nhật mật khẩu thành công!";
} else {
    echo "Lỗi: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>