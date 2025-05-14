
document.addEventListener("DOMContentLoaded", function() {
    const products = document.querySelectorAll(".product");

    products.forEach(product => {
        // Xử lý click vào sản phẩm
        product.addEventListener("click", function(e) {
            if (!e.target.closest('.heart-icon') && !e.target.closest('.eye-icon') && !e.target.closest('.product-cart-icon')) {
                products.forEach(p => p.classList.remove("selected"));
                this.classList.add("selected");
            }
        });

        // Xử lý click vào icon trái tim
        const heartIcon = product.querySelector('.heart-icon');
        if (heartIcon) {
            heartIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const productId = product.querySelector('a').href.split('id=')[1];
                const icon = this.querySelector('i');

                // Thêm hiệu ứng animation
                icon.style.animation = 'none';
                icon.offsetHeight; // Trigger reflow
                icon.style.animation = 'heartBeat 0.5s';

                // Gửi request AJAX
                fetch('add_to_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    // Hiển thị thông báo
                    const notification = document.createElement('div');
                    notification.className = 'notification';
                    notification.textContent = data.message;
                    document.body.appendChild(notification);

                    // Hiệu ứng hiển thị và ẩn thông báo
                    notification.style.display = 'block';
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        setTimeout(() => {
                            notification.remove();
                        }, 500);
                    }, 2000);

                    // Cập nhật trạng thái icon
                    if (data.success) {
                        if (data.action === 'added') {
                            icon.style.color = '#FF8A00';
                        } else {
                            icon.style.color = '';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Hiển thị thông báo lỗi
                    const notification = document.createElement('div');
                    notification.className = 'notification error';
                    notification.textContent = 'Có lỗi xảy ra, vui lòng thử lại sau';
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.style.opacity = '0';
                        setTimeout(() => {
                            notification.remove();
                        }, 500);
                    }, 2000);
                });
            });
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail-gallery img');
    const mainImage = document.querySelector('.main-image img');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            mainImage.src = this.src;

            // Xóa active ở tất cả thumbnail khác
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            
            // Thêm active vào thumbnail đang click
            this.classList.add('active');
        });
    });
});