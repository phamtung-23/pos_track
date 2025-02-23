<?php
// Giả lập một API để trả về vị trí của marker (ví dụ shipper)
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
include('../../../helper/general.php');

// Đặt tiêu đề trả về JSON
header('Content-Type: application/json');

// Kiểm tra phương thức yêu cầu (chỉ cho phép GET)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Use GET.'
    ]);
    exit();
}

$currentYear = date('Y');
// get id in query string
$orderId = $_GET['id'];
// Load existing orders
$ordersDir = "../../../database/orders/" . $currentYear;
$fileName = $orderId;
$filePath = $ordersDir . "/" . $fileName . ".json";

$dataRes = getDataFromJson($filePath);

if ($dataRes['status'] === 'success') {
    $data = $dataRes['data'];
} else {
    $data = null;
}

$latitude = $data['latitude'] ?? 0;
$longitude = $data['longitude'] ?? 0;

// Giả lập vị trí của marker (có thể lấy từ database hoặc hệ thống theo dõi GPS)
$markerPosition = [
    'lng' => $longitude, // Longitude
    'lat' => $latitude  // Latitude
];

// Trả về dữ liệu vị trí dưới dạng JSON
echo json_encode([
    'success' => true,
    'location' => $markerPosition
]);
