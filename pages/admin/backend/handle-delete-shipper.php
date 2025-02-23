<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

include('../../../helper/general.php');

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];

    $userDataPath = '../../../database/users.json';

    $deleteRes = removeUserById($userId, $userDataPath);
    

    if ($deleteRes['status'] === 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Deleted shipper successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $deleteRes['message']]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>