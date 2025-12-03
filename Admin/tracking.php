<?php
include 'includes/header.php';

$order_code = trim($_GET['code'] ?? '');
$order = null;
$tracking_steps = [];

if ($order_code !== '') {
    $order_code_esc = $conn->real_escape_string($order_code);

    $sql = "SELECT o.*, d.name AS driver_name, d.phone AS driver_phone
            FROM orders o
            LEFT JOIN drivers d ON o.driver_id = d.id
            WHERE o.order_code = '$order_code_esc' LIMIT 1";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $status = strtolower($order['status']);

        // 4 BƯỚC CHUẨN – KHÔNG THỜI GIAN – KHÔNG TRẠNG THÁI THỪA
        $tracking_steps = [
            ['icon' => 'shopping-bag', 'label' => 'Đặt hàng thành công',     'done' => true],
            ['icon' => 'gear',         'label' => 'Đang xử lý đơn hàng',     'done' => in_array($status, ['processing','confirmed','preparing','picked','shipped','delivered','completed'])],
            ['icon' => 'truck',        'label' => 'Shipper đang giao',       'done' => in_array($status, ['shipped','delivered','completed'])],
            ['icon' => 'check-circle', 'label' => 'Giao hàng thành công',     'done' => $status === 'completed' || $status === 'delivered']
        ];

        // Trường hợp hủy đơn
        if ($status === 'cancelled') {
            $tracking_steps = [
                ['icon' => 'shopping-bag', 'label' => 'Đặt hàng thành công', 'done' => true],
                ['icon' => 'x-circle',     'label' => 'Đơn hàng đã bị hủy',   'done' => true, 'cancelled' => true]
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
    <title>Theo dõi vận đơn #<?= htmlspecialchars($order_code) ?: 'Nhập mã đơn' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #28a745;
            --primary-dark: #218838;
            --primary-light: #34ce57;
            --danger: #dc3545;
            --gray-600: #6c757d;
            --gray-900: #212529;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f1f8e9 0%, #d4edda 100%);
            min-height: 100vh;
            line-height: 1.6;
        }

        .wrapper { max-width: 1100px; margin: 0 auto; padding: 30px 20px; }

        .header {
            text-align: center;
            margin-bottom: 50px;
        }
        .header h1 {
            font-family: Arial, sans-serif;
            font-size: 42px;
            font-weight: 549;
            background: linear-gradient(90deg, var(--primary-dark), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }
        .header p { font-size: 18px; color: var(--gray-600); }

        .main-grid {
            display: grid;
            gap: 50px;
        }

        .search-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(40,167,69,0.15);
            border: 1px solid rgba(40,167,69,0.1);
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-form {
            position: relative;
            max-width: 600px;
            width: 100%;
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s;
        }
        .search-form input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 8px rgba(40,167,69,0.15);
        }
        .search-form button {
            position: absolute;
            right: 8px; top: 8px;
            width: 52px; height: 52px;
            border: none;
            border-radius: 16px;
            background: var(--primary);
            color: white;
            font-size: 22px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 25px rgba(40,167,69,0.4);
        }
        .search-form button:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        /* Kết quả chung (có đơn / không tìm thấy / chưa nhập) */
        .result-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 25px 70px rgba(40,167,69,0.18);
            border: 1px solid rgba(40,167,69,0.1);
            min-height: 420px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px 40px;
            animation: fadeInUp 0.7s ease;
        }
        .result-card i {
            font-size: 110px;
            margin-bottom: 30px;
        }
        .result-card h3 {
            font-size: 32px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 16px;
        }
        .result-card p {
            font-size: 18px;
            color: var(--gray-600);
            max-width: 600px;
            line-height: 1.7;
        }

        .order-card {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(40,167,69,0.18);
            border: 1px solid rgba(40,167,69,0.1);
            animation: fadeInUp 0.8s ease;
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
        .order-code { font-size: 32px; font-weight: 800; }
        .order-total { font-size: 24px; font-weight: 700; }
        .order-status {
            padding: 10px 28px;
            border-radius: 50px;
            background: rgba(255,255,255,0.3);
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Timeline - 4 bước */
        .timeline {
            padding: 70px 80px;
            position: relative;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0; bottom: 0;
            width: 6px;
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            border-radius: 3px;
            transform: translateX(-50%);
        }
        .step {
            display: flex;
            align-items: center;
            margin-bottom: 80px;
            position: relative;
        }
        .step:last-child { margin-bottom: 0; }
        .step-icon {
            width: 84px; height: 84px;
            border-radius: 50%;
            background: white;
            border: 6px solid #ddd;
            color: #aaa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            z-index: 2;
            box-shadow: 0 15px 40px rgba(40,167,69,0.3);
            transition: all 0.4s;
            flex-shrink: 0;
        }
        .step.done .step-icon {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            animation: pulse 2s infinite;
        }
        .step.cancelled .step-icon {
            background: var(--danger);
            border-color: var(--danger);
            color: white;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(40,167,69,0.4); }
            70% { box-shadow: 0 0 0 24px rgba(40,167,69,0); }
            100% { box-shadow: 0 0 0 0 rgba(40,167,69,0); }
        }
        .step-content {
            flex: 1;
            padding: 30px 45px;
            background: #f8fff9;
            border-radius: 24px;
            margin: 0 40px;
            border: 2px solid transparent;
        }
        .step.done .step-content {
            background: linear-gradient(135deg, #e8f9eb, #d4f7dc);
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(40,167,69,0.15);
        }
        .step-content h3 {
            font-size: 21px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .driver-info {
            background: linear-gradient(135deg, #f8fff9, #e8f9eb);
            padding: 40px;
            border-top: 4px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        .driver-avatar {
            width: 100px; height: 100px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            font-size: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .timeline::before { left: 50px; }
            .step { flex-direction: column; align-items: flex-start; }
            .step-content { margin: 25px 0 0 110px; width: calc(100% - 130px); }
            .step-icon { position: absolute; left: 0; }
            .order-header { text-align: center; flex-direction: column; }
        }
        @media (max-width: 576px) {
            .header h1 { font-size: 34px; }
            .search-card, .result-card { padding: 30px 20px; }
            .step-content { margin-left: 90px; width: calc(100% - 110px); }
        }

        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(30px); }
            to   { opacity:1; transform:translateY(0); }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="header">
        <h1><b>Theo dõi vận đơn</b></h1>
        <p>Nhập mã đơn hàng để xem tình trạng giao hàng mới nhất</p>
    </div>

    <div class="main-grid">

        <!-- Form tìm kiếm -->
        <div class="search-card">
            <form class="search-form" method="GET">
                <input type="text" name="code" value="<?= htmlspecialchars($order_code) ?>" 
                       placeholder="VD: OD20251201-001" required autocomplete="off">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <!-- Kết quả -->
        <?php if ($order_code === ''): ?>
            <div class="result-card">
                <i class="fas fa-truck" style="color:var(--primary);opacity:0.8;"></i>
                <h3>Nhập mã đơn hàng để theo dõi</h3>
                <p>Mã đơn được gửi qua <strong>SMS</strong> và <strong>Email</strong> ngay sau khi đặt hàng.</p>
            </div>

        <?php elseif (!$order): ?>
            <div class="result-card">
                <i class="fas fa-times-circle" style="color:#dc3545;"></i>
                <h3>Không tìm thấy đơn hàng</h3>
                <p>Mã <strong><?= htmlspecialchars($order_code) ?></strong> không tồn tại hoặc đã bị xóa.</p>
            </div>

        <?php else: ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-code">#<?= htmlspecialchars($order['order_code']) ?></div>
                        <div style="opacity:0.9;margin-top:8px;font-size:16px;">
                            Đặt lúc: <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div class="order-total">$<?= number_format($order['total'], 2) ?></div>
                        <div class="order-status">
                            <?= ucwords(str_replace('_', ' ', $order['status'])) ?>
                        </div>
                    </div>
                </div>

                <div class="timeline">
                    <?php foreach ($tracking_steps as $step): ?>
                        <div class="step <?= $step['done'] ? 'done' : '' ?> <?= isset($step['cancelled']) ? 'cancelled' : '' ?>">
                            <div class="step-icon">
                                <i class="fas fa-<?= $step['icon'] ?>"></i>
                            </div>
                            <div class="step-content">
                                <h3><?= $step['label'] ?></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($order['driver_name'])): ?>
                    <div class="driver-info">
                        <div class="driver-avatar"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <h4 style="font-size:23px;margin-bottom:8px;">
                                Tài xế: <?= htmlspecialchars($order['driver_name']) ?>
                            </h4>
                            <p style="font-size:18px;color:var(--gray-600);">
                                <i class="fas fa-phone"></i> <?= htmlspecialchars($order['driver_phone']) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

    <div style="text-align:center;margin-top:50px;color:#666;font-size:15px;">
        <p>Cập nhật theo thời gian thực • Hỗ trợ: 1900 1234</p>
    </div>
</div>

<script>
    const input = document.querySelector('.search-form input');
    input && input.focus();
    input && input.addEventListener('keypress', e => {
        if (e.key === 'Enter') e.target.form.submit();
    });
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
