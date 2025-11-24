<?php
include 'includes/header.php';

// [Giữ nguyên toàn bộ PHP xử lý session, logout, filter, thống kê...]
$admin_username = $_SESSION['admin_username'];

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    if (isset($_COOKIE['admin_remember_token'])) {
        setcookie('admin_remember_token', '', time() - 3600, '/');
    }
    header("Location: login.php");
    exit;
}

// Set default dates
$firstDay = date('Y-m-01', strtotime('now')); // Default to first day of current month (2025-11-01)
$today = date('Y-m-d', strtotime('now'));     // Default to current date (2025-11-16)
$from = $_GET['from'] ?? $firstDay;
$to = $_GET['to'] ?? $today;

$where = [];
if ($from) $where[] = "created_at >= '$from 00:00:00'";
if ($to) $where[] = "created_at <= '$to 23:59:59'";
$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$totalOrders = $conn->query("SELECT COUNT(*) as total FROM orders $whereSql")->fetch_assoc()['total'];
$statusQuery = $conn->query("
    SELECT 
        SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status='processing' THEN 1 ELSE 0 END) as processing,
        SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM orders $whereSql
");
$status = $statusQuery->fetch_assoc();
$completedOrders = $status['completed'] ?? 0;
$cancelledOrders = $status['cancelled'] ?? 0;
?>

<main class="main-content">
    <div class="dashboard-header">
        <div>
            <h1>
                <i class="fas fa-tachometer-alt" style="color: #4CAF50;"></i>
                Dashboard
            </h1>
            <p class="welcome-text">Chào mừng quay lại, <strong><?= htmlspecialchars($admin_username) ?></strong>! Đây là tổng quan hoạt động Ecobazar.</p>
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
        <button type="submit" class="filter-btn">
            <i class="fas fa-filter"></i> Lọc
        </button>
        <button type="button" class="reset-btn" onclick="resetFilter()">
            <i class="fas fa-rotate-right"></i> Reset
        </button>
    </form>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card" data-color="blue">
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-content">
                <p class="stat-label">Tổng đơn hàng</p>
                <p class="stat-value" id="totalOrders">0</p>
            </div>
        </div>
        <div class="stat-card" data-color="amber">
            <div class="stat-icon"><i class="fas fa-hourglass-start"></i></div>
            <div class="stat-content">
                <p class="stat-label">Đang chờ</p>
                <p class="stat-value" id="pending">0</p>
            </div>
        </div>
        <div class="stat-card" data-color="cyan">
            <div class="stat-icon"><i class="fas fa-cog"></i></div>
            <div class="stat-content">
                <p class="stat-label">Đang xử lý</p>
                <p class="stat-value" id="processing">0</p>
            </div>
        </div>
        <div class="stat-card" data-color="green">
            <div class="stat-icon"><i class="fas fa-check"></i></div>
            <div class="stat-content">
                <p class="stat-label">Hoàn thành</p>
                <p class="stat-value" id="completed">0</p>
            </div>
        </div>
        <div class="stat-card" data-color="red">
            <div class="stat-icon"><i class="fas fa-times"></i></div>
            <div class="stat-content">
                <p class="stat-label">Đã hủy</p>
                <p class="stat-value" id="cancelled">0</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-box">
            <h3>Thống kê trạng thái đơn hàng</h3>
            <canvas id="orderChart"></canvas>
        </div>
        <div class="chart-box">
            <h3>Tỷ lệ hoàn thành / hủy</h3>
            <canvas id="deliveryChart"></canvas>
        </div>
    </div>
</main>

<!-- Chart.js + Plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
// Animation số đếm
function animateValue(id, end) {
    const obj = document.getElementById(id);
    let start = 0;
    if (end === 0) { obj.textContent = '0'; return; }
    const duration = 1200;
    const increment = end / (duration / 16);
    const timer = setInterval(() => {
        start += increment;
        obj.textContent = Math.floor(start).toLocaleString();
        if (start >= end) {
            obj.textContent = end.toLocaleString();
            clearInterval(timer);
        }
    }, 16);
}

// Reset filter function
function resetFilter() {
    document.querySelector('input[name="from"]').value = '<?php echo $firstDay; ?>';
    document.querySelector('input[name="to"]').value = '<?php echo $today; ?>';
    document.querySelector('.filter-form').submit();
}

// Gọi animation
animateValue('totalOrders', <?= $totalOrders ?>);
animateValue('pending', <?= $status['pending'] ?? 0 ?>);
animateValue('processing', <?= $status['processing'] ?? 0 ?>);
animateValue('completed', <?= $status['completed'] ?? 0 ?>);
animateValue('cancelled', <?= $status['cancelled'] ?? 0 ?>);

