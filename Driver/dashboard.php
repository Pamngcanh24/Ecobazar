<?php
include 'includes/header.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Đăng xuất
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/');
    header("Location: login.php");
    exit;
}

// Lấy tên tài xế
$stmt = $conn->prepare("SELECT name FROM drivers WHERE id = ?");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$driver_name = $stmt->get_result()->fetch_assoc()['name'] ?? 'Tài xế';
$stmt->close();

// Thiết lập ngày mặc định
$firstDay = date('Y-m-01');
$today = date('Y-m-d');
$from = $_GET['from'] ?? $firstDay;
$to = $_GET['to'] ?? $today;

// Xây dựng điều kiện WHERE
$where = ["driver_id = ?"];
$params = [$driver_id];
$types = "s";

if ($from) {
    $where[] = "DATE(order_date) >= ?";
    $params[] = $from;
    $types .= "s";
}
if ($to) {
    $where[] = "DATE(order_date) <= ?";
    $params[] = $to;
    $types .= "s";
}
$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Thống kê tổng quan
$query = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as running,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
    COALESCE(SUM(total), 0) as earnings
    FROM orders $whereSql";

$stmt = $conn->prepare($query);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Doanh thu theo ngày
$revenue_data = [];
$labels = [];

$start = new DateTime($from);
$end = new DateTime($to);
$interval = new DateInterval('P1D');
$period = new DatePeriod($start, $interval, $end->modify('+1 day'));

