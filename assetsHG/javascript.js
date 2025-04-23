
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


document.addEventListener('DOMContentLoaded', function() {
    // Get the current page URL
    const currentPage = window.location.pathname.split('/').pop();

    // Map pages to tab names
    const pageToTabMap = {
        'description.html': 'description',
        'additional-info.html': 'additional-info',
        'customer-feedback.html': 'customer-feedback'
    };

    // Find the corresponding tab for the current page
    const activeTab = pageToTabMap[currentPage] || 'description'; // Default to 'description' if no match

    // Add 'active' class to the corresponding tab
    document.querySelectorAll('.tab').forEach(tab => {
        if (tab.getAttribute('data-tab') === activeTab) {
            tab.classList.add('active');
        }
    });
});