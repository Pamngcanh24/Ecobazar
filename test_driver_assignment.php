<?php
// Test script cho chức năng phân công tài xế tự động
require_once './includes/connect.php';

// Hàm tách quận từ địa chỉ (copy từ process_order.php)
function extractDistrictFromAddress($address) {
    // Danh sách các quận trong Hà Nội
    $hanoiDistricts = [
        'Ba Đình', 'Hoàn Kiếm', 'Tây Hồ', 'Long Biên', 'Cầu Giấy',
        'Đống Đa', 'Hai Bà Trưng', 'Hoàng Mai', 'Thanh Xuân', 'Nam Từ Liêm',
        'Bắc Từ Liêm', 'Hà Đông', 'Thanh Trì', 'Gia Lâm', 'Đông Anh',
        'Sóc Sơn', 'Mê Linh', 'Đan Phượng', 'Hoài Đức', 'Quốc Oai',
        'Thạch Thất', 'Chương Mỹ', 'Thanh Oai', 'Thường Tín', 'Phú Xuyên',
        'Ứng Hòa', 'Mỹ Đức', 'Ba Vì', 'Sơn Tây', 'Phúc Thọ',
        'Thạch Thất', 'Mê Linh', 'Đông Anh', 'Gia Lâm'
    ];
    
    // Chuẩn hóa địa chỉ
    $address = mb_strtolower($address, 'UTF-8');
    
    foreach ($hanoiDistricts as $district) {
        $districtLower = mb_strtolower($district, 'UTF-8');
        if (strpos($address, $districtLower) !== false) {
            return $district;
        }
    }
    
    // Nếu không tìm thấy quận cụ thể, thử tìm theo pattern "quận + tên"
    if (preg_match('/quận\s+([\p{L}\s]+)/u', $address, $matches)) {
        return ucwords(trim($matches[1]));
    }
    
    // Nếu vẫn không tìm thấy, trả về null
    return null;
}