// Biểu đồ đường
new Chart(document.getElementById('orderChart'), {
    type: 'line',
    data: {
        labels: ['Đang chờ', 'Xử lý', 'Hoàn thành', 'Hủy'],
        datasets: [{
            label: 'Số lượng',
            data: [<?= $status['pending'] ?? 0 ?>, <?= $status['processing'] ?? 0 ?>, <?= $status['completed'] ?? 0 ?>, <?= $status['cancelled'] ?? 0 ?>],
            borderColor: '#4CAF50',
            backgroundColor: 'rgba(76, 175, 80, 0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#4CAF50',
            pointBorderColor: '#fff',
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: 'rgba(0,0,0,0.8)', cornerRadius: 8 }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        },
        animation: { duration: 1500, easing: 'easeOutQuart' }
    }
});

// Biểu đồ tròn nhỏ gọn với text được căn giữa
new Chart(document.getElementById('deliveryChart'), {
    type: 'doughnut',
    data: {
        labels: ['Hoàn thành', 'Đã hủy'],
        datasets: [{
            data: [<?= $completedOrders ?>, <?= $cancelledOrders ?>],
            backgroundColor: ['#4CAF50', '#FF3B30'],
            borderColor: '#fff',
            borderWidth: 2,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        cutout: '50%',
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 15, font: { size: 12 } } },
            datalabels: {
                color: '#fff',
                font: { weight: 'bold', size: 12 },
                anchor: 'center', // Căn giữa text trong từng segment
                align: 'center',  // Đảm bảo text nằm gọn trong vùng
                clamp: true,      // Giới hạn text trong kích thước chart
                formatter: (value, ctx) => {
                    const sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                    return sum > 0 ? ((value / sum) * 100).toFixed(1) + '%' : '0%';
                }
            },
            tooltip: {
                callbacks: {
                    label: ctx => `${ctx.label}: ${ctx.raw} đơn`
                }
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    },
    plugins: [ChartDataLabels]
});

// Dark mode toggle
document.getElementById('themeToggle').addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    document.querySelector('#themeToggle i').classList.toggle('fa-moon');
    document.querySelector('#themeToggle i').classList.toggle('fa-sun');
});

// Load theme
if (localStorage.getItem('theme') === 'dark' || 
    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.body.classList.add('dark-mode');
    document.querySelector('#themeToggle i').classList.replace('fa-moon', 'fa-sun');
}

