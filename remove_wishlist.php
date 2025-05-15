<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra id từ URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: wishlist.php");
    exit();
}

$wishlist_id = intval($_GET['id']);

// Kết nối database
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xóa sản phẩm khỏi wishlist
$stmt = $conn->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $wishlist_id, $_SESSION['user_id']);
$stmt->execute();

// Kiểm tra xem có xóa thành công không
if ($stmt->affected_rows > 0) {
    // Thành công - chuyển hướng về trang wishlist
    header("Location: wishlist.php?removed=1");
} else {
    // Thất bại - chuyển hướng về trang wishlist với thông báo lỗi
    header("Location: wishlist.php?error=1");
}

$stmt->close();
$conn->close();
?>