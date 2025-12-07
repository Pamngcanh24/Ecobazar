<?php
include 'includes/header.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Xử lý tìm kiếm và lọc ngày
$search = $_GET['search'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? date('Y-m-d');

// Phân trang
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// Điều kiện WHERE
$where = ["driver_id = ?", "status = 'completed'"];
$params = [$driver_id];
$types = "s";

if ($search !== '') {
    $where[] = "(order_code LIKE ? OR billing_name LIKE ? OR billing_phone LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if ($from !== '') {
    $where[] = "DATE(order_date) >= ?";
    $params[] = $from;
    $types .= "s";
}

if ($to !== '') {
    $where[] = "DATE(order_date) <= ?";
    $params[] = $to;
    $types .= "s";
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Đếm tổng số đơn đã giao
$countSql = "SELECT COUNT(*) as total FROM orders $whereSql";
$countStmt = $conn->prepare($countSql);
if ($params) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$countStmt->close();

// Lấy danh sách đơn hàng đã giao
$sql = "SELECT o.*, u.email as user_email, 
        COALESCE(o.order_date, o.created_at) AS display_date 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        $whereSql
        ORDER BY display_date DESC 
        LIMIT ?, ?";
$offset = $start;
array_push($params, $offset, $limit);
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử giao hàng - Ecobazar Tài Xế</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green: #22c55e;
            --green-dark: #16a34a;
            --gray: #f8fafc;
            --text: #1e293b;
            --text-light: #64748b;
        }
        body { background:#f8fff9; font-family:'Segoe UI',sans-serif; }
        .container { max-width:1300px; margin:20px auto; padding:0 20px; }

        .page-header {
            display:flex; justify-content:space-between; align-items:center;
            margin-bottom:30px; flex-wrap:wrap; gap:15px;
            background:#fff; padding:25px; border-radius:16px;
            box-shadow:0 8px 25px rgba(34,197,94,0.1);
            border:1px solid #dcfce7;
        }
        .page-header h1 {
            font-size:2rem; color:var(--text); display:flex; align-items:center; gap:12px;
        }
        .page-header h1 i {
            font-size:2.4rem; background:linear-gradient(45deg,#22c55e,#86efac);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
        }

        .search-filter {
            background:#fff; padding:20px; border-radius:16px; margin-bottom:25px;
            box-shadow:0 4px 15px rgba(0,0,0,0.05); border:1px solid #dcfce7;
            display:flex; flex-wrap:wrap; gap:15px; align-items:end;
        }
        .form-group { display:flex; flex-direction:column; min-width:200px; }
        .form-group label { font-size:0.9rem; color:var(--text-light); margin-bottom:6px; font-weight:500; }
        .form-group input, .form-group button {
            padding:12px 16px; border-radius:10px; border:2px solid #e2e8f0; font-size:0.95rem;
        }
        .form-group input:focus { outline:none; border-color:var(--green); box-shadow:0 0 0 4px rgba(34,197,94,0.15); }
        .btn-search { background:var(--green); color:white; border:none; cursor:pointer; font-weight:600; }
        .btn-search:hover { background:var(--green-dark); }

        .history-table {
            width:100%; background:#fff; border-collapse:collapse;
            box-shadow:0 8px 25px rgba(0,0,0,0.08); border-radius:16px; overflow:hidden;
        }
        .history-table th {
            background:linear-gradient(135deg,#22c55e,#16a34a); color:white; padding:18px; text-align:left; font-weight:600;
        }
        .history-table td { padding:18px; border-bottom:1px solid #f0fdf4; vertical-align:middle; }
        .history-table tr:hover { background:#f0fdf4; }
        .status-badge { padding:6px 14px; border-radius:30px; font-size:0.85rem; font-weight:600; }
        .status-completed { background:#dcfce7; color:#166534; }

        .customer-info small { display:block; color:var(--text-light); font-size:0.85rem; }
        .total-price { font-size:1.1rem; font-weight:700; color:var(--green); }

        .pagination {
            display:flex; justify-content:center; gap:8px; margin:30px 0;
        }
        .pagination a, .pagination span {
            padding:10px 16px; border-radius:8px; background:#fff; border:1px solid #e2e8f0;
            text-decoration:none; color:var(--text); font-weight:500;
        }
        .pagination a:hover { background:var(--green); color:white; border-color:var(--green); }
        .pagination .active { background:var(--green); color:white; border-color:var(--green); }

        @media (max-width:768px) {
            .search-filter { flex-direction:column; align-items:stretch; }
            .page-header { flex-direction:column; text-align:center; }
            .history-table { font-size:0.9rem; }
            .history-table th, .history-table td { padding:12px 8px; }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Tiêu đề trang -->
    <div class="page-header">
        <h1>
            <i class="fas fa-history"></i> Lịch Sử Giao Hàng
        </h1>
        <div style="color:var(--text-light); font-size:1.1rem;">
            Tổng <strong style="color:var(--green);"><?= $totalRows ?></strong> đơn đã giao thành công
        </div>
    </div>

    <!-- Form tìm kiếm & lọc ngày -->
    <form method="GET" class="search-filter">
        <div class="form-group">
            <label>Tìm kiếm (Mã đơn, tên, số điện thoại)</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nhập từ khóa...">
        </div>
        <div class="form-group">
            <label>Từ ngày</label>
            <input type="date" name="from" value="<?= $from ?>">
        </div>
        <div class="form-group">
            <label>Đến ngày</label>
            <input type="date" name="to" value="<?= $to ?>">
        </div>
        <div class="form-group">
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
        </div>
    </form>

    <!-- Bảng lịch sử giao hàng -->
    <table class="history-table">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Ngày giao</th>
                <th>Số lượng SP</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Thời gian hoàn thành</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr onclick="window.location='dr_order_detail.php?id=<?= $row['id'] ?>'" style="cursor:pointer;">
                    <td><strong><?= htmlspecialchars($row['order_code']) ?></strong></td>
                    <td>
                        <div class="customer-info">
                            <div style="font-weight:600; color:var(--text);"><?= htmlspecialchars($row['billing_name']) ?></div>
                            <small><?= htmlspecialchars($row['billing_phone']) ?></small>
                            <small><?= htmlspecialchars($row['billing_email']) ?></small>
                        </div>
                    </td>
                    <td><?= date('d/m/Y', strtotime($row['display_date'])) ?></td>
                    <td>
                        <?php
                        // Đếm số sản phẩm trong đơn
                        $itemStmt = $conn->prepare("SELECT SUM(quantity) as items FROM order_items WHERE order_id = ?");
                        $itemStmt->bind_param("i", $row['id']);
                        $itemStmt->execute();
                        $items = $itemStmt->get_result()->fetch_assoc()['items'] ?? 0;
                        echo $items . " sản phẩm";
                        $itemStmt->close();
                        ?>
                    </td>
                    <td class="total-price">$<?= number_format($row['total'], 2) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $row['payment_method'])) ?></td>
                    <td>
                        <span class="status-badge status-completed">
                        Completed
                        </span>
                        <br>
                        <small style="color:var(--text-light);">
                            <?= date('H:i d/m/Y', strtotime($row['display_date'])) ?>
                        </small>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:60px; color:var(--text-light);">
                        <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:15px; opacity:0.3;"></i><br>
                        Chưa có đơn hàng nào được giao thành công
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Phân trang -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&from=<?= $from ?>&to=<?= $to ?>"><i class="fas fa-angle-left"></i></a>
        <?php endif; ?>

        <?php
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        for ($i = $startPage; $i <= $endPage; $i++):
        ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&from=<?= $from ?>&to=<?= $to ?>" 
               class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&from=<?= $from ?>&to=<?= $to ?>"><i class="fas fa-angle-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>