// Hàm tự động phân công tài xế theo quận (copy từ process_order.php)
function assignDriverByDistrict($district) {
    global $conn;
    
    if (!$district) {
        return null; // Không thể phân công nếu không xác định được quận
    }
    
    // Tìm tài xế trong cùng quận với số đơn hàng hiện tại ít nhất
    $stmt = $conn->prepare("
        SELECT id, name, phone, current_orders 
        FROM drivers 
        WHERE LOWER(address) LIKE LOWER(CONCAT('%', ?, '%')) 
        AND current_orders < 10  -- Giới hạn số đơn tối đa mỗi tài xế
        ORDER BY current_orders ASC, RAND()  -- Ưu tiên tài xế có ít đơn, random nếu bằng nhau
        LIMIT 1
    ");
    
    $stmt->bind_param("s", $district);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($driver = $result->fetch_assoc()) {
        // Tăng số đơn của tài xế này
        $updateStmt = $conn->prepare("UPDATE drivers SET current_orders = current_orders + 1 WHERE id = ?");
        $updateStmt->bind_param("s", $driver['id']);
        $updateStmt->execute();
        
        return $driver['id'];
    }
    
    // Nếu không tìm thấy tài xế trong quận, tìm tài xế có ít đơn nhất
    $fallbackStmt = $conn->prepare("
        SELECT id, name, phone, current_orders 
        FROM drivers 
        WHERE current_orders < 10
        ORDER BY current_orders ASC, RAND()
        LIMIT 1
    ");
    $fallbackStmt->execute();
    $fallbackResult = $fallbackStmt->get_result();
    
    if ($fallbackDriver = $fallbackResult->fetch_assoc()) {
        // Tăng số đơn của tài xế này
        $updateStmt = $conn->prepare("UPDATE drivers SET current_orders = current_orders + 1 WHERE id = ?");
        $updateStmt->bind_param("s", $fallbackDriver['id']);
        $updateStmt->execute();
        
        return $fallbackDriver['id'];
    }
    
    return null; // Không tìm thấy tài xế phù hợp
}

echo "<h1>Test Chức Năng Phân Công Tài Xế Tự Động</h1>";

// Test cases cho các địa chỉ khác nhau
$testAddresses = [
    "Số 123, Phố Trần Duy Hưng, Cầu Giấy, Hà Nội",
    "44 Trần Thái Tông, Cầu Giấy, HN",
    "Số 56, Phố Láng Hạ, Đống Đa, Hà Nội",
    "Số 45, Phố Tô Hiến Thành, Hai Bà Trưng, Hà Nội",
    "Số 101, Phố Hoàng Hoa Thám, Ba Đình, Hà Nội",
    "Số 22, Phố Cầu Giấy, Cầu Giấy, Hà Nội",
    "Số 8, Ngõ Hàng Bông, Hoàn Kiếm, Hà Nội",
    "Số 12, Phố Bà Triệu, Hoàn Kiếm, Hà Nội",
    "Số 27, Phố Nguyễn Trãi, Thanh Xuân, Hà Nội",
    "Số 3, Ngõ Thông Phong, Đống Đa, Hà Nội"
];

echo "<h2>Test Tách Quận Từ Địa Chỉ:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Địa chỉ</th><th>Quận được tách</th></tr>";

foreach ($testAddresses as $address) {
    $district = extractDistrictFromAddress($address);
    echo "<tr>";
    echo "<td>" . htmlspecialchars($address) . "</td>";
    echo "<td>" . ($district ? htmlspecialchars($district) : "Không xác định") . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Test Phân Công Tài Xế:</h2>";

// Reset current_orders về 0 cho tất cả tài xế
$conn->query("UPDATE drivers SET current_orders = 0");

// Test phân công cho từng địa chỉ
$assignedDrivers = [];
foreach ($testAddresses as $address) {
    $district = extractDistrictFromAddress($address);
    $driver_id = assignDriverByDistrict($district);
    
    if ($driver_id) {
        // Lấy thông tin tài xế được phân công
        $stmt = $conn->prepare("SELECT name, phone, current_orders FROM drivers WHERE id = ?");
        $stmt->bind_param("s", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();
        
        $assignedDrivers[] = [
            'address' => $address,
            'district' => $district,
            'driver_id' => $driver_id,
            'driver_name' => $driver['name'],
            'driver_phone' => $driver['phone'],
            'current_orders' => $driver['current_orders']
        ];
    } else {
        $assignedDrivers[] = [
            'address' => $address,
            'district' => $district,
            'driver_id' => null,
            'driver_name' => 'Không tìm thấy tài xế',
            'driver_phone' => '',
            'current_orders' => 0
        ];
    }
}

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Địa chỉ</th><th>Quận</th><th>Tài xế được phân công</th><th>SĐT</th><th>Số đơn hiện tại</th></tr>";

foreach ($assignedDrivers as $assignment) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($assignment['address']) . "</td>";
    echo "<td>" . ($assignment['district'] ? htmlspecialchars($assignment['district']) : 'Không xác định') . "</td>";
    echo "<td>" . htmlspecialchars($assignment['driver_name']) . "</td>";
    echo "<td>" . htmlspecialchars($assignment['driver_phone']) . "</td>";
    echo "<td>" . $assignment['current_orders'] . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Tổng kết:</h2>";
echo "<p>Đã phân công " . count(array_filter($assignedDrivers, function($a) { return $a['driver_id'] !== null; })) . "/" . count($testAddresses) . " đơn hàng</p>";

// Hiển thị trạng thái current_orders cuối cùng của các tài xế
echo "<h2>Trạng thái current_orders của tài xế sau test:</h2>";
$result = $conn->query("SELECT id, name, address, current_orders FROM drivers ORDER BY current_orders DESC, name");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Tên</th><th>Địa chỉ</th><th>Số đơn hiện tại</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
    echo "<td>" . $row['current_orders'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='test_driver_assignment.php'>Test lại</a> | <a href='08shop.php'>Về trang chủ</a></p>";
?>