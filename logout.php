<?php
session_start();
// Hủy toàn bộ session
session_unset();
session_destroy();
setcookie('remember_token', '', time() - 3600, '/'); // Xóa cookie nếu có

// Chuyển hướng về trang chủ
header("Location: login.php");
exit;
