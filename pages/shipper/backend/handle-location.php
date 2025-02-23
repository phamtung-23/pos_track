<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shipper') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

include('../../../helper/general.php');

header('Content-Type: application/json');

$currentYear = date('Y');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Load existing orders
    $ordersDir = "../../../database/orders/".$currentYear;
    $fileName = $orderId;
    $filePath = $ordersDir."/".$fileName.".json";

    $dataRes = getDataFromJson($filePath);

    if ($dataRes['status'] === 'success') {
        $data = $dataRes['data'];
    } else {
      $data = null;
    }

    $data['latitude'] = $latitude;
    $data['longitude'] = $longitude;

    $updateRes = updateDataToJson($data, $ordersDir, $fileName);
    
    if ($updateRes['status'] === 'success') {
        echo json_encode(['success' => true, 'message' => 'Updated successfully', 'data' => $updateRes['data']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>