<?php
// Dòng đầu tiên - bắt đầu session
session_start();

// Kiểm tra nếu người dùng đã đăng nhập thì chuyển hướng
if (isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Xử lý lỗi từ session nếu có
$login_error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$old_email = isset($_SESSION['old_email']) ? $_SESSION['old_email'] : '';
unset($_SESSION['login_error'], $_SESSION['old_email']);

// Hiển thị thông báo thành công nếu có
if (isset($_SESSION['register_success'])) {
    $success_message = $_SESSION['register_success'];
    unset($_SESSION['register_success']); // Xóa sau khi hiển thị
}
$pageTitle = "Log In";
include './includes/head.php'; 
?>


   <!-- Breadcrumb -->
   <div class="breadcrumb-container">
        <div class="breadcrumb">
            <a href="homepage.php" class="home-icon" title="Home">
                <i class="fas fa-home" aria-hidden="true"></i>
            </a>
            <span> &gt; </span>
            <a href="#">Account</a>
            <span> &gt; </span>
            <a href="#" class="active">Login</a>
        </div>
    </div>

    <div class="container">
        <form action="process_login.php" method="POST" class="signin-form">
            <h2>Sign In</h2>
            
            <!-- Hiển thị lỗi đăng nhập nếu có -->
            <?php if (!empty($login_error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($login_error); ?>
                </div>
            <?php endif; ?>

            <!-- Email Input -->
            <label for="email"></label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required 
                   value="<?php echo htmlspecialchars($old_email); ?>">

            <!-- Password Input -->
            <label for="password"></label>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <span class="toggle-password" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
            </div>

            <!-- Remember Me and Forgot Password -->
            <div class="options">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="login-btn">Login</button>

            <!-- Register Link -->
            <center><p>Don't have an account? <a href="register.php" class="register-link">Register</a></p></center>
        </form>
    </div>

<?php include './includes/footer.php'; ?>
<script src="./assets/scrip.js"></script>
</body>
</html>