foreach ($period as $date) {
    $dateStr = $date->format('Y-m-d');
    $labels[] = $date->format('d/m');

    $stmt = $conn->prepare("SELECT COALESCE(SUM(total),0) as earn FROM orders WHERE driver_id=? AND status='completed' AND DATE(order_date)=?");
    $stmt->bind_param("ss", $driver_id, $dateStr);
    $stmt->execute();
    $revenue_data[] = (int)$stmt->get_result()->fetch_assoc()['earn'];
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tài Xế - Ecobazar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <style>
        /* === RESET & BASE === */
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
            color: #2d3748;
            line-height: 1.6;
            transition: all 0.3s ease;
        }
        .dark-mode body { background: #0f172a; color: #e2e8f0; }

        .container { max-width: 1400px; margin: 20px auto; padding: 0 20px; }

        /* === HEADER === */
        .dashboard-header {
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 20px; flex-wrap: wrap; gap: 16px;
            background: #fff; padding: 25px; border-radius: 10px; 
            box-shadow: 0 10px 20px #c0e9caff;
            min-height: 120px;
            border: 1px solid #28a745;
        }
        .dashboard-header h1 { 
            font-size: 2rem; color: #2d3748; 
            display: flex; align-items: center; gap: 15px; 
        }
        .dashboard-header h1 i { 
            font-size: 2.4rem; 
            background: linear-gradient(45deg, #28a745, #22c55e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .welcome-text { 
            color: #64748b; font-size: 1rem; margin-top: 8px; 
        }
        .welcome-text strong { color: #28a745; font-weight: 600; }

        .header-actions { display: flex; gap: 12px; align-items: center; }

        /* === BUTTONS === */
        .theme-btn, .sign-out-btn, .filter-btn, .reset-btn {
            padding: 10px 16px; border: none; border-radius: 8px; cursor: pointer; 
            font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px;
        }
        .theme-btn {
            background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .theme-btn:hover { background: #fff; transform: translateY(-2px); }

        .sign-out-btn {
            background: #fee2e2; color: #dc2626;
            box-shadow: 0 2px 6px rgba(220,38,38,0.15);
        }
        .sign-out-btn:hover {
            background: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(220,38,38,0.25);
        }

        .filter-btn { background: #4CAF50; color: #fff; }
        .filter-btn:hover { background: #45a049; transform: translateY(-2px); }
        .reset-btn { background: #6c757d; color: #fff; }
        .reset-btn:hover { background: #5a6268; transform: translateY(-2px); }

        /* === FILTER FORM === */
        .filter-form {
            display: flex; align-items: center; gap: 15px; margin-bottom: 20px; 
            background: #fff; padding: 15px; border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .date-group { display: flex; align-items: center; gap: 8px; }
        .date-group label { font-weight: 500; color: #64748b; white-space: nowrap; }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .date-input {
            padding: 10px 12px 10px 38px; border: 2px solid #e2e8f0; border-radius: 8px;
            background: #fff; font-size: 0.95rem; width: 160px; transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .date-input:focus {
            outline: none; border-color: #4CAF50;
            box-shadow: 0 0 0 4px rgba(76,175,80,0.2);
        }

        /* === STATS GRID === */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 24px;
        }
        .stat-card {
            background: #fff; padding: 20px; border-radius: 10px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); position: relative; overflow: hidden;
            border: 1px solid #28a745; transition: all 0.3s;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 5px; height: 100%;
            background: #28a745;
        }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 12px 25px rgba(40,167,69,0.25); }
        .stat-icon { font-size: 2.4rem; margin-bottom: 12px; }
        .stat-label { color: #64748b; font-size: 0.9rem; }
        .stat-value { font-size: 2rem; font-weight: 800; color: #1e293b; }

        /* === CHARTS === */
        .charts-grid {
            display: grid; grid-template-columns: 2fr 1fr; gap: 25px;
        }
        .chart-box {
            background: #fff; padding: 55px; border-radius: 10px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            height: 380px; border: 1px solid #28a745;
        }
        .chart-box h3 { margin-bottom: 20px; color: #1e293b; font-size: 1.2rem; }

        /* === MODAL === */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;
            z-index: 1000; backdrop-filter: blur(5px);
        }
        .modal {
            background: center; background: #fff; padding: 28px; border-radius: 12px;
            width: 90%; max-width: 380px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .modal-icon { font-size: 3.5rem; margin-bottom: 16px; }
        .modal h3 { margin-bottom: 12px; color: #1e293b; }
        .modal p { color: #64748b; margin-bottom: 24px; }
        .modal-actions { display: flex; gap: 12px; justify-content: center; }
        .btn-danger, .btn-secondary {
            padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;
        }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        .btn-danger:hover { background: #dc2626; }
        .btn-secondary:hover { background: #cbd5e1; }

        /* === DARK MODE === */
        .dark-mode {
            background: #0f172a; color: #e2e8f0;
        }
        .dark-mode .dashboard-header,
        .dark-mode .filter-form,
        .dark-mode .stat-card,
        .dark-mode .chart-box,
        .dark-mode .modal { background: #1e293b; }
        .dark-mode .welcome-text,
        .dark-mode .stat-label { color: #94a3b8; }
        .dark-mode .stat-value { color: #f1f5f9; }
        .dark-mode .date-input { background: #334155; border-color: #475569; color: #e2e8f0; }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .dashboard-header { flex-direction: column; align-items: flex-start; text-align: center; }
            .filter-form { flex-direction: column; align-items: stretch; }
            .charts-grid { grid-template-columns: 1fr; }
            .chart-box { height: 300px; }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1>
                <i class="fas fa-steering-wheel"></i> Dashboard Tài Xế
            </h1>
            <p class="welcome-text">Chào mừng trở lại, <strong><?= htmlspecialchars($driver_name) ?></strong>! (ID: <?= $driver_id ?>)</p>
        </div>
        <div class="header-actions">
            <button id="themeToggle" class="theme-btn" title="Chuyển chế độ sáng/tối">
                <i class="fas fa-moon"></i>
            </button>
            <button class="sign-out-btn" onclick="confirmSignOut()">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="filter-form">
        <div class="date-group">
            <label>Từ ngày:</label>
            <div class="input-group">
                <i class="fas fa-calendar-alt"></i>
                <input type="date" name="from" value="<?= $from ?>" class="date-input">
            </div>
        </div>
        <div class="date-group">
            <label>Đến ngày:</label>
            <div class="input-group">
                <i class="fas fa-calendar-alt"></i>
                <input type="date" name="to" value="<?= $to ?>" class="date-input">
            </div>
        </div>
        <button type="submit" class="filter-btn">Lọc</button>
        <button type="button" class="reset-btn" onclick="resetFilter()">Reset</button>
    </form>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="color:#3b82f6"><i class="fas fa-shopping-cart"></i></div>
            <p class="stat-label">Tổng đơn hàng</p>
            <p class="stat-value" id="totalOrders">0</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#06b6d4"><i class="fas fa-truck"></i></div>
            <p class="stat-label">Đang giao</p>
            <p class="stat-value" id="running">0</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#28a745"><i class="fas fa-check-circle"></i></div>
            <p class="stat-label">Đã hoàn thành</p>
            <p class="stat-value" id="completed">0</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#ef4444"><i class="fas fa-times-circle"></i></div>
            <p class="stat-label">Đã hủy</p>
            <p class="stat-value" id="cancelled">0</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#f59e0b"><i class="fas fa-wallet"></i></div>
            <p class="stat-label">Thu nhập</p>
            <p class="stat-value" id="earnings">0</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-box">
            <h3>Doanh thu theo ngày</h3>
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="chart-box">
            <h3>Tỷ lệ hoàn thành</h3>
            <canvas id="completionChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
// Animation số đếm
function animateValue(id, end) {
    const obj = document.getElementById(id);
    let start = 0;
    if (end == 0) { obj.textContent = '0'; return; }
    const duration = 1200;
    const increment = end / (duration / 16);
    const timer = setInterval(() => {
        start += increment;
        if (start >= end) {
            obj.textContent = Math.floor(end).toLocaleString();
            if (id === 'earnings') obj.textContent += ' $';
            clearInterval(timer);
        } else {
            obj.textContent = Math.floor(start).toLocaleString();
            if (id === 'earnings') obj.textContent += ' $';
        }
    }, 16);
}

animateValue('totalOrders', <?= $stats['total_orders'] ?? 0 ?>);
animateValue('running', <?= $stats['running'] ?? 0 ?>);
animateValue('completed', <?= $stats['completed'] ?? 0 ?>);
animateValue('cancelled', <?= $stats['cancelled'] ?? 0 ?>);
animateValue('earnings', <?= $stats['earnings'] ?? 0 ?>);

// Biểu đồ doanh thu
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Doanh thu',
            data: <?= json_encode($revenue_data) ?>,
            backgroundColor: '#28a745',
            borderRadius: 8,
            barThickness: 20,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Biểu đồ tròn
new Chart(document.getElementById('completionChart'), {
    type: 'doughnut',
    data: {
        labels: ['Hoàn thành', 'Đã hủy'],
        datasets: [{
            data: [<?= $stats['completed'] ?>, <?= $stats['cancelled'] ?>],
            backgroundColor: ['#28a745', '#ef4444'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 12
        }]
    },
    options: {
        cutout: '60%',
        plugins: {
            legend: { position: 'bottom' },
            datalabels: {
                color: '#fff',
                font: { weight: 'bold', size: 16 },
                formatter: (v, ctx) => {
                    const sum = ctx.dataset.data.reduce((a,b)=>a+b,0);
                    return sum > 0 ? Math.round((v/sum)*100) + '%' : '';
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});

// Dark mode
document.getElementById('themeToggle').addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    document.querySelector('#themeToggle i').classList.toggle('fa-moon');
    document.querySelector('#themeToggle i').classList.toggle('fa-sun');
});
if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.body.classList.add('dark-mode');
    document.querySelector('#themeToggle i').classList.replace('fa-moon', 'fa-sun');
}

// Reset filter
function resetFilter() {
    document.querySelector('input[name="from"]').value = '<?= $firstDay ?>';
    document.querySelector('input[name="to"]').value = '<?= $today ?>';
    document.querySelector('.filter-form').submit();
}

// Confirm logout - giống hệt admin
function confirmSignOut() {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.innerHTML = `
        <div class="modal">
            <center><div class="modal-icon">⚠️</div>
            <h3>Xác nhận đăng xuất</h3>
            <p>Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?</p></center>
            <div class="modal-actions">
                <button onclick="window.location.href='dashboard.php?logout=1'" class="btn-danger">Đăng xuất</button>
                <button onclick="this.closest('.modal-overlay').remove()" class="btn-secondary">Hủy</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
}
</script>

<?php include 'includes/footer.php'; ?>