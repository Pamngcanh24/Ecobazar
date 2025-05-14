<?php
session_start();
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích']);
    exit();
}

// Kiểm tra dữ liệu POST
if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin sản phẩm']);
    exit();
}

$product_id = intval($_POST['product_id']);
$user_id = $_SESSION['user_id'];

// Kết nối database
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối database']);
    exit();
}

// Kiểm tra xem sản phẩm đã có trong wishlist chưa
$check_stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Nếu sản phẩm đã tồn tại trong wishlist, xóa nó đi
    $delete_stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $delete_stmt->bind_param("ii", $user_id, $product_id);
    
    if ($delete_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích', 'action' => 'removed']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa sản phẩm khỏi danh sách yêu thích']);
    }
    $delete_stmt->close();
} else {
    // Nếu sản phẩm chưa có trong wishlist, thêm vào
    $insert_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $insert_stmt->bind_param("ii", $user_id, $product_id);
    
    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã thêm sản phẩm vào danh sách yêu thích', 'action' => 'added']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm sản phẩm vào danh sách yêu thích']);
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();