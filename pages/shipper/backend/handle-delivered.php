<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shipper') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

include('../../../helper/general.php');

header('Content-Type: application/json');

$currentDay = date('d');
$currentMonth = date('m');
$currentYear = date('Y');

$dateForOrder = $currentDay . '_' . $currentMonth . '_' . $currentYear;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['id'];

    $orderNote = $_POST['note'];
    $images = $_FILES['images'];

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

    $data['status'] = 'delivered';
    $data['history'][] = [
        'userId' => $_SESSION['user_id'],
        'status' => 'delivered',
        'createdAt' => date('Y-m-d H:i:s')
    ];

    // Save note and images
    $data['note'] = $orderNote;
    $data['images'] = [];

    if (!empty($images)) {
        $imageDir = "../../../uploads/".$orderId;

        // Ensure the directory exists
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        foreach ($images['tmp_name'] as $key => $tmpName) {
            $imgId = uniqid('img_');
            $imageFileName = $dateForOrder."_".$imgId."_".$images['name'][$key];
            $imageFilePath = $imageDir."/".$imageFileName;

            if (move_uploaded_file($tmpName, $imageFilePath)) {
                $data['images'][] = $imageFileName;
            }
        }
    }

    $updateRes = updateDataToJson($data, $ordersDir, $fileName);
    
    if ($updateRes['status'] === 'success') {
        echo json_encode(['success' => true, 'message' => 'Order confirm successfully', 'data' => $updateRes['data']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>