<?php
include 'includes/header.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: driver_login.php");
    exit;
}

$driver_id = $_SESSION['driver_id'];
$success = $error = '';

// Lấy dữ liệu
$stmt = $conn->prepare("SELECT name, email, phone, citizen_id, bank_account, avatar FROM drivers WHERE id = ?");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();
$stmt->close();

$avatar = (!empty($driver['avatar']) && file_exists("uploads/avatars/" . $driver['avatar']))
    ? "uploads/avatars/" . $driver['avatar']
    : "assets/plantlogo.png";

// XỬ LÝ FORM (chỉ cho phép sửa: tên + ngân hàng + đổi mật khẩu + avatar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // === UPLOAD AVATAR ===
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $uploadDir = 'uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $allowed = ['image/jpeg','image/jpg','image/png','image/webp'];
        $file = $_FILES['avatar'];

        if (in_array($file['type'], $allowed) && $file['size'] <= 5*1024*1024) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = "driver_{$driver_id}.{$ext}";
            $path = $uploadDir . $filename;

            foreach (glob($uploadDir . "driver_{$driver_id}.*") as $old) @unlink($old);

            if (move_uploaded_file($file['tmp_name'], $path)) {
                $stmt = $conn->prepare("UPDATE drivers SET avatar = ? WHERE id = ?");
                $stmt->bind_param("ss", $filename, $driver_id);
                $stmt->execute(); $stmt->close();
                $avatar = $path . "?v=" . time();
                $success = "Đổi ảnh thành công! ";
            }
        }
    }

    // Chỉ lấy những trường được phép sửa
    $name         = trim($_POST['name'] ?? '');
    $bank_account = trim($_POST['bank_account'] ?? '');

    $current_pass = $_POST['current_password'] ?? '';
    $new_pass     = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';
    $change_pass  = !empty($current_pass) || !empty($new_pass) || !empty($confirm_pass);

    // Validate
    if (empty($name)) $error = 'Vui lòng nhập họ tên.';

    // Validate đổi mật khẩu
    if ($change_pass) {
        if (empty($current_pass) || empty($new_pass) || empty($confirm_pass))
            $error = 'Vui lòng nhập đầy đủ các trường mật khẩu.';
        elseif (strlen($new_pass) < 6)
            $error = 'Mật khẩu mới phải ít nhất 6 ký tự.';
        elseif ($new_pass !== $confirm_pass)
            $error = 'Mật khẩu xác nhận không khớp.';
        else {
            $stmt = $conn->prepare("SELECT password FROM drivers WHERE id = ?");
            $stmt->bind_param("s", $driver_id);
            $stmt->execute();
            $hash = $stmt->get_result()->fetch_assoc()['password'] ?? '';
            $stmt->close();

            if (!password_verify($current_pass, $hash))
                $error = 'Mật khẩu hiện tại không đúng.';
        }
    }

    // Lưu dữ liệu (chỉ cập nhật những gì được phép)
    if (empty($error)) {
        $conn->autocommit(false);
        try {
            $stmt = $conn->prepare("UPDATE drivers SET name = ?, bank_account = ? WHERE id = ?");
            $stmt->bind_param("sss", $name, $bank_account, $driver_id);
            $stmt->execute(); $stmt->close();

            if ($change_pass) {
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE drivers SET password = ? WHERE id = ?");
                $stmt->bind_param("ss", $new_hash, $driver_id);
                $stmt->execute(); $stmt->close();
            }

            $conn->commit();
            $success .= 'Cập nhật thông tin thành công!';
            $driver['name'] = $name;
            $driver['bank_account'] = $bank_account;
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Có lỗi xảy ra, vui lòng thử lại.';
        }
        $conn->autocommit(true);
    }
}
?>

