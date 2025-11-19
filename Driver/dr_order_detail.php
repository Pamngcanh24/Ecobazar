<?php
include 'includes/header.php';

if(!isset($_GET['id'])){
    die("Không có ID đơn hàng");
}

$order_id = intval($_GET['id']);

// lấy thông tin đơn hàng
$sql = "SELECT o.*, d.id AS driver_id, d.name AS driver_name, d.phone AS driver_phone
        FROM orders o
        LEFT JOIN drivers d ON o.driver_id = d.id
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$order){
    die("Không tìm thấy đơn hàng");
}

// lấy danh sách sản phẩm trong đơn
$item_sql = "SELECT oi.*, p.name as product_name,p.image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = $order_id";

$items = $conn->query($item_sql);

// Helper: extract district from address
function extractDistrictFromAddress($address) {
    $hanoiDistricts = [
        'Ba Đình', 'Hoàn Kiếm', 'Tây Hồ', 'Long Biên', 'Cầu Giấy',
        'Đống Đa', 'Hai Bà Trưng', 'Hoàng Mai', 'Thanh Xuân', 'Nam Từ Liêm',
        'Bắc Từ Liêm', 'Hà Đông', 'Thanh Trì', 'Gia Lâm', 'Đông Anh',
        'Sóc Sơn', 'Mê Linh', 'Đan Phượng', 'Hoài Đức', 'Quốc Oai',
        'Thạch Thất', 'Chương Mỹ', 'Thanh Oai', 'Thường Tín', 'Phú Xuyên',
        'Ứng Hòa', 'Mỹ Đức', 'Ba Vì', 'Sơn Tây', 'Phúc Thọ'
    ];
    $address = mb_strtolower($address, 'UTF-8');
    foreach ($hanoiDistricts as $district) {
        $districtLower = mb_strtolower($district, 'UTF-8');
        if (strpos($address, $districtLower) !== false) return $district;
    }
    if (preg_match('/quận\s+([\p{L}\s]+)/u', $address, $matches)) {
        return ucwords(trim($matches[1]));
    }
    return null;
}

// Helper: assign driver by district and increment their current_orders
function assignDriverByDistrict($conn, $district) {
    if (!$district) return null;
    $stmt = $conn->prepare("SELECT id, current_orders FROM drivers WHERE LOWER(address) LIKE LOWER(CONCAT('%', ?, '%')) AND current_orders < 10 ORDER BY current_orders ASC, RAND() LIMIT 1");
    $stmt->bind_param("s", $district);
    $stmt->execute();
    $res = $stmt->get_result();
    $driver = $res->fetch_assoc();
    $stmt->close();
    if ($driver) {
        $up = $conn->prepare("UPDATE drivers SET current_orders = current_orders + 1 WHERE id = ?");
        $up->bind_param("s", $driver['id']);
        $up->execute();
        $up->close();
        return $driver['id'];
    }
    $fb = $conn->prepare("SELECT id FROM drivers WHERE current_orders < 10 ORDER BY current_orders ASC, RAND() LIMIT 1");
    $fb->execute();
    $fbRes = $fb->get_result();
    $fbDriver = $fbRes->fetch_assoc();
    $fb->close();
    if ($fbDriver) {
        $up2 = $conn->prepare("UPDATE drivers SET current_orders = current_orders + 1 WHERE id = ?");
        $up2->bind_param("s", $fbDriver['id']);
        $up2->execute();
        $up2->close();
        return $fbDriver['id'];
    }
    return null;
}

