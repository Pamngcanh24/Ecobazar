<!-- Thanh tìm kiếm chung cho tất cả các trang quản trị -->
<div class="search-box">
    <input 
        type="text" 
        id="globalSearchInput" 
        placeholder="<?= $search_placeholder ?? 'Tìm kiếm nhanh...' ?>" 
        autocomplete="off"
        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
    >
    <i class="fas fa-search search-icon"></i>
    <?php if (!empty($_GET['search'])): ?>
        <a href="<?= remove_url_param('search') ?>" class="search-clear" title="Xóa tìm kiếm">
            <i class="fas fa-times"></i>
        </a>
    <?php endif; ?>
</div>

<?php
// Hàm hỗ trợ xóa param search khỏi URL hiện tại
function remove_url_param($param) {
    $url = $_SERVER['REQUEST_URI'];
    return preg_replace('/[?&]' . $param . '=[^&]+/', '', $url);
}
?>