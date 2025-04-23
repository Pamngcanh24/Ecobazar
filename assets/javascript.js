
document.addEventListener("DOMContentLoaded", function() {
    const products = document.querySelectorAll(".product");

    products.forEach(product => {
        product.addEventListener("click", function() {
            // Xóa class 'selected' khỏi tất cả sản phẩm
            products.forEach(p => p.classList.remove("selected"));

            // Thêm class 'selected' vào sản phẩm được click
            this.classList.add("selected");
        });
    });
});
