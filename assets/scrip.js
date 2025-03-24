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