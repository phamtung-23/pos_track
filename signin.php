<?php
session_start();

// Thiết lập thời gian session tồn tại là 1 giờ (3600 giây)
ini_set('session.gc_maxlifetime', 3600); 
session_set_cookie_params(3600); // Đảm bảo cookie session cũng hết hạn sau 1 giờ

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['login_email'];
    $password = $_POST['your_pass'];

    // Đọc file users.json
    $file = 'database/users.json';
    if (file_exists($file)) {
        $users = json_decode(file_get_contents($file), true);
    } else {
       echo "<script>
                alert('Không tìm thấy danh sách tài khoản!');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    // Kiểm tra tài khoản
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            // Kiểm tra mật khẩu đã hash
            if (password_verify($password, $user['password'])) { // So sánh mật khẩu nhập vào với mật khẩu hash
                // Lưu thông tin người dùng vào session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['fullname']; // Giả sử 'full_name' có trong dữ liệu người dùng
                $_SESSION['email'] = $user['email']; // Giả sử 'email' có trong dữ liệu người dùng
                $_SESSION['phone'] = $user['phone']; // Giả sử 'phone' có trong dữ liệu người dùng
                $_SESSION['idTelegram'] = $user['idTelegram']; // Giả sử 'idTelegram' có trong dữ liệu người dùng
                // Ghi nhận thời gian bắt đầu phiên
                $_SESSION['login_time'] = time();

                // Chuyển hướng dựa trên vai trò
                if ($user['role'] == 'admin') {
                    header("Location: pages/admin");
                } elseif ($user['role'] == 'shipper') {
                    header("Location: pages/shipper");
                }
                exit();
            } else {
               
                 echo "<script>
                        alert('Sai mật khẩu!');
                        window.location.href = 'index.php';
                      </script>";
                exit();
            }
        }
    }

    echo "<script>
            alert('Không tìm thấy tài khoản!');
            window.location.href = 'index.php';
          </script>";
}
?>