// Handle driver actions: accept or decline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logged_driver_id = $_SESSION['driver_id'] ?? null;
    $action = $_POST['action'] ?? '';
    if ($logged_driver_id && $order['driver_id'] === $logged_driver_id && strtolower($order['status']) === 'pending') {
        if ($action === 'accept') {
            $up = $conn->prepare("UPDATE orders SET status = 'processing' WHERE id = ? AND driver_id = ? AND status = 'pending'");
            $up->bind_param("is", $order_id, $logged_driver_id);
            $up->execute();
            $up->close();
            echo "<script>alert('Bạn đã nhận đơn thành công'); window.location.href='order.php';</script>";
            exit;
        }
        if ($action === 'decline') {
            if (!empty($order['driver_id'])) {
                $dec = $conn->prepare("UPDATE drivers SET current_orders = GREATEST(current_orders - 1, 0) WHERE id = ?");
                $dec->bind_param("s", $order['driver_id']);
                $dec->execute();
                $dec->close();
            }
            $district = extractDistrictFromAddress($order['shipping_address']);
            $new_driver_id = assignDriverByDistrict($conn, $district);
            if ($new_driver_id) {
                $set = $conn->prepare("UPDATE orders SET driver_id = ? WHERE id = ?");
                $set->bind_param("si", $new_driver_id, $order_id);
                $set->execute();
                $set->close();
            } else {
                $clr = $conn->prepare("UPDATE orders SET driver_id = NULL WHERE id = ?");
                $clr->bind_param("i", $order_id);
                $clr->execute();
                $clr->close();
            }
            echo "<script>alert('Bạn đã từ chối đơn, hệ thống sẽ phân công lại'); window.location.href='order.php';</script>";
            exit;
        }
    }
}
?>
<style>
*{font-family:'Poppins',sans-serif;}
.main-content{padding:32px;background:#f5f7fa;min-height:100vh;}

.order-box{
    background:#fff;
    border-radius:14px;
    padding:30px;
    margin-bottom:28px;
    box-shadow:0 4px 18px rgba(0,0,0,.06);
    border:1px solid #e8e8e8;
}

.order-title{
    font-size:22px;
    font-weight:700;
    margin-bottom:22px;
    color:#111;
    border-left:4px solid #00b207;
    padding-left:12px;
}

/* 2 COLUMN INFO */
.order-row{
    display:flex;
    gap:60px;
    flex-wrap:wrap;
}
.order-col{
    flex:1;
    min-width:300px;
}
.order-col h4{
    margin:0 0 14px;
    font-size:17px;
    font-weight:700;
    color:#00b207;
    letter-spacing:.3px;
}
.order-col p{
    margin:7px 0;
    font-size:15px;
    color:#333;
}
.order-col p b{
    color:#000;
}

/* TABLE */
.table-items{
    width:100%;
    border-collapse:collapse;
    font-size:15px;
}
.table-items th{
    background:#eef3ee;
    padding:12px;
    text-align:center;
    font-weight:600;
    border-bottom:1px solid #dcdcdc;
}
.table-items td{
    padding:12px;
    border-bottom:1px solid #f0f0f0;
    text-align:center;
}
.table-items td:first-child{
    text-align:left;
}
.table-items tr:hover td{
    background:#f8faf8;
}
.table-items img{
    width:55px;
    height:55px;
    border-radius:8px;
    object-fit:cover;
}
.table-items td.img-cell{
    text-align:center;
}
.table-items td.img-cell img{
    width:50px;
    height:50px;
    object-fit:cover;
    border-radius:6px;
}

/* STATUS BADGE */
.status-badge{
   display:inline-block;
   padding:6px 14px;
   border-radius:24px;
   font-size:13px;
   font-weight:600;
   text-transform:capitalize;
}
.status-badge.Pending{background:#fff3cd; color: #856404;}
.status-badge.Processing{background:#cce5ff;  color: #004085;}
.status-badge.Completed{background:#d4edda;color: #155724;}
.status-badge.Cancelled{background:#f8d7da; color: #721c24;}

/* BTN BACK */
.btn-back{
    display:inline-block;
    margin-top:14px;
    margin-bottom:24px;
    padding:10px 18px;
    background:#00b207;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
    font-size:15px;
    font-weight:600;
    transition:.2s;
}
.btn-back:hover{
    opacity:.85;
    transform:translateY(-1px);
}
.action-row{ margin-top:16px; display:flex; gap:12px; }
.btn-accept{ background:#00b207; color:#fff; padding:10px 18px; border-radius:8px; border:none; cursor:pointer; text-decoration:none; font-size:15px; font-weight:600; transition:.2s; }
.btn-decline{ background:#dc3545; color:#fff; padding:10px 18px; border-radius:8px; border:none; cursor:pointer; text-decoration:none; font-size:15px; font-weight:600; transition:.2s; }
.btn-accept:hover, .btn-decline:hover{ opacity:.85; transform:translateY(-1px); }
</style>
<?php
$status_class = strtolower($order['status']);

switch($status_class){
    case "completed":
        $status_badge = '<span class="status-badge Completed">Completed</span>';
        break;
    case "processing":
        $status_badge = '<span class="status-badge Processing">Processing</span>';
        break;
    case "pending":
        $status_badge = '<span class="status-badge Pending">Pending</span>';
        break;
    case "cancelled":
        $status_badge = '<span class="status-badge Cancelled">Cancelled</span>';
        break;
}

?>

<main class="main-content">
<a href="order.php" class="btn-back">Quay lại</a>

<div class="order-box">
    <div class="order-title">Chi tiết đơn hàng <?php echo $order['order_code']; ?></div>

    <div class="order-row">
        <div class="order-col">
            <h4>Thông tin khách hàng</h4>
            <p><b>Họ tên:</b> <?php echo $order['billing_name']; ?></p>
            <p><b>Email:</b> <?php echo $order['billing_email']; ?></p>
            <p><b>SĐT:</b> <?php echo $order['billing_phone']; ?></p>
            <p><b>Địa chỉ:</b> <?php echo $order['shipping_address']; ?></p>
        </div>

        <div class="order-col">
            <h4>Thông tin đơn hàng</h4>
            <p><b>Ngày tạo:</b> <?php echo $order['order_date']; ?></p>
            <p><b>Trạng thái:</b> <?= $status_badge ?></p>
            <p><b>Shipping Fee:</b> <?= number_format($order['shipping_cost'],2) ?>$</p>
            <p><b>Tổng:</b> <?= number_format($order['total'],2) ?>$</p>
            <p><b>Tài xế:</b> 
            <?php  
                if (!empty($order['driver_id'])) {
                    echo '<span style="color:#00b207;font-weight:600">'
                        . htmlspecialchars($order['driver_name']) . ' - ' . htmlspecialchars($order['driver_phone']) .
                        '</span>';
                } else {
                    echo '<span style="color:#999">Chưa phân công</span>';
                }
            ?>
            </p>  
        </div>
    </div>
</div>

<div class="order-box">
    <div class="order-title">Danh sách sản phẩm</div>
     <table class="table-items">
        <tr>
            <th>IMAGE</th>
            <th>PRODUCT</th>
            <th>PRICE</th>
            <th>QUANTITY</th>
            <th>SUBTOTAL</th>
        </tr>
        <?php while($i = $items->fetch_assoc()): ?>
        <tr>
            <td class="img-cell"><img src="../img/<?php echo $i['image']; ?>" alt=""></td>
            <td><?php echo $i['product_name']; ?></td>
            <td><?php echo $i['quantity']; ?></td>
            <td><?php echo number_format($i['price'],0,",",".")." $"; ?></td>
            <td><?php echo number_format($i['price'] * $i['quantity'],0,",",".")." $"; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php if (strtolower($order['status']) === 'pending' && isset($_SESSION['driver_id']) && $_SESSION['driver_id'] === $order['driver_id']): ?>
  <div class="action-row">
    <form method="POST" style="display:inline">
      <input type="hidden" name="action" value="accept">
      <button type="submit" class="btn-accept">Nhận đơn</button>
    </form>
    <form method="POST" style="display:inline">
      <input type="hidden" name="action" value="decline">
      <button type="submit" class="btn-decline">Từ chối đơn</button>
    </form>
  </div>
<?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