// Confirm sign out
function confirmSignOut() {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.innerHTML = `
        <div class="modal">
            <div class="modal-icon">⚠️</div>
            <h3>Xác nhận đăng xuất</h3>
            <p>Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?</p>
            <div class="modal-actions">
                <button onclick="window.location.href='dashboard.php?logout=1'" class="btn-danger">Đăng xuất</button>
                <button onclick="this.closest('.modal-overlay').remove()" class="btn-secondary">Hủy</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
}
</script>

<style>
/* Reset & Base */
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f7fa;
    color: #2d3748;
    line-height: 1.6;
    transition: all 0.3s ease;
}
.dark-mode body { background: #0f172a; color: #e2e8f0; }

/* Layout */
.main-content { padding: 20px; max-width: 1400px; margin: 0 auto; }

/* Header */
.dashboard-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;
    background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    min-height: 120px; /* Increased height for a larger header */
    box-shadow: 0 10px 20px #c0e9caff;
    border: 1px solid #28a745;

}
.dashboard-header h1 { 
    font-size: 2rem; /* Increased font size for title */
    color: #2d3748; 
    display: flex; 
    align-items: center; 
    gap: 15px; /* Increased gap between icon and text */
}
.dashboard-header h1 i { font-size: 1.5rem; /* Increased icon size */ }
.welcome-text { 
    color: #64748b; 
    font-size: 1rem; /* Increased font size for welcome text */
    margin-top: 8px; /* Adjusted spacing */
}
.header-actions { display: flex; gap: 12px; align-items: center; }

/* Buttons */
.theme-btn, .sign-out-btn, .filter-btn, .btn-danger, .btn-secondary, .reset-btn {
    padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.3s;
    display: flex; align-items: center; gap: 6px; font-size: 0.9rem;
}
.theme-btn { background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.theme-btn:hover { background: #fff; transform: translateY(-1px); }
.sign-out-btn { background: #fee2e2; color: #dc2626; }
.sign-out-btn:hover { background: #fecaca; }
.filter-btn { background: #4CAF50; color: #fff; padding: 8px 16px; }
.filter-btn:hover { background: #45a049; transform: translateY(-1px); }
.btn-danger { background: #F44336; color: #fff; }
.btn-secondary { background: #e2e8f0; color: #475569; }
.btn-danger:hover, .btn-secondary:hover { opacity: 0.9; transform: translateY(-1px); }
.reset-btn {
    background: #6c757d;
    color: #fff;
}
.reset-btn:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

/* Filter */
.filter-form {
    display: flex; align-items: center; gap: 15px; margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.date-group {
    display: flex; align-items: center; gap: 8px; font-size: 0.95rem; color: #64748b;
}
.date-group label { white-space: nowrap; font-weight: 500; }
.input-group {
    position: relative; display: flex; align-items: center;
}
.input-group i { 
    position: absolute; 
    left: 12px; 
    color: #94a3b8; 
    font-size: 1rem; 
    transition: color 0.3s ease;
}
.date-input {
    padding: 10px 12px 10px 36px; /* Increased padding for better spacing */
    border: 2px solid #e2e8f0; /* Thicker border for a modern look */
    border-radius: 8px; /* Rounded corners */
    background: #fff;
    font-size: 0.95rem;
    width: 160px; /* Slightly wider */
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.date-input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 0 4px rgba(76,175,80,0.2);
}
.date-input:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.date-input::-webkit-calendar-picker-indicator {
    opacity: 0.6;
    cursor: pointer;
}
.date-input::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Stats Grid */
.stats-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 24px;
}
.stat-card {
    background: #fff; padding: 12px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.06); 
    display: flex; align-items: center; gap: 12px; transition: all 0.3s; position: relative; overflow: hidden;
}
.stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; transition: all 0.3s;
}
.stat-card[data-color="blue"]::before { background: #2196F3; }
.stat-card[data-color="amber"]::before { background: #FFC107; }
.stat-card[data-color="cyan"]::before { background: #00BCD4; }
.stat-card[data-color="green"]::before { background: #4CAF50; }
.stat-card[data-color="red"]::before { background: #F44336; }
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.stat-icon { font-size: 1.5rem; }
.stat-label { color: #64748b; font-size: 0.8rem; margin-bottom: 2px; }
.stat-value { font-size: 1.2rem; font-weight: 700; color: #1e293b; }

/* Charts */
.charts-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;
}
.chart-box {
    background: #fff; padding: 34px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    height: 300px; /* Fixed height to align charts */
    border: 1px solid #28a745;
}
.chart-box h3 { margin-bottom: 12px; color: #1e293b; font-size: 1rem; font-weight: 600; }

/* Modal */
.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);
    display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(5px);
}
.modal {
    background: #fff; padding: 24px; border-radius: 8px; text-align: center; width: 90%; max-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.modal-icon { font-size: 2.5rem; margin-bottom: 12px; }
.modal h3 { margin-bottom: 10px; color: #1e293b; }
.modal p { color: #64748b; margin-bottom: 20px; }
.modal-actions { display: flex; gap: 10px; justify-content: center; }

/* Dark Mode */
.dark-mode .main-content, .dark-mode .dashboard-header, .dark-mode .filter-form, .dark-mode .stat-card, .dark-mode .chart-box { background: #1e293b; }
.dark-mode .welcome-text, .dark-mode .date-group, .dark-mode .stat-label, .dark-mode .modal p { color: #94a3b8; }
.dark-mode .date-input, .dark-mode .theme-btn { 
    background: #334155; 
    border-color: #475569; 
    color: #e2e8f0;
}
.dark-mode .date-input:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 0 4px rgba(76,175,80,0.2);
}
.dark-mode .date-input:hover {
    border-color: #64748b;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}
.dark-mode .input-group i { color: #94a3b8; }
.dark-mode .stat-value, .dark-mode .chart-box h3 { color: #f1f5f9; }
.dark-mode .dashboard-header { box-shadow: 0 4px 8px rgba(0,0,0,0.3); }
.dark-mode .filter-form { box-shadow: 0 2px 4px rgba(0,0,0,0.3); }
.dark-mode .stat-card { box-shadow: 0 2px 4px rgba(0,0,0,0.3); }
.dark-mode .chart-box { box-shadow: 0 2px 4px rgba(0,0,0,0.3); }

/* Responsive */
@media (max-width: 768px) {
    .dashboard-header { flex-direction: column; align-items: flex-start; min-height: auto; padding: 15px; }
    .dashboard-header h1 { font-size: 1.5rem; }
    .header-actions { width: 100%; justify-content: space-between; }
    .filter-form { flex-direction: column; gap: 10px; }
    .stats-grid { grid-template-columns: 1fr; }
    .charts-grid { grid-template-columns: 1fr; }
    .chart-box { height: 250px; } /* Adjusted height for mobile */
}
</style>

<?php include 'includes/footer.php'; ?>