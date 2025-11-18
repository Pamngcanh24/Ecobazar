<?php
include 'includes/header.php';

// Lấy ngày giờ hiện tại
$currentDateTime = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
$formattedDateTime = $currentDateTime->format('l, F j, Y, h:i A T'); // e.g., Sunday, November 16, 2025, 03:04 PM +07

// Kiểm tra nếu ID không được cung cấp
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID người dùng không hợp lệ!";
    header("Location: driver.php");
    exit;
}

$id = intval($_GET['id']);

// Lấy thông tin chi tiết của driver
$stmt = $conn->prepare("SELECT id, name, email, phone, bank_account FROM drivers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Không tìm thấy người dùng!";
    header("Location: driver.php");
    exit;
}

$driver = $result->fetch_assoc();
$stmt->close();

// Xử lý xóa driver
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id === $id) {
        $delete_stmt = $conn->prepare("DELETE FROM drivers WHERE id = ?");
        $delete_stmt->bind_param("i", $delete_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "Xóa người dùng thành công!";
            header("Location: driver.php");
        } else {
            $_SESSION['error_message'] = "Có lỗi xảy ra khi xóa người dùng!";
            header("Location: driver_detail.php?id=" . $id);
        }
        $delete_stmt->close();
        exit;
    }
}
?>

<main class="main-content">
    <div class="header-row">
        <h2>Driver Details</h2>
        <div class="datetime-display">
            <span>Updated: <?php echo $formattedDateTime; ?></span>
        </div>
        <a href="driver.php" class="btn-back">Back to List</a>
    </div>

    <div class="driver-detail-container">
        <div class="detail-card">
            <h3>Driver Information</h3>
            <div class="detail-item">
                <span class="detail-label">ID:</span>
                <span class="detail-value"><?php echo htmlspecialchars($driver['id']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Name:</span>
                <span class="detail-value"><?php echo htmlspecialchars($driver['name']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?php echo htmlspecialchars($driver['email']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Phone:</span>
                <span class="detail-value"><?php echo htmlspecialchars($driver['phone']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Bank Account (MBBANK):</span>
                <span class="detail-value"><?php echo htmlspecialchars($driver['bank_account']); ?></span>
            </div>
            <div class="detail-actions">
                <a href="driver_edit.php?id=<?php echo $driver['id']; ?>" class="edit-link">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="#" 
                   onclick="showConfirmModal('driver_detail.php?delete_id=<?php echo $driver['id']; ?>'); return false;"
                   class="delete-link">
                    <i class="fas fa-trash-alt"></i> Delete
                </a>
            </div>
        </div>
    </div>
</main>

<!-- Modal xác nhận xóa -->
<div id="confirmModal" class="confirm-modal">
    <div class="confirm-content">
        <h3>Xác nhận xóa</h3>
        <p>Bạn có chắc chắn muốn xóa người dùng này?</p>
        <div class="confirm-buttons">
            <button id="confirmDelete" class="btn-confirm">Xóa</button>
            <button onclick="closeModal()" class="btn-cancel">Hủy</button>
        </div>
    </div>
</div>

<style>
/* Detail Container */
.driver-detail-container {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.detail-card {
    max-width: 600px;
    margin: 0 auto;
}

.detail-card h3 {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 20px;
    font-weight: 600;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e2e8f0;
}

.detail-label {
    font-weight: 500;
    color: #64748b;
}

.detail-value {
    color: #1e293b;
    font-weight: 400;
}

.detail-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.edit-link, .delete-link {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.edit-link {
    background: #4CAF50;
    color: #fff;
}

.edit-link:hover {
    background: #45a049;
}

.delete-link {
    background: #dc3545;
    color: #fff;
}

.delete-link:hover {
    background: #c82333;
}

.btn-back {
    padding: 8px 15px;
    background: #6c757d;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: #5a6268;
}

/* Header Row Enhancements */
.header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.datetime-display {
    color: #64748b;
    font-size: 0.9rem;
}

.datetime-display span {
    padding: 5px 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

/* Modal Styles */
.confirm-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    font-family: 'Poppins', sans-serif;
}

.confirm-content {
    background: white;
    padding: 35px;
    border-radius: 12px;
    text-align: center;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.confirm-content h3 {
    margin: 0 0 20px;
    color: #333;
    font-size: 24px;
    font-weight: 600;
}

.confirm-content p {
    color: #666;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 25px;
}

.confirm-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 25px;
}

.confirm-buttons button {
    padding: 10px 25px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    font-size: 15px;
    transition: all 0.3s ease;
}

.btn-confirm {
    background: #dc3545;
    color: white;
}

.btn-confirm:hover {
    background: #c82333;
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background: #5a6268;
}

/* Dark Mode */
@media (prefers-color-scheme: dark) {
    .driver-detail-container, .detail-card, .header-row {
        background: #1e293b;
    }
    .detail-label { color: #94a3b8; }
    .detail-value, .detail-card h3, .header-row h2 { color: #f1f5f9; }
    .datetime-display { color: #94a3b8; }
    .datetime-display span { background: #2d3748; }
    .confirm-content { background: #1e293b; }
    .confirm-content h3 { color: #e2e8f0; }
    .confirm-content p { color: #94a3b8; }
}
</style>

<script>
function showConfirmModal(deleteUrl) {
    document.getElementById('confirmModal').style.display = 'flex';
    document.getElementById('confirmDelete').onclick = function() {
        window.location.href = deleteUrl;
    };
}

function closeModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

// Đóng modal khi click ra ngoài
window.onclick = function(event) {
    let modal = document.getElementById('confirmModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'includes/footer.php'; ?>