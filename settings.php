<?php
session_start();
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}

$user_id = $_SESSION['user_id'] ?? 1;

// Xử lý các biểu mẫu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cập nhật thông tin cá nhân
    if (isset($_POST['update_personal'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
            $_SESSION['message'] = "Vui lòng điền đầy đủ các trường.";
            $_SESSION['message_type'] = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Email không hợp lệ.";
            $_SESSION['message_type'] = "error";
        } else {
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $user_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Cập nhật thông tin cá nhân thành công!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Lỗi cập nhật thông tin người dùng: " . $conn->error;
                $_SESSION['message_type'] = "error";
            }
            $stmt->close();
        }
        header("Location: settings.php");
        exit();
    }

    // Cập nhật địa chỉ thanh toán
    if (isset($_POST['update_billing'])) {
        $billing_name = trim($_POST['billing_name']);
        $billing_address = trim($_POST['billing_address']);
        $billing_email = trim($_POST['billing_email']);
        $billing_phone = trim($_POST['billing_phone']);

        if (empty($billing_name) || empty($billing_address) || empty($billing_email) || empty($billing_phone)) {
            $_SESSION['message'] = "Vui lòng điền đầy đủ các trường.";
            $_SESSION['message_type'] = "error";
        } elseif (!filter_var($billing_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Email thanh toán không hợp lệ.";
            $_SESSION['message_type'] = "error";
        } else {
            $order_result = $conn->query("SELECT id FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1");
            if ($order_result && $order_result->num_rows > 0) {
                $order_id = $order_result->fetch_assoc()['id'];
                $stmt = $conn->prepare("UPDATE orders SET billing_name = ?, billing_address = ?, billing_email = ?, billing_phone = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $billing_name, $billing_address, $billing_email, $billing_phone, $order_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Cập nhật địa chỉ thanh toán thành công!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Lỗi cập nhật địa chỉ thanh toán: " . $conn->error;
                    $_SESSION['message_type'] = "error";
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "Không tìm thấy đơn hàng để cập nhật.";
                $_SESSION['message_type'] = "error";
            }
        }
        header("Location: settings.php");
        exit();
    }

    // Đổi mật khẩu
    if (isset($_POST['change_password'])) {
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['message'] = "Vui lòng điền đầy đủ các trường.";
            $_SESSION['message_type'] = "error";
        } elseif (strlen($new_password) < 8) {
            $_SESSION['message'] = "Mật khẩu mới phải dài ít nhất 8 ký tự.";
            $_SESSION['message_type'] = "error";
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            $stmt->close();

            if ($user_data && password_verify($current_password, $user_data['password'])) {
                if ($new_password === $confirm_password) {
                    $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $new_password_hashed, $user_id);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = "Đổi mật khẩu thành công!";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Lỗi đổi mật khẩu: " . $conn->error;
                        $_SESSION['message_type'] = "error";
                    }
                    $stmt->close();
                } else {
                    $_SESSION['message'] = "Mật khẩu mới và xác nhận không khớp.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                $_SESSION['message'] = "Mật khẩu hiện tại không đúng.";
                $_SESSION['message_type'] = "error";
            }
        }
        header("Location: settings.php");
        exit();
    }
}

// Lấy thông tin người dùng
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Lấy địa chỉ thanh toán mới nhất
$billing_query = $conn->query("SELECT 
    billing_name, 
    billing_address, 
    billing_email, 
    billing_phone,
    billing_name as first_name, 
    SUBSTRING_INDEX(billing_name, ' ', -1) as last_name,
    '' as company,
    billing_address as address1,
    '' as address2,
    'US' as country,
    '' as state,
    '' as postcode
FROM orders 
WHERE user_id = $user_id 
ORDER BY created_at DESC 
LIMIT 1");

$billing_address = $billing_query->fetch_assoc();

$pageTitle = "Account Settings";
include './includes/head.php';
?>

<link rel="stylesheet" href="./assets/style.css">

<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="dashboard.php" title="Home"><i class="fas fa-home"></i></a>
        <span> > </span>
        <a href="dashboard.php">Account</a>
        <span> > </span>
        <a href="settings.php" class="active">Settings</a>
    </div>
</div>

<!-- Hiển thị toast notification -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="toast <?php echo $_SESSION['message_type']; ?>">
        <span class="toast-icon">
            <?php echo $_SESSION['message_type'] === 'success' ? '✔' : '✖'; ?>
        </span>
        <span class="toast-message"><?php echo htmlspecialchars($_SESSION['message']); ?></span>
        <button class="close-btn">×</button>
    </div>
    <?php
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
<?php endif; ?>

<div class="settings-container">
    <div class="settings-layout">
        <?php include './includes/dash.php'; ?>
        <!-- Main Content -->
        <div class="main-content">
            <div class="settings-main-content">
                <h1 class="settings-title">Account Settings</h1>
                <!-- Personal Information Section -->
                <div class="settings-section">
                    <h2>Personal Information</h2>
                    <form id="personalInfoForm" class="settings-form" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label>First name</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" class="form-input">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="form-input">
                            </div>
                        </div>
                        <button type="submit" name="update_personal" class="save-btn">Save Changes</button>
                    </form>
                </div>
                <!-- Billing Address Section -->
                <div class="settings-section">
                    <h2>Billing Address</h2>
                    <form id="billingAddressForm" class="settings-form" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="billing_name" value="<?php echo htmlspecialchars($billing_address['billing_name'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Company Name (optional)</label>
                                <input type="text" name="company" value="<?php echo htmlspecialchars($billing_address['company'] ?? ''); ?>" class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Street Address</label>
                            <textarea name="billing_address" class="form-input"><?php echo htmlspecialchars($billing_address['billing_address'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Country / Region</label>
                                <select name="country" class="form-input">
                                    <option value="US" <?php echo (isset($billing_address['country']) && $billing_address['country'] == 'US') ? 'selected' : ''; ?>>United States</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <select name="state" class="form-input">
                                    <option value="Ha Noi" <?php echo (isset($billing_address['state']) && $billing_address['state'] == 'Ha Noi') ? 'selected' : ''; ?>>Ha Noi</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Zip Code</label>
                                <input type="text" name="postcode" value="<?php echo htmlspecialchars($billing_address['postcode'] ?? ''); ?>" class="form-input">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="billing_email" value="<?php echo htmlspecialchars($billing_address['billing_email'] ?? $user['email']); ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" name="billing_phone" value="<?php echo htmlspecialchars($billing_address['billing_phone'] ?? $user['phone']); ?>" class="form-input">
                            </div>
                        </div>
                        <button type="submit" name="update_billing" class="save-btn">Save Changes</button>
                    </form>
                </div>
                <!-- Change Password Section -->
                <div class="settings-section">
                    <h2>Change Password</h2>
                    <form id="changePasswordForm" class="settings-form" method="post">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-input">
                        </div>
                        <button type="submit" name="change_password" class="save-btn">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="./assets/scrip.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include './includes/footer.php'; ?>