<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shipper') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

include('../../../helper/general.php');

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $idTelegram = $_POST['idTelegram'];

    $userdata = [
        'id' => $userId,
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'idTelegram' => $idTelegram
    ];
    $userDataPath = '../../../database/users.json';

    $updateRes = updateUserInfoById($userId, $userdata, $userDataPath);

    if ($updateRes['status'] === 'success') {
        echo json_encode(['success' => true, 'message' => 'Updated profile successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => $updateRes['message']]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>