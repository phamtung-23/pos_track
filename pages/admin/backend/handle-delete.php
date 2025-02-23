<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

include('../../../helper/general.php');

header('Content-Type: application/json');

$currentYear = date('Y');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['id'];

    // Load existing orders
    $ordersFilePath = "../../../database/orders/".$currentYear."/".$orderId.".json";

    $deleteRes = deleteFile($ordersFilePath);
    

    if ($deleteRes['status'] === 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Order deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Order not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>