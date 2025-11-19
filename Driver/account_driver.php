<?php
include 'includes/header.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: driver_login.php");
    exit;
}

$driver_id = $_SESSION['driver_id']; // Bây giờ là chuỗi (string)

// ====================== UPLOAD AVATAR ====================== 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $uploadDir = 'uploads/avatars/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $allowed = ['image/jpeg','image/jpg','image/png','image/webp'];
    $file = $_FILES['avatar'];

    if (in_array($file['type'], $allowed) && $file['size'] <= 5*1024*1024) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        // Dùng driver_id nguyên bản (có thể chứa ký tự đặc biệt), nhưng vẫn an toàn nhờ sanitize
        $safe_id = preg_replace('/[^a-zA-Z0-9_-]/', '_', $driver_id);
        $filename = "driver_{$safe_id}.{$ext}";
        $path = $uploadDir . $filename;

        // Xóa ảnh cũ (dùng pattern linh hoạt hơn)
        foreach (glob($uploadDir . "driver_{$safe_id}.*") as $oldFile) {
            @unlink($oldFile);
        }

        if (move_uploaded_file($file['tmp_name'], $path)) {
            // Dùng "s" vì driver_id là string
            $stmt = $conn->prepare("UPDATE drivers SET avatar = ? WHERE id = ?");
            $stmt->bind_param("ss", $filename, $driver_id);
            $stmt->execute();
            $stmt->close();

            // Reload trang để cập nhật ảnh mới ngay lập tức
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// ====================== LẤY THÔNG TIN ====================== 
$stmt = $conn->prepare("SELECT id, name, email, phone, bank_account, citizen_id, created_at, avatar FROM drivers WHERE id = ?");
$stmt->bind_param("s", $driver_id); // "s" cho string
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();
$stmt->close();

$avatar = (!empty($driver['avatar']) && file_exists("uploads/avatars/" . $driver['avatar']))
    ? "uploads/avatars/" . $driver['avatar']
    : "assets/plantlogo.png";
?>

<!-- CSS giữ nguyên đẹp như cũ -->
<style>
    :root {
        --green: #22c55e;
        --green-dark: #16a34a;
        --green-light: #ecfdf5;
        --green-bg: #f8fff9;
        --text: #1e293b;
        --text-light: #64748b;
    }

    .profile-card {
        max-width: 780px;
        margin: 2px auto;
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid #f0fdf4;
    }
    .profile-header-bar {
        height: 6px;
        background: linear-gradient(90deg, var(--green), #86efac);
    }
    .profile-content { padding: 32px 36px; }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 30px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    /* AVATAR + NÚT ĐỔI ẢNH */
    .avatar-container {
        position: relative;
        display: inline-block;
    }
    .avatar-wrapper {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        overflow: hidden;
        background: var(--green-light);
        border: 6px solid #fff;
        box-shadow: 0 10px 30px rgba(34,197,94,0.35);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.3s ease;
    }
    .avatar-wrapper img[src*="plantlogo.png"] {
        width: 76px;
        height: 76px;
        object-fit: contain;
    }

    .change-avatar-label {
        position: absolute;
        bottom: -14px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--green);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 22px;
        border-radius: 30px;
        box-shadow: 0 6px 20px rgba(34,197,94,0.5);
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        z-index: 2;
    }
    .change-avatar-label:hover {
        transform: translateX(-50%) translateY(-6px);
        box-shadow: 0 12px 30px rgba(34,197,94,0.6);
    }
    .change-avatar-label input { display: none; }

    .profile-info h2 { margin: 0 0 8px; font-size: 27px; font-weight: 700; color: var(--text); }
    .driver-id { 
        color: var(--green); 
        font-weight: 600; 
        font-size: 17px; 
        margin-bottom: 18px; 
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
    }
    .profile-actions a {
        padding: 10px 24px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: 0.3s;
    }
    .btn-edit { background: var(--green); color: white; }
    .btn-logout { background: #fee2e2; color: #dc2626; }
    .profile-actions a:hover { transform: translateY(-4px); }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 18px;
        margin-top: 28px;
    }
    .info-item {
        background: var(--green-bg);
        padding: 18px 22px;
        border-radius: 16px;
        border-left: 5px solid var(--green);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.35s ease;
    }
    .info-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 32px rgba(34,197,94,0.25);
        border-left-color: var(--green-dark);
    }
    .info-icon {
        width: 48px; height: 48px; background: var(--green); color: white;
        border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .info-text small { display: block; font-size: 12px; color: var(--text-light); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
    .info-text strong { font-size: 16px; color: var(--text); }
    .info-text strong.empty { color: #94a3b8; }

    @media (max-width: 640px) {
        .profile-content { padding: 24px 20px; }
        .profile-header { flex-direction: column; text-align: center; gap: 20px; }
        .avatar-container { margin: 0 auto; }
        .info-grid { grid-template-columns: 1fr; }
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
                        <a href="?logout=1" class="btn-logout">Đăng xuất</a>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-text"><small>Email</small><strong><?= htmlspecialchars($driver['email'] ?? '—') ?></strong></div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="info-text"><small>Điện thoại</small><strong><?= htmlspecialchars($driver['phone'] ?? '—') ?></strong></div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-id-card"></i></div>
                    <div class="info-text"><small>CCCD / CMND</small>
                        <strong class="<?= empty($driver['citizen_id']) ? 'empty' : '' ?>">
                            <?= htmlspecialchars($driver['citizen_id'] ?: 'Chưa cập nhật') ?>
                        </strong>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-university"></i></div>
                    <div class="info-text"><small>MBBank</small>
                        <strong class="<?= empty($driver['bank_account']) ? 'empty' : '' ?>">
                            <?= htmlspecialchars($driver['bank_account'] ?: 'Chưa liên kết') ?>
                        </strong>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="info-text"><small>Ngày tham gia</small>
                        <strong><?= $driver['created_at'] ? date('d/m/Y', strtotime($driver['created_at'])) : '—' ?></strong>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>