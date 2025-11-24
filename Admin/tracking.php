<?php
include 'includes/header.php';

// Lấy mã đơn từ URL
$order_code = trim($_GET['code'] ?? '');
$order = null;
$tracking_steps = [];

if ($order_code !== '') {
    $order_code_esc = $conn->real_escape_string($order_code);

    // Lấy thông tin đơn hàng + tài xế
    $sql = "SELECT o.*, 
                   d.name AS driver_name, 
                   d.phone AS driver_phone,
                   d.avatar AS driver_avatar
            FROM orders o
            LEFT JOIN drivers d ON o.driver_id = d.id
            WHERE o.order_code = '$order_code_esc' 
            LIMIT 1";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();

        $status = strtolower($order['status']);
        $order_time = date('d/m/Y H:i', strtotime($order['order_date']));
        $now = date('d/m/Y H:i');

        // Timeline hành trình (tự động theo trạng thái)
        $tracking_steps = [
            ['icon' => 'shopping-bag', 'label' => 'Đặt hàng thành công',          'time' => $order_time, 'done' => true],
            ['icon' => 'package',      'label' => 'Đang xử lý đơn hàng',         'time' => $order_time, 'done' => in_array($status, ['processing','confirmed','preparing','shipped','delivered','completed'])],
            ['icon' => 'check-circle', 'label' => 'Đã xác nhận đơn hàng',        'time' => $order_time, 'done' => in_array($status, ['confirmed','preparing','shipped','delivered','completed'])],
            ['icon' => 'truck',        'label' => 'Đã giao cho shipper',          'time' => $order_time, 'done' => in_array($status, ['shipped','delivered','completed'])],
            ['icon' => 'map-pin',      'label' => 'Shipper đang giao',            'time' => $order_time, 'done' => in_array($status, ['delivered','completed'])],
            ['icon' => 'gift',         'label' => 'Giao hàng thành công',         'time' => $now,        'done' => in_array($status, ['delivered','completed'])]
        ];

        // Nếu bị hủy
        if ($status === 'cancelled') {
            $tracking_steps = [
                ['icon' => 'shopping-bag', 'label' => 'Đặt hàng thành công', 'time' => $order_time, 'done' => true],
                ['icon' => 'x-circle',     'label' => 'Đơn hàng đã bị hủy',   'time' => $now,        'done' => true, 'cancelled' => true]
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo dõi vận đơn #<?= htmlspecialchars($order_code) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #28a745;
            --primary-dark: #218838;
            --primary-light: #34ce57;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-600: #6c757d;
            --gray-800: #343a40;
            --gray-900: #212529;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f1f8e9 0%, #d4edda 100%); 
            min-height:100vh; 
            line-height: 1.6;
        }

        .tracking-wrapper { max-width: 1100px; margin: 0 auto; padding: 30px 20px; }

        /* Header */
        .tracking-header {
            text-align: center;
            margin-bottom: 50px;
            color: var(--gray-900);
        }
        .tracking-header h1 {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(90deg, var(--primary-dark), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }
        .tracking-header p {
            font-size: 18px;
            color: var(--gray-600);
        }

        /* Search Card */
        .search-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(40,167,69,0.15);
            margin-bottom: 50px;
            border: 1px solid rgba(40,167,69,0.1);
            transition: transform 0.3s ease;
        }
        .search-card:hover { transform: translateY(-5px); }

        .search-form {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        .search-form input {
            width: 100%;
            height: 68px;
            padding: 0 80px 0 28px;
            border: 3px solid transparent;
            border-radius: 20px;
            font-size: 18px;
            font-weight: 500;
            background: #f8fff9;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .search-form input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 8px rgba(40,167,69,0.15);
            transform: translateY(-3px);
        }
        .search-form button {
            position: absolute;
            right: 8px;
            top: 8px;
            width: 52px;
            height: 52px;
            border: none;
            border-radius: 16px;
            background: var(--primary);
            color: white;
            font-size: 22px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(40,167,69,0.4);
        }
        .search-form button:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        /* Order Card */
        .order-card {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(40,167,69,0.18);
            border: 1px solid rgba(40,167,69,0.1);
            animation: fadeInUp 0.8s ease;
        }
        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(40px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .order-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .order-code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .order-total {
            font-size: 24px;
            font-weight: 700;
        }
        .order-status {
            padding: 10px 28px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(255,255,255,0.25);
            display: inline-block;
        }

        /* Timeline */
        .timeline-container {
            padding: 50px 60px;
            position: relative;
        }
        .timeline-container::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            border-radius: 3px;
            transform: translateX(-50%);
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 60px;
            position: relative;
        }
        .timeline-item:last-child { margin-bottom: 0; }

        .timeline-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            z-index: 2;
            box-shadow: 0 15px 40px rgba(40,167,69,0.3);
            border: 6px solid var(--primary);
            color: var(--primary);
            flex-shrink: 0;
            transition: all 0.4s ease;
        }
        .timeline-item.done .timeline-icon {
            background: var(--primary);
            color: white;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(40,167,69,0.4); }
            70% { box-shadow: 0 0 0 20px rgba(40,167,69,0); }
            100% { box-shadow: 0 0 0 0 rgba(40,167,69,0); }
        }
        .timeline-item.cancelled .timeline-icon {
            background: var(--danger);
            border-color: var(--danger);
            color: white;
        }

        .timeline-content {
            flex: 1;
            padding: 28px 40px;
            background: #f8fff9;
            border-radius: 24px;
            margin: 0 30px;
            border: 2px solid transparent;
            transition: all 0.4s ease;
        }
        .timeline-item.done .timeline-content {
            background: linear-gradient(135deg, #e6f9eb, #d4f1d9);
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(40,167,69,0.15);
        }
        .timeline-content h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
        }
        .timeline-content p {
            color: var(--gray-600);
            font-size: 16px;
            font-weight: 500;
        }

        /* Driver Section */
        .driver-section {
            background: linear-gradient(135deg, #f8fff9, #e6f9eb);
            padding: 40px;
            border-top: 3px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        .driver-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            flex-shrink: 0;
        }
        .driver-info h4 {
            font-size: 22px;
            color: var(--gray-900);
            margin-bottom: 8px;
        }
        .driver-info p {
            color: var(--gray-600);
            font-size: 18px;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 100px 40px;
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 60px rgba(40,167,69,0.12);
            margin: 20px 0;
        }
        .empty-state i {
            font-size: 120px;
            color: var(--primary);
            margin-bottom: 30px;
            opacity: 0.8;
        }
        .empty-state h3 {
            font-size: 32px;
            color: var(--gray-900);
            margin-bottom: 16px;
        }
        .empty-state p {
            font-size: 18px;
            color: var(--gray-600);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* Footer Note */
        .footer-note {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: var(--gray-600);
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .timeline-container::before { left: 50px; }
            .timeline-item { flex-direction: column; align-items: flex-start; }
            .timeline-content { margin: 20px 0 0 100px; width: calc(100% - 120px); }
            .timeline-icon { position: absolute; left: 0; }
            .order-header { flex-direction: column; text-align: center; }
            .search-card, .order-card { padding: 30px 20px; }
        }

        @media (max-width: 576px) {
            .tracking-header h1 { font-size: 32px; }
            .order-code { font-size: 24px; }
            .timeline-content { margin-left: 80px; width: calc(100% - 100px); }
        }
    </style>
</head>
<body>

<div class="tracking-wrapper">

    <!-- Header -->
    <div class="tracking-header">
        <h1>Theo dõi vận đơn</h1>
        <p>Nhập mã đơn hàng để xem chi tiết hành trình giao hàng</p>
    </div>

    <!-- Search Form -->
    <div class="search-card">
        <form class="search-form" method="GET">
            <input type="text" name="code" value="<?= htmlspecialchars($order_code) ?>" 
                   placeholder="VD: OD20251125-01" required autocomplete="off">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <!-- Kết quả -->
    <?php if (!$order_code): ?>
        <div class="empty-state">
            <i class="fas fa-truck"></i>
            <h3>Nhập mã đơn hàng để bắt đầu</h3>
            <p>Bạn sẽ nhận được mã đơn qua <strong>SMS</strong> và <strong>Email</strong> ngay sau khi đặt hàng thành công.</p>
        </div>

    <?php elseif (!$order): ?>
        <div class="empty-state">
            <i class="fas fa-times-circle"></i>
            <h3>Không tìm thấy đơn hàng</h3>
            <p>Mã đơn <strong><?= htmlspecialchars($order_code) ?></strong> không tồn tại hoặc đã bị xóa.</p>
        </div>

    <?php else: ?>
        <div class="order-card">
            <!-- Header đơn hàng -->
            <div class="order-header">
                <div>
                    <div class="order-code">#<?= htmlspecialchars($order['order_code']) ?></div>
                    <div style="opacity:0.9; margin-top:8px; font-size:16px;">
                        <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div class="order-total">$<?= number_format($order['total'], 2) ?></div>
                    <div class="order-status">
                        <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                    </div>
                </div>
            </div>

            <!-- Timeline hành trình -->
            <div class="timeline-container">
                <?php foreach ($tracking_steps as $step): ?>
                    <div class="timeline-item <?= $step['done'] ? 'done' : '' ?> <?= $step['cancelled'] ?? '' ?>">
                        <div class="timeline-icon">
                            <i class="fas fa-<?= $step['icon'] ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h3><?= $step['label'] ?></h3>
                            <p><?= $step['time'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Thông tin tài xế -->
            <?php if (!empty($order['driver_name'])): ?>
                <div class="driver-section">
                    <div class="driver-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="driver-info">
                        <h4><?= htmlspecialchars($order['driver_name']) ?></h4>
                        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($order['driver_phone']) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer-note">
            <p><i class="fas fa-shield-alt"></i> Thông tin được cập nhật theo thời gian thực • Hỗ trợ: 1900 1234</p>
        </div>
    <?php endif; ?>
</div>

<script>
    // Tự động submit khi nhấn Enter
    document.querySelector('.search-form input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') this.form.submit();
    });
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>