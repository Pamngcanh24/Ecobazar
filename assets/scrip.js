function togglePassword(id = 'password') {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector("i");
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

// Xóa các hàm liên quan đến quản lý địa chỉ thanh toán
// Giữ lại các hàm upload ảnh và đổi mật khẩu

// Hàm autoFillFromPrevious (nếu muốn cho phép điền từ địa chỉ cũ nhưng phải xác nhận)
function autoFillFromPrevious(addressId) {
    if (confirm('Bạn có muốn sử dụng địa chỉ này cho đơn hàng mới không?\nLưu ý: Bạn vẫn cần xác nhận lại thông tin khi thanh toán.')) {
        // Gọi API lấy thông tin địa chỉ
        fetch(`/api/get-address/${addressId}`)
            .then(response => response.json())
            .then(data => {
                // Điền vào form thanh toán (nếu có)
                console.log('Đã chọn địa chỉ:', data);
                // Bạn có thể mở popup hoặc tab mới với form thanh toán
                window.location.href = '/checkout?use_address=' + addressId;
            });
    }
}
//xác nhận đăng xuất

function confirmLogout() {
    return confirm("Bạn có chắc chắn muốn đăng xuất không?");
}

