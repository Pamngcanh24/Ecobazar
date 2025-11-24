<?php
include 'includes/header.php';

// ==================== XỬ LÝ ĐĂNG XUẤT KHI XÁC NHẬN ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout'])) {
    unset($_SESSION['driver_id']);
    session_unset();
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/');
    
    // Đi về login + thông báo thành công
    header("Location: login.php?logout=success");
    exit;
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit;
}

$driver_id = $_SESSION['driver_id'];

// ====================== UPLOAD AVATAR (giữ nguyên như cũ) ====================== 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $uploadDir = 'uploads/avatars/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $allowed = ['image/jpeg','image/jpg','image/png','image/webp'];
    $file = $_FILES['avatar'];

    if (in_array($file['type'], $allowed) && $file['size'] <= 5*1024*1024) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safe_id = preg_replace('/[^a-zA-Z0-9_-]/', '_', $driver_id);
        $filename = "driver_{$safe_id}.{$ext}";
        $path = $uploadDir . $filename;

        foreach (glob($uploadDir . "driver_{$safe_id}.*") as $oldFile) @unlink($oldFile);

        if (move_uploaded_file($file['tmp_name'], $path)) {
            $stmt = $conn->prepare("UPDATE drivers SET avatar = ? WHERE id = ?");
            $stmt->bind_param("ss", $filename, $driver_id);
            $stmt->execute(); $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// ====================== LẤY THÔNG TIN ====================== 
$stmt = $conn->prepare("SELECT id, name, email, phone, bank_account, citizen_id, created_at, avatar FROM drivers WHERE id = ?");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();
$stmt->close();

$avatar = (!empty($driver['avatar']) && file_exists("uploads/avatars/" . $driver['avatar']))
    ? "uploads/avatars/" . $driver['avatar']
    : "assets/plantlogo.png";
?>

<!-- CSS ĐẸP NHƯ GRAB + POPUP ĐĂNG XUẤT -->
<style>
    :root {
        --green: #22c55e; --green-dark: #16a34a; --green-light: #ecfdf5; --green-bg: #f8fff9;
        --text: #1e293b; --text-light: #64748b; --red: #dc2626;
    }
    .profile-card { max-width:780px; margin:2px auto; background:#fff; border-radius:20px; overflow:hidden;
        box-shadow:0 10px 30px rgba(0,0,0,0.1); border:1px solid #f0fdf4; }
    .profile-header-bar { height:6px; background:linear-gradient(90deg,var(--green),#86efac); }
    .profile-content { padding:32px 36px; }
    .profile-header { display:flex; align-items:center; gap:30px; flex-wrap:wrap; margin-bottom:10px; }
    .avatar-wrapper { width:140px; height:140px; border-radius:50%; overflow:hidden; background:var(--green-light);
        border:6px solid #fff; box-shadow:0 10px 30px rgba(34,197,94,0.35); display:flex; align-items:center; justify-content:center; }
    .avatar-wrapper img { width:100%; height:100%; object-fit:cover; }
    .avatar-wrapper img[src*="plantlogo.png"] { width:76px; height:76px; object-fit:contain; }
    .profile-info h2 { margin:0 0 8px; font-size:27px; font-weight:700; color:var(--text); }
    .driver-id { color:var(--green); font-weight:600; font-size:17px; margin-bottom:18px; font-family:'Courier New',monospace; letter-spacing:1px; }
    .profile-actions a { padding:10px 24px; border-radius:30px; font-size:14px; font-weight:600; text-decoration:none;
        display:inline-block; transition:0.3s; }
    .btn-edit { background:var(--green); color:white; }
    .btn-logout { background:#fee2e2; color:var(--red); }
    .profile-actions a:hover { transform:translateY(-4px); }
    .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:18px; margin-top:28px; }
    .info-item { background:var(--green-bg); padding:18px 22px; border-radius:16px; border-left:5px solid var(--green);
        display:flex; align-items:center; gap:16px; transition:all .35s; }
    .info-item:hover { transform:translateY(-8px); box-shadow:0 16px 32px rgba(34,197,94,0.25); border-left-color:var(--green-dark); }
    .info-icon { width:48px; height:48px; background:var(--green); color:white; border-radius:12px;
        display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
    .info-text small { display:block; font-size:12px; color:var(--text-light); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px; }
    .info-text strong { font-size:16px; color:var(--text); }
    .info-text strong.empty { color:#94a3b8; }

    /* POPUP ĐĂNG XUẤT SIÊU ĐẸP */
    #logoutModal {
        display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:9999;
        align-items:center; justify-content:center; backdrop-filter:blur(8px);
    }
    #logoutModal.show { display:flex; }
    .modal-content {
        background:#fff; width:90%; max-width:400px; border-radius:24px; overflow:hidden;
        box-shadow:0 25px 60px rgba(0,0,0,0.3); animation:pop 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
        text-align:center;
    }
    @keyframes pop { from{transform:scale(0.7);opacity:0} to{transform:scale(1);opacity:1} }
    .modal-icon {
        width:90px; height:90px; margin:30px auto 16px;
        background:#fee2e2; border-radius:50%; display:flex; align-items:center; justify-content:center;
    }
    .modal-icon i { font-size:42px; color:var(--red); }
    .modal-title { font-size:22px; font-weight:700; color:var(--text); margin:0 0 12px; }
    .modal-desc { color:var(--text-light); font-size:15px; margin:0 0 32px; padding:0 20px; line-height:1.5; }
    .modal-buttons { display:flex; gap:12px; padding:0 24px 32px; }
    .modal-buttons button {
        flex:1; padding:14px; border:none; border-radius:50px; font-weight:600; font-size:15.5px;
        cursor:pointer; transition:all 0.3s;
    }
    .btn-cancel { background:#f1f5f9; color:#475569; }
    .btn-confirm { background:var(--red); color:white; box-shadow:0 10px 30px rgba(220,38,38,0.4); }
    .btn-confirm:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(220,38,38,0.5); }

    @media (max-width:640px) {
        .profile-content { padding:24px 20px; }
        .profile-header { flex-direction:column; text-align:center; gap:20px; }
        .info-grid { grid-template-columns:1fr; }
    }
</style>

<main class="main-content" style="padding-top:10px;">
    <div class="profile-card">
        <div class="profile-header-bar"></div>
        <div class="profile-content">

            <div class="profile-header">
                <div class="avatar-container">
                    <div class="avatar-wrapper">
                        <img src="<?= $avatar ?>?v=<?= time() ?>" alt="Avatar">
                    </div>
                    <!-- <form method="POST" enctype="multipart/form-data" style="margin:0;">
                        <label class="change-avatar-label">
                            Đổi ảnh
                            <input type="file" name="avatar" accept="image/*" onchange="this.form.submit()">
                        </label>
                    </form> -->
                </div>

                <div class="profile-info">
                    <h2><?= htmlspecialchars($driver['name']) ?></h2>
                    <!-- Hiển thị nguyên bản ID dạng chuỗi, đẹp hơn -->
                    <div class="driver-id">ID: <?= htmlspecialchars($driver['id']) ?></div>
                    <div class="profile-actions">
                        <a href="driver_edit_profile.php" class="btn-edit">Chỉnh sửa</a>
                        <a href="javascript:void(0)" onclick="openLogoutModal()" class="btn-logout">Đăng xuất</a>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item"><div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-text"><small>Email</small><strong><?= htmlspecialchars($driver['email'] ?? '—') ?></strong></div></div>
                <div class="info-item"><div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="info-text"><small>Điện thoại</small><strong><?= htmlspecialchars($driver['phone'] ?? '—') ?></strong></div></div>
                <div class="info-item"><div class="info-icon"><i class="fas fa-id-card"></i></div>
                    <div class="info-text"><small>CCCD / CMND</small>
                        <strong class="<?= empty($driver['citizen_id']) ? 'empty' : '' ?>">
                            <?= htmlspecialchars($driver['citizen_id'] ?: 'Chưa cập nhật') ?>
                        </strong>
                    </div></div>
                <div class="info-item"><div class="info-icon"><i class="fas fa-university"></i></div>
                    <div class="info-text"><small>MBBank</small>
                        <strong class="<?= empty($driver['bank_account']) ? 'empty' : '' ?>">
                            <?= htmlspecialchars($driver['bank_account'] ?: 'Chưa liên kết') ?>
                        </strong>
                    </div></div>
                <div class="info-item"><div class="info-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="info-text"><small>Ngày tham gia</small>
                        <strong><?= $driver['created_at'] ? date('d/m/Y', strtotime($driver['created_at'])) : '—' ?></strong>
                    </div></div>
            </div>
        </div>
    </div>
</main>

<!-- POPUP ĐĂNG XUẤT -->
<div id="logoutModal">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-sign-out-alt"></i></div>
        <h3 class="modal-title">Xác nhận đăng xuất</h3>
        <p class="modal-desc">Bạn có chắc chắn muốn đăng xuất khỏi </br>tài khoản tài xế?</p>
        <div class="modal-buttons">
            <button class="btn-cancel" onclick="closeLogoutModal()">Hủy bỏ</button>
            <button class="btn-confirm" onclick="confirmLogout()">Đăng xuất ngay</button>
        </div>
    </div>
</div>

<script>
function openLogoutModal() {
    document.getElementById('logoutModal').classList.add('show');
}
function closeLogoutModal() {
    document.getElementById('logoutModal').classList.remove('show');
}
function confirmLogout() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = '<input type="hidden" name="confirm_logout" value="1">';
    document.body.appendChild(form);
    form.submit();
}
// Đóng khi nhấn ngoài modal
document.getElementById('logoutModal').addEventListener('click', function(e){
    if(e.target === this) closeLogoutModal();
});
</script>

<?php include 'includes/footer.php'; ?>