<?php
require_once 'includes/connect.php'; // hoặc file kết nối DB của bạn

$limit = 6;
$page = max(1, intval($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$start = ($page - 1) * $limit;

$where = '';
$params = [];
$types = '';
if ($search !== '') {
    $where = "WHERE p.name LIKE ?";
    $params[] = "%$search%";
    $types = 's';
}

$countSql = "SELECT COUNT(*) FROM products p $where";
$stmt = $conn->prepare($countSql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$totalRows = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where 
        ORDER BY p.id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$params[] = $start; $params[] = $limit;
$types .= 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

ob_start();
while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['id']) ?></td>
    <td>
        <?php if ($row['image']): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Product" style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
        <?php else: ?>
            <div style="width:60px;height:60px;background:#eee;border-radius:4px;"></div>
        <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= number_format($row['price'], 0, ',', '.') ?> $</td>
    <td><?= htmlspecialchars($row['category_name'] ?? 'N/A') ?></td>
    <td><?= htmlspecialchars($row['stock']) ?></td>
    <td>
        <a href="#" onclick="showConfirmModal('product.php?delete_id=<?= $row['id'] ?>&page=<?= $page ?>&search=<?= urlencode($search) ?>');return false;" class="delete-link">
            <i class="fas fa-trash-alt"></i> Delete
        </a>
        <a href="product_edit.php?id=<?= $row['id'] ?>" class="edit-link">
            <i class="fas fa-edit"></i> Edit
        </a>
    </td>
</tr>
<?php endwhile;
$html = ob_get_clean();
if (!$result->num_rows) {
    $html = '<tr><td colspan="7" style="text-align:center;padding:50px;color:#888;">Không tìm thấy sản phẩm nào</td></tr>';
}

// Phân trang
ob_start();
if ($page > 1): ?>
    <a href="#" onclick="loadProducts(<?= $page-1 ?>, '<?= htmlspecialchars($search, ENT_QUOTES) ?>');return false;" class="page-item"><i class="fa-solid fa-angle-left"></i></a>
<?php else: ?>
    <span class="page-item disabled"><i class="fa-solid fa-angle-left"></i></span>
<?php endif;

for ($i = 1; $i <= $totalPages; $i++):
    if ($i == $page): ?>
        <span class="page-item active"><?= $i ?></span>
    <?php else: ?>
        <a href="#" onclick="loadProducts(<?= $i ?>, '<?= htmlspecialchars($search, ENT_QUOTES) ?>');return false;" class="page-item"><?= $i ?></a>
    <?php endif;
endfor;

if ($page < $totalPages): ?>
    <a href="#" onclick="loadProducts(<?= $page+1 ?>, '<?= htmlspecialchars($search, ENT_QUOTES) ?>');return false;" class="page-item"><i class="fa-solid fa-angle-right"></i></a>
<?php else: ?>
    <span class="page-item disabled"><i class="fa-solid fa-angle-right"></i></span>
<?php endif;
$pagination = ob_get_clean();

echo json_encode([
    'html' => $html,
    'pagination' => $pagination,
    'total' => $totalRows,
    'showing_start' => $totalRows ? $start + 1 : 0,
    'showing_end' => min($start + $limit, $totalRows)
]);