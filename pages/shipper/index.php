<?php
session_start();

include('../../helper/general.php');

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shipper') {
    echo "<script>alert('Bạn chưa đăng nhập! Vui lòng đăng nhập lại.'); window.location.href = '../../index.php';</script>";
    exit();
}

// Retrieve full name and email from session
$fullName = $_SESSION['full_name'];
$userId = $_SESSION['user_id']; // operator_email matches user_id

$orderList = getAllJsonData("../../database/orders/");
// print_r($data);

// filer orders by deliveryPersonId
$orderList = array_filter($orderList, function ($order) use ($userId) {
    return $order['deliveryPersonId'] === $userId;
});

?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>Shipper Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <main>
        <nav class="navbar navbar-expand-md bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <h2>Delivery</h2>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarScroll">
                    <ul class="navbar-nav me-auto my-2 my-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="profile.php">Profile</a>
                        </li>
                    </ul>
                    <form action="../../logout.php" class="d-flex" role="search">
                        <button class="btn btn-outline-danger" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
        <div class="container pt-3">
            <h2>Order Management</h2>
            <p>Welcome, <?php echo $fullName; ?>!</p>
        </div>
        <!-- <div class="container">
            <div class="row">
                <div class="col-12 d-flex justify-content-end">
                    <a href="create-order.php">
                        <button class="btn btn-success mt-2 mb-2"><i class="fa-solid fa-plus"></i> Add Order</button>
                    </a>
                </div>
            </div>
        </div> -->
        <div class="container">
            <div class="mb-3">
                <label for="statusFilter" class="form-label me-2">Filter by Status:</label>
                <select id="statusFilter" class="form-select" style="width: 200px;">
                    <option value="">All</option>
                    <option value="Created">Created</option>
                    <option value="Waiting for shipper to accept">Waiting Shipper Accept</option>
                    <option value="Shipping">Shipping</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Canceled">Canceled</option>
                </select>
            </div>
            <div class="table-responsive" style="margin-bottom: 80px !important;">
                <table id="adminTable" class="table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Item name</th>
                            <!-- <th scope="col">Weight (kg)</th>
                            <th scope="col">Customer Name</th> -->
                            <th style="min-width: 150px;" scope="col">Address</th>
                            <!-- <th scope="col">Delivery Person</th> -->
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $indexOrder = 1;
                        foreach ($orderList as $key => $order) {
                            $shipperInfo = getUserInfoById($order['deliveryPersonId'], "../../database/users.json");
                            echo "<tr>";
                            echo "<th scope='row'>" . ($indexOrder) . "</th>";
                            echo "<td>" . $order['itemName'] . "</td>";
                            // echo "<td>" . $order['weight'] . "</td>";
                            // echo "<td>" . $order['customerName'] . "</td>";
                            echo "<td style='min-width: 150px;'>" . $order['address'] . "</td>";
                            // echo "<td>" . ($shipperInfo['data']['fullname'] ?? '') . "</td>";
                            echo "<td><span class='" . getColorByStatus($order['status']) . "'>" . getStatusName($order['status']) . "</span></td>";
                            echo "<td class='align-middle h-100 d-flex gap-1 justify-content-center align-items-center'>
                                <button id='btn-detail' onclick='openDetail(\"" . $order['idOrder'] . "\")' type='button' class='btn btn-secondary'><i class='fa-regular fa-eye'></i></button>
                            </td>";
                            echo "</tr>";
                            $indexOrder++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer id="sticky-footer" class="flex-shrink-0 py-2 bg-dark text-white-50">
        <div class="container text-center">
            <small>© 2025 Phần mềm phát triển bởi PTTung 0359663439</small>
        </div>
    </footer>
    <script>
        $(document).ready(function() {
            if (window.innerWidth <= 550) {
                $('#adminTable').DataTable({
                    scrollY: true, // Set the vertical scrolling height
                    scrollX: true, // Set the vertical scrolling height
                    scrollCollapse: true, // Allow the table to reduce height if less content
                    paging: true, // Enable pagination
                    searching: true, // Enable searching
                    ordering: true, // Enable column sorting
                    info: true, // Show table info
                    language: {
                        search: "Search:",
                        paginate: {
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            } else {
                $('#adminTable').DataTable({
                    paging: true, // Enable pagination
                    searching: true, // Enable searching
                    ordering: true, // Enable column sorting
                    info: true, // Show table info
                    language: {
                        search: "Search:",
                        paginate: {
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            }

            // Filter table when the status dropdown changes
            $('#statusFilter').on('change', function() {
                let selectedStatus = $(this).val();

                $('#adminTable').DataTable().columns(6).search(selectedStatus).draw();
            });



        });

        function openDetail(id) {
            window.location.href = 'detail-order.php?id=' + id;
        }
    </script>

</body>

</html>