<style>
    :root {
        --green: #22c55e;
        --green-light: #ecfdf5;
        --green-bg: #f8fff9;
        --text: #1e293b;
        --text-light: #64748b;
        --gray: #94a3b8;
    }

    .edit-card {
        max-width: 800px;
        margin: 2px auto;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: 1px solid #f0fdf4;
        font-family: system-ui, -apple-system, sans-serif;
    }
    .edit-header { height: 5px; background: linear-gradient(90deg, var(--green), #86efac); }
    .edit-content { padding: 28px 32px; font-size: 14.5px; line-height: 1.5; }

    .page-title { font-size: 22px; font-weight: 700; text-align: center; color: var(--text); margin: 0 0 6px; }
    .page-subtitle { font-size: 13.5px; color: var(--text-light); text-align: center; margin-bottom: 24px; }

    .alert {
        padding: 11px 16px; border-radius: 10px; margin-bottom: 20px;
        font-size: 13.8px; font-weight: 600; text-align: center;
    }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* Avatar */
    .avatar-section { text-align: center; margin-bottom: 24px; }
    .avatar-preview {
        width: 108px; height: 108px; border-radius: 50%; overflow: hidden;
        margin: 0 auto 12px; border: 4px solid #fff;
        box-shadow: 0 6px 20px rgba(34,197,94,0.25); background: var(--green-light);
        display: flex; align-items: center; justify-content: center;
    }
    .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-preview img[src*="plantlogo.png"] { width: 62px; height: 62px; object-fit: contain; }
    .change-avatar-text { color: var(--green); font-weight: 600; font-size: 13.5px; cursor: pointer; }

    /* Form */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
        margin-bottom: 10px;
    }
    .form-group label {
        display: block; font-size: 12.8px; color: var(--text-light);
        text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; font-weight: 600;
    }
    .form-group input {
        width: 100%; padding: 12px 15px; border: 1.8px solid #e2e8f0;
        border-radius: 12px; font-size: 14.5px; box-sizing: border-box;
    }
    .form-group input:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3.5px rgba(34,197,94,0.15); }

    /* TRƯỜNG KHÔNG THỂ SỬA – SIÊU ĐẸP */
    .locked-field {
        position: relative;
        background: #f8fafc !important;
        color: var(--text) !important;
        cursor: not-allowed;
    }
    .locked-field::after {
        content: "Khóa";
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        background: var(--gray); color: white; font-size: 11px; padding: 4px 8px;
        border-radius: 6px; pointer-events: none;
    }

    .password-box {
        grid-column: 1 / -1; background: var(--green-bg); padding: 22px 1px;
        border-radius: 14px; border: 1.8px dashed var(--green-light); margin-top: 8px;
    }
    .password-title { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 16px; }

    .btn-save {
        display: block; width: 100%; max-width: 300px; margin: 28px auto 8px;
        padding: 13px 20px; background: var(--green); color: white; border: none;
        border-radius: 50px; font-size: 15px; font-weight: 600; cursor: pointer;
        box-shadow: 0 6px 20px rgba(34,197,94,0.35); transition: all 0.3s;
    }
    .btn-save:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(34,197,94,0.45); }
    .btn-back { display: block; text-align: center; color: var(--green); font-weight: 600; font-size: 14px; text-decoration: none; margin-top: 16px; }
    .btn-back:hover { text-decoration: underline; }

    @media (max-width: 640px) {
        .edit-content { padding: 24px 18px; }
        .form-grid { grid-template-columns: 1fr; gap: 16px; }
        .password-box { padding: 20px; }
        .avatar-preview { width: 96px; height: 96px; }
        .avatar-preview img[src*="plantlogo.png"] { width: 56px; height: 56px; }
    }
</style>

<main class="main-content" style="padding-top:10px;">
    <div class="edit-card">
        <div class="edit-header"></div>
        <div class="edit-content">

            <h1 class="page-title">Chỉnh sửa hồ sơ</h1>
            <p class="page-subtitle">Chỉ có thể thay đổi họ tên, ảnh đại diện, ngân hàng và mật khẩu</p>

            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

            <!-- Ảnh đại diện -->
            <div class="avatar-section">
                <div class="avatar-preview">
                    <img src="<?= $avatar ?>?v=<?= time() ?>" alt="Avatar">
                </div>
                <form method="POST" enctype="multipart/form-data" style="margin:0;">
                    <label class="change-avatar-text">
                        Đổi ảnh đại diện
                        <input type="file" name="avatar" accept="image/*" onchange="this.form.submit()" style="display:none;">
                    </label>
                </form>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">

                    <div class="form-group">
                        <label>Họ và tên *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($driver['name']) ?>" required>
                    </div>

                    <!-- EMAIL – KHÔNG THỂ SỬA -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" value="<?= htmlspecialchars($driver['email']) ?>" class="locked-field" readonly tabindex="-1">
                    </div>

                    <!-- SỐ ĐIỆN THOẠI – KHÔNG THỂ SỬA -->
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" value="<?= htmlspecialchars($driver['phone']) ?>" class="locked-field" readonly tabindex="-1">
                    </div>

                    <!-- CCCD – KHÔNG THỂ SỬA -->
                    <div class="form-group">
                        <label>CCCD / CMND</label>
                        <input type="text" value="<?= htmlspecialchars($driver['citizen_id'] ?? 'Chưa cập nhật') ?>" class="locked-field" readonly tabindex="-1">
                    </div>

                    <!-- NGÂN HÀNG – ĐƯỢC PHÉP SỬA -->
                    <div class="form-group">
                        <label>Số tài khoản MBBank</label>
                        <input type="text" name="bank_account" value="<?= htmlspecialchars($driver['bank_account'] ?? '') ?>" placeholder="Có thể thay đổi">
                    </div>

                </div>

                <!-- Đổi mật khẩu -->
                <div class="password-box">
                    <div class="password-title">Đổi mật khẩu (để trống nếu không đổi)</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu mới</label>
                            <input type="password" name="new_password" autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label>Xác nhận mật khẩu</label>
                            <input type="password" name="confirm_password" autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-save">Lưu thay đổi</button>
            </form>

            <a href="account_driver.php" class="btn-back">Quay lại hồ sơ</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>