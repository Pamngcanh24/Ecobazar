<?php
include 'includes/header.php';

if(!isset($_GET['id'])){
    die("Không có ID đơn hàng");
}

$order_id = intval($_GET['id']);

// lấy thông tin đơn hàng
$sql = "SELECT o.*, o.id, d.name as driver_name, d.phone as driver_phone
        FROM orders o
        LEFT JOIN drivers d ON o.id = d.id
        WHERE o.id = $order_id";

$order = $conn->query($sql)->fetch_assoc();

if(!$order){
    die("Không tìm thấy đơn hàng");
}

// lấy danh sách sản phẩm trong đơn
$item_sql = "SELECT oi.*, p.name as product_name,p.image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = $order_id";

$items = $conn->query($item_sql);
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
            <p><b>Discount:</b> <?= number_format($order['discount'],2) ?>%</p>
            <p><b>Tổng:</b> <?= number_format($order['total'],2) ?>$</p>
            <p><b>Tài xế:</b> 
            <?php 
                if($order['id']){
                    echo '<a href="driver-detail.php?id='.$order['id'].'" style="color:#00b207;font-weight:600;text-decoration:none">
                            ' . $order['driver_name'] . '-' . $order['driver_phone'] . '
                        </a>';
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

<a href="order.php" class="btn-back">Quay lại</a>

</main>

<?php include 'includes/footer.php'; ?>
