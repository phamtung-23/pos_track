<?php

$statusDelivery = [
  'created' => 'Created',
  'waiting_shipper_accept' => 'Waiting for shipper to accept',
  'shipping' => 'Shipping',
  'delivered' => 'Delivered',
  'canceled' => 'Canceled'
];

// Hàm ghi log
function logEntry($message)
{
  $logFile = '../../../logs/payment_update_log.txt';
  $timestamp = date("Y-m-d H:i:s");
  // get full path
  $filePath = $_SERVER['PHP_SELF'];
  $logMessage = "[$timestamp] $filePath: $message\n";
  file_put_contents($logFile, $logMessage, FILE_APPEND);
}

function getDataFromJson($filePath)
{
  // Check if the file exists
  if (!file_exists($filePath)) {
    return ['status' => 'fail', 'message' => 'File not found'];
  }

  // Read the file content
  $jsonContent = file_get_contents($filePath);

  // Decode the JSON data
  $data = json_decode($jsonContent, true);

  // Check for JSON decoding errors
  if (json_last_error() !== JSON_ERROR_NONE) {
    return ['status' => 'fail', 'message' => 'Error decoding JSON'];
  }

  return ['status' => 'success', 'data' => $data];
}

function saveDataToJson($data, $directory, $fileName)
{
  // Ensure the directory exists
  if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
  }

  $filePath = $directory . '/' . $fileName . '.json';

  // Save data to JSON file
  if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT))) {
    return ['status' => 'success', 'data' => $data];
  } else {
    return ['status' => 'fail'];
  }
}

// delete a file
function deleteFile($filePath)
{
  if (file_exists($filePath)) {
    unlink($filePath);
    return ['status' => 'success'];
  } else {
    return ['status' => 'fail', 'message' => 'File not found'];
  }
}

// get all data files in a directory
function getAllDataFiles($directory)
{
  // check if the directory exists
  if (!is_dir($directory)) {
    return ['status' => 'fail', 'message' => 'Directory not found'];
  }

  // get all files in the directory
  $files = scandir($directory);

  // filter out the current directory and parent directory
  $files = array_diff($files, ['.', '..']);

  // read the content of each file
  $data = [];
  foreach ($files as $file) {
    $filePath = $directory . '/' . $file;
    $jsonContent = file_get_contents($filePath);
    $data[] = json_decode($jsonContent, true);
  }

  return ['status' => 'success', 'data' => $data];
}


function getAllJsonData($path) {
  $data = [];

  // Kiểm tra xem đường dẫn có tồn tại và là một thư mục không
  if (!is_dir($path)) {
      throw new Exception("The provided path is not a valid directory: $path");
  }

  // Tạo đối tượng RecursiveDirectoryIterator để duyệt qua các thư mục và tệp
  $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($path),
      RecursiveIteratorIterator::LEAVES_ONLY
  );

  foreach ($iterator as $file) {
      // Kiểm tra nếu là tệp JSON
      if ($file->isFile() && strtolower($file->getExtension()) === 'json') {
          // Đọc nội dung tệp JSON
          $content = file_get_contents($file->getRealPath());
          
          // Giải mã nội dung JSON thành mảng hoặc đối tượng
          $jsonData = json_decode($content, true);

          if (json_last_error() === JSON_ERROR_NONE) {
              $data[] = $jsonData;
          } else {
              throw new Exception("Error decoding JSON file: " . $file->getRealPath());
          }
      }
  }

  return $data;
}

// get information of the user by user id
function getUserInfoById($userId, $filePath)
{
  // check if the file exists
  if (!file_exists($filePath)) {
    return ['status' => 'fail', 'message' => 'File not found'];
  }

  // read the file content
  $jsonContent = file_get_contents($filePath);

  // decode the JSON data
  $data = json_decode($jsonContent, true);

  // check for JSON decoding errors
  if (json_last_error() !== JSON_ERROR_NONE) {
    return ['status' => 'fail', 'message' => 'Error decoding JSON'];
  }

  // find the user by user id
  foreach ($data as $item) {
    if ($item['id'] === $userId) {
      return ['status' => 'success', 'data' => $item];
    }
  }
}

// get color by status
function getColorByStatus($status)
{
  switch ($status) {
    case 'created':
      return 'badge text-bg-primary';
    case 'waiting_shipper_accept':
      return 'badge text-bg-info';
    case 'shipping':
      return 'badge text-bg-warning';
    case 'delivered':
      return 'badge text-bg-success';
    case 'canceled':
      return 'badge text-bg-danger';
    default:
      return '';
  }
}

// get name of the status
function getStatusName($status)
{
  global $statusDelivery;
  return $statusDelivery[$status] ?? '';
}

// remove user by id
function removeUserById($userId, $filePath)
{
  // check if the file exists
  if (!file_exists($filePath)) {
    return ['status' => 'fail', 'message' => 'File not found'];
  }

  // read the file content
  $jsonContent = file_get_contents($filePath);

  // decode the JSON data
  $data = json_decode($jsonContent, true);

  // check for JSON decoding errors
  if (json_last_error() !== JSON_ERROR_NONE) {
    return ['status' => 'fail', 'message' => 'Error decoding JSON'];
  }

  // find the user by user id
  $index = -1;
  foreach ($data as $key => $item) {
    if ($item['id'] === $userId) {
      $index = $key;
      break;
    }
  }

  // remove the user from the data array
  if ($index !== -1) {
    array_splice($data, $index, 1);
  } else {
    return ['status' => 'fail', 'message' => 'User not found'];
  }

  // save the updated data
  if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT))) {
    return ['status' => 'success'];
  } else {
    return ['status' => 'fail', 'message' => 'Error saving data'];
  }
}

// update user by user id
function updateUserInfoById($userId, $data, $filePath)
{
  // check if the file exists
  if (!file_exists($filePath)) {
    return ['status' => 'fail', 'message' => 'File not found'];
  }

  // read the file content
  $jsonContent = file_get_contents($filePath);

  // decode the JSON data
  $users = json_decode($jsonContent, true);

  // check for JSON decoding errors
  if (json_last_error() !== JSON_ERROR_NONE) {
    return ['status' => 'fail', 'message' => 'Error decoding JSON'];
  }

  // find the user by user id
  foreach ($users as $index => $item) {
    if ($item['id'] === $userId) {
      // update the user info
      $users[$index] = array_merge($item, $data);
      break;
    }
  }

  // save the updated data
  if (file_put_contents($filePath, json_encode($users, JSON_PRETTY_PRINT))) {
    return ['status' => 'success', 'data' => $data];
  } else {
    return ['status' => 'fail'];
  }
}


function updateDataToJson($data, $directory, $fileName)
{
  // Ensure the directory exists
  if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
  }

  $filePath = $directory . '/' . $fileName . '.json';

  // Load existing data or initialize a new object
  $existingData = [];
  if (file_exists($filePath)) {
    $fileContent = file_get_contents($filePath);
    $existingData = json_decode($fileContent, true);
  }

  // Merge existing data with new data
  $updatedData = array_merge($existingData, $data);

  // Save data to JSON file
  if (file_put_contents($filePath, json_encode($updatedData, JSON_PRETTY_PRINT))) {
    return ['status' => 'success', 'data' => $updatedData];
  } else {
    return ['status' => 'fail'];
  }
}