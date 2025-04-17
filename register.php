<?php
session_start(); // PHẢI là dòng đầu tiên, trước mọi HTML
// Hiển thị thông báo đăng ký thành công nếu có
if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
    echo '<div class="success-message">Đăng ký thành công! Vui lòng đăng nhập.</div>';
}
$pageTitle = "Create Account";
include './includes/head.php';
?>

   <!-- Breadcrumb -->
   <div class="breadcrumb-container">
        <div class="breadcrumb">
            <a href="#" class="home-icon" title="Home">
                <i class="fas fa-home" aria-hidden="true"></i>
            </a>
            <span> &gt; </span>
            <a href="#">Account</a>
            <span> &gt; </span>
            <a href="#" class="active">Create Account</a>
        </div>
    </div>

    <div class="container1">
        <form action="process_register.php" method="POST" class="signup-form">
            <h2>Create Account</h2>
            
            <!-- Hiển thị lỗi nếu có -->
            <?php
    
            if (isset($_SESSION['errors'])) {
                echo '<div class="error-messages">';
                foreach ($_SESSION['errors'] as $error) {
                    echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['errors']);
            }
            ?>

            <!-- Email Input -->
            <label for="email"></label>
            <input type="email" id="email" name="email" placeholder="Email" required 
                   value="<?php echo isset($_SESSION['old_email']) ? htmlspecialchars($_SESSION['old_email']) : ''; ?>">
            <?php unset($_SESSION['old_email']); ?>
         <!--password-->
         <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('password')"><i class="fas fa-eye"></i></span>
            </div>
            <!-- confirm password-->
            <div class="input-group">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <span class="toggle-password" onclick="togglePassword('confirm_password')"><i class="fas fa-eye"></i></span>
            </div>

            <!-- Terms & Conditions -->
            <div class="checkbox-container">
                <label for="terms"><input type="checkbox" id="terms" name="terms">Accept all terms & Conditions</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="create-btn">Create Account</button>

            <!-- Login Link -->
            <center><p>Already have account? <a href="login.php" class="login-link">Login</a></p></center>
        </form>
    </div>

<?php include './includes/footer.php'; ?>
<script src="./assets/scrip.js"></script>
</body>
</html>
