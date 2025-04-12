<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecobazar');
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}

$user_id = $_SESSION['user_id'] ?? 1;

// Lấy thông tin người dùng
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Lấy địa chỉ thanh toán gần nhất từ bảng orders
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
include 'head.php';
?>

<link rel="stylesheet" href="style.css">

<div class="breadcrumb-container">
    <div class="breadcrumb">
        <a href="dashboard.php" title="Home"><i class="fas fa-home"></i></a>
        <span> &gt; </span>
        <a href="dashboard.php">Account</a>
        <span> &gt; </span>
        <a href="settings.php" class="active">Settings</a>
    </div>
</div>

<div class="settings-container">
    <div class="settings-layout">
    <?php include 'dash.php'; ?>


        <!-- Main Content -->
        <div class="main-content">
        <div class="settings-main-content">
            <h1 class="settings-title">Account Settings</h1>
            
            <!-- Personal Information Section -->
            <div class="settings-section">
                <form id="personalInfoForm" class="settings-form">
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
                    <button type="submit" class="save-btn">Save Changes</button>
                </form>
            </div>

            <!-- Billing Address Section -->
            <div class="settings-section">
                <h2>Billing Address</h2>
                <form id="billingAddressForm" class="settings-form">
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
                                <option value="Viet Nam" <?php echo (isset($billing_address['country']) && $billing_address['country'] == 'US') ? 'selected' : ''; ?>>United States</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <select name="state" class="form-input">
                                <option value="Ha Noi" <?php echo (isset($billing_address['state']) && $billing_address['state'] == 'Ha Noi') ? 'selected' : ''; ?>>Ha Noi</option>
                            </select>
                            <!-- <input type="text" name="state" value="<?php echo htmlspecialchars($billing_address['state'] ?? ''); ?>" class="form-input"> -->
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
                    <button type="submit" class="save-btn">Save Changes</button>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="settings-section">
                <h2>Change Password</h2>
                <form id="changePasswordForm" class="settings-form">
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
                    <button type="submit" class="save-btn">Change Password</button>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>
</div>

<?php include 'footer.php'; ?>