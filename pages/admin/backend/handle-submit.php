<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

include '../../../helper/general.php';

header('Content-Type: application/json');

// Get data from POST request
// $requestData = json_decode(file_get_contents("php://input"), true);

$currentYear = date("Y");

$idOrder = uniqid("order_");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $data['idOrder'] = $idOrder;
  $data['itemName'] = $_POST['itemName'];
  $data['customerName'] = $_POST['customerName'];
  $data['weight'] = $_POST['weight'];
  $data['deliveryPersonId'] = $_POST['deliveryPerson'];
  $data['address'] = $_POST['address'];
  $data['status'] = 'waiting_shipper_accept';
  $data['createdBy'] = $_SESSION['user_id'];
  $data['history'] = [
    [
      'userId' => $_SESSION['user_id'],
      'status' => 'created',
      'createdAt' => date('Y-m-d H:i:s')
    ]
  ];
  $data['createdAt'] = date('Y-m-d H:i:s');
  $data['updatedAt'] = date('Y-m-d H:i:s');
}


if ($data) {
  // Save the updated JSON data back to the file
  $directory = '../../../database/orders/' . $currentYear;
  logEntry("directory: $directory");
  $res = saveDataToJson($data, $directory, $idOrder);
  echo json_encode(['success' => true, 'message' => 'Create order successful', 'data' => $data]);
} else {
  echo json_encode(['success' => false, 'message' => 'Error creating order']);
}
