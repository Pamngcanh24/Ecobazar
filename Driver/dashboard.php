<?php
include 'includes/header.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: driver_login.php");
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Đăng xuất
if (isset($_GET['logout'])) {
    session_unset(); session_destroy();
    setcookie('remember_token', '', time() - 3600, '/');
    header("Location: driver_login.php"); 
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

// Thống kê tổng quan theo khoảng thời gian
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

// Lấy dữ liệu doanh thu theo ngày trong khoảng thời gian
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
        :root {
            --primary: #28a745;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --bg: #f0fdf4;
        }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: #1e293b; margin:0; min-height:100vh; transition: all 0.3s; }
        .dark-mode body { background: #0f172a; color: #e2e8f0; --bg: #0f172a; }

        .container { max-width: 1400px; margin: 20px auto; padding: 0 20px; }

        /* Header */
        .dashboard-header {
            background: rgba(255,255,255,0.97); backdrop-filter: blur(12px);
            padding: 30px; border-radius: 16px; 
            box-shadow: 0 10px 20px #c0e9caff;
            margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
            border: 1px solid #28a745;
        }
        .dashboard-header h1 {
            font-size: 2.4rem; color: var(--primary); margin: 0;
            display: flex; align-items: center;
        }
        .dashboard-header h1 i { font-size: 2.8rem; background: linear-gradient(45deg, #28a745, #22c55e); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .welcome-text { color: #64748b; font-size: 1.1rem; margin-top: 8px; }
        .welcome-text strong { color: var(--primary); font-weight: 600; }

        .header-actions { display: flex; gap: 14px; align-items: center; }

        /* Buttons */
        .btn { padding: 10px 16px; border-radius: 12px; border: none; cursor: pointer; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.95rem; }
        .theme-btn { background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .logout-btn { background: #fee2e2; color: #dc2626; }
        .filter-btn { background: var(--primary); color: white; }
        .reset-btn { background: #6b7280; color: white; }

        /* Filter Form */
        .filter-form {
            background: white; padding: 18px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            margin-bottom: 25px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
        }
        .date-group { display: flex; align-items: center; gap: 10px; }
        .date-group label { font-weight: 500; color: #475569; white-space: nowrap; }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .date-input {
            padding: 10px 12px 10px 38px; border: 2px solid #e2e8f0; border-radius: 10px;
            font-size: 0.95rem; width: 160px; transition: all 0.3s; background: white;
        }
        .date-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px #28a745; }

        /* Stats Grid */
        .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 24px;
        }
        .stat-card {
            background: white; padding: 24px; border-radius: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            position: relative; overflow: hidden; transition: all 0.4s; border: 1px solid #28a745;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 6px; height: 100%; background: var(--primary);
        }
        .stat-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px #28a745; }
        .stat-icon { font-size: 2.6rem; margin-bottom: 14px; opacity: 0.9; }
        .stat-label { color: #64748b; font-size: 0.95rem; margin-bottom: 8px; }
        .stat-value { font-size: 2.2rem; font-weight: 800; color: #1e293b; margin: 0; }
        .stat-value small { font-size: 1rem; font-weight: normal; color: #64748b; }

        /* Charts */
        .charts-grid {
            display: grid; grid-template-columns: 2fr 1fr; gap: 25px;
        }
        .chart-box {
            background: white; padding: 24px; border-radius: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: 1px solid #28a745;
        }
        .chart-box h3 { margin: 0 0 20px; font-size: 1.3rem; color: #1e293b; display: flex; align-items: center; gap: 10px; }
        .chart-box h3 i { color: var(--primary); }

        /* Dark Mode */
        .dark-mode .dashboard-header, .dark-mode .filter-form,
        .dark-mode .stat-card, .dark-mode .chart-box {
            background: #1e293b; border-color: #28a745;
        }
        .dark-mode .stat-value, .dark-mode .chart-box h3 { color: #f1f5f9; }
        .dark-mode .stat-label, .dark-mode .welcome-text { color: #94a3b8; }
        .dark-mode .date-input { background: #334155; border-color: #475569; color: #e2e8f0; }

        @media (max-width: 992px) {
            .charts-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .dashboard-header { flex-direction: column; align-items: flex-start; text-align: center; }
            .header-actions { width: 100%; justify-content: space-between; }
            .filter-form { flex-direction: column; align-items: stretch; }
            .date-input { width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1><i class="fas fa-steering-wheel"></i>Dashboard Tài Xế</h1>
            <p class="welcome-text">Chào mừng trở lại, <strong><?= htmlspecialchars($driver_name) ?></strong>! (ID: <?= $driver_id ?>)</p>
        </div>
        <div class="header-actions">
            <button id="themeToggle" class="btn theme-btn"><i class="fas fa-moon"></i></button>
            <button class="btn logout-btn" onclick="confirmSignOut()"><i class="fas fa-sign-out-alt"></i> Đăng xuất</button>
        </div>
    </div>

    <!-- Filter -->
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
        <button type="submit" class="btn filter-btn"><i class="fas fa-filter"></i> Lọc</button>
         <button type="button" class="btn reset-btn" onclick="resetFilter()">
            <i class="fas fa-rotate-right"></i> Reset
        </button>
    </form>

    <!-- Thống kê từ <?= date('d/m/Y', strtotime($from)) ?> → <?= date('d/m/Y', strtotime($to)) ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="color:#3b82f6"><i class="fas fa-shopping-cart"></i></div>
            <p class="stat-label">Tổng đơn hàng</p>
            <p class="stat-value" data-target="<?= $stats['total_orders'] ?? 0 ?>">0</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#06b6d4"><i class="fas fa-truck"></i></div>
            <p class="stat-label">Đang giao</p>
            <p class="stat-value" data-target="<?= $stats['running'] ?? 0 ?>">0</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#28a745"><i class="fas fa-check-circle"></i></div>
            <p class="stat-label">Đã hoàn thành</p>
            <p class="stat-value" data-target="<?= $stats['completed'] ?? 0 ?>">0</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#ef4444"><i class="fas fa-times-circle"></i></div>
            <p class="stat-label">Đã hủy</p>
            <p class="stat-value" data-target="<?= $stats['cancelled'] ?? 0 ?>">0</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#f59e0b"><i class="fas fa-wallet"></i></div>
            <p class="stat-label">Thu nhập</p>
            <p class="stat-value" data-target="<?= $stats['earnings'] ?? 0 ?>">0<small>$</small></p>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-box">
            <h3><i class="fas fa-chart-line"></i> Doanh thu theo ngày</h3>
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="chart-box">
            <h3><i class="fas fa-chart-pie"></i> Tỷ lệ hoàn thành</h3>
            <canvas id="completionChart"></canvas>
        </div>
    </div>
</div>

<script>
// Animation số đếm
document.querySelectorAll('.stat-value').forEach(el => {
    const target = parseFloat(el.getAttribute('data-target')) || 0;
    let start = 0;
    const increment = target / 90;
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            el.innerHTML = Math.round(target).toLocaleString() + (el.querySelector('small') ? '<small>$</small>' : '');
            clearInterval(timer);
        } else {
            el.innerHTML = Math.floor(start).toLocaleString() + (el.querySelector('small') ? '<small>$</small>' : '');
        }
    }, 20);
});

// Biểu đồ doanh thu
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Doanh thu (đ)',
            data: <?= json_encode($revenue_data) ?>,
            backgroundColor: '#28a745',
            borderWidth: 2,
            borderRadius: 8,
            barThickness: 24,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => 'Doanh thu: ' + ctx.raw.toLocaleString() + '$' } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() + '$' } }
        }
    }
});

// Biểu đồ tròn
new Chart(document.getElementById('completionChart'), {
    type: 'doughnut',
    data: {
        labels: ['Hoàn thành', 'Đã hủy'],
        datasets: [{
            data: [<?= $stats['completed'] ?? 0 ?>, <?= $stats['cancelled'] ?? 0 ?>],
            backgroundColor: ['#28a745', '#ef4444'],
            borderWidth: 4,
            borderColor: '#fff',
            hoverOffset: 15
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 20, font: { size: 14 } } },
            datalabels: {
                color: '#fff',
                font: { weight: 'bold', size: 18 },
                formatter: (value, ctx) => {
                    const total = ctx.dataset.data.reduce((a,b) => a+b, 0);
                    return total > 0 ? Math.round((value/total)*100) + '%' : '';
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
// Reset filter function
function resetFilter() {
    document.querySelector('input[name="from"]').value = '<?php echo $firstDay; ?>';
    document.querySelector('input[name="to"]').value = '<?php echo $today; ?>';
    document.querySelector('.filter-form').submit();
}

// Confirm logout
function confirmSignOut() {
    const overlay = document.createElement('div');
    overlay.style.cssText = `position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);display:flex;justify-content:center;align-items:center;z-index:9999;backdrop-filter:blur(8px);`;
    overlay.innerHTML = `
        <div style="background:white;padding:30px;border-radius:20px;text-align:center;max-width:380px;box-shadow:0 20px 50px rgba(0,0,0,0.3);">
            <div style="font-size:4rem;margin-bottom:15px;">Warning</div>
            <h3>Xác nhận đăng xuất</h3>
            <p>Bạn có chắc chắn muốn đăng xuất?</p>
            <div style="display:flex;gap:15px;justify-content:center;margin-top:25px;">
                <button onclick="window.location.href='?logout=1'" style="padding:10px 20px;background:#ef4444;color:white;border:none;border-radius:12px;cursor:pointer;">Đăng xuất</button>
                <button onclick="this.closest('[style]').parentElement.remove()" style="padding:10px 20px;background:#e2e8f0;color:#475569;border:none;border-radius:12px;cursor:pointer;">Hủy</button>
            </div>
        </div>`;
    document.body.appendChild(overlay);
}
</script>

<?php include 'includes/footer.php'; ?>