<script>
// Hàm tải dữ liệu chung (sẽ được gọi từ từng trang)
function loadTable(page = 1, search = '') {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    if (search.trim()) {
        url.searchParams.set('search', search.trim());
    } else {
        url.searchParams.delete('search');
    }
    window.history.replaceState({}, '', url);

    // Tải nội dung bảng + phân trang qua Ajax
    fetch(`${window.location.pathname}_ajax.php?page=${page}&search=${encodeURIComponent(search)}`)
        .then(r => r.json())
        .then(data => {
            document.querySelector('#tableBody').innerHTML = data.html;
            document.querySelector('.pagination').innerHTML = data.pagination;
            document.querySelector('.table-footer div').textContent = 
                `Showing ${data.showing_start} to ${data.showing_end} of ${data.total} results`;
        })
        .catch(() => {
            document.querySelector('#tableBody').innerHTML = 
                '<tr><td colspan="10" style="text-align:center;color:red;">Lỗi tải dữ liệu</td></tr>';
        });
}

// Live search với debounce
let searchTimer;
document.getElementById('globalSearchInput')?.addEventListener('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        loadTable(1, this.value.trim());
    }, 350);
});
</script>