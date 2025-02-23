<?php
session_start();

include('../../helper/general.php');

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Bạn chưa đăng nhập! Vui lòng đăng nhập lại.'); window.location.href = '../../index.php';</script>";
    exit();
}

// Retrieve full name and email from session
$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id']; // operator_email matches user_id

$orderList = getAllJsonData("../../database/orders/");
// print_r($data);

?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>Admin Dashboard</title>
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
                    <h2>Dashboard</h2>
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
                            <a class="nav-link" aria-current="page" href="shipper.php">Shippers</a>
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
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex justify-content-end">
                    <a href="create-order.php">
                        <button class="btn btn-success mt-2 mb-2"><i class="fa-solid fa-plus"></i> Add Order</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="table-responsive" style="margin-bottom: 80px !important;">
                <table id="adminTable" class="table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Item name</th>
                            <!-- <th scope="col">Weight (kg)</th> -->
                            <!-- <th scope="col">Customer Name</th> -->
                            <th style='min-width: 150px;' scope="col">Address</th>
                            <!-- <th scope="col">Delivery Person</th> -->
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($orderList as $key => $order) {
                            $shipperInfo = getUserInfoById($order['deliveryPersonId'], "../../database/users.json");
                            echo "<tr>";
                            echo "<th scope='row'>" . ($key + 1) . "</th>";
                            echo "<td>" . $order['itemName'] . "</td>";
                            // echo "<td>" . $order['weight'] . "</td>";
                            // echo "<td>" . $order['customerName'] . "</td>";
                            echo "<td style='min-width: 150px;'>" . $order['address'] . "</td>";
                            // echo "<td>" . ($shipperInfo['data']['fullname'] ?? '') . "</td>";
                            echo "<td><span class='" . getColorByStatus($order['status']) . "'>" . getStatusName($order['status']) . "</span></td>";
                            echo "<td class='align-middle h-100 d-flex gap-1 justify-content-center align-items-center'>
                                <button id='btn-detail' onclick='openDetail(\"" . $order['idOrder'] . "\")' type='button' class='btn btn-secondary'><i class='fa-regular fa-eye'></i></button>
                                <button id='btn-delete' type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#exampleModal' data-bs-name='" . $order['itemName'] . "' data-bs-idOrder='" . $order['idOrder'] . "'><i class='fa-solid fa-trash'></i></button>
                            </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirm Delete</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="content-model">
                        Are you sure you want to delete this order?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">cancel</button>
                        <button type="button" class="btn btn-danger btn-confirm-delete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer id="sticky-footer" class="flex-shrink-0 py-2 bg-dark text-white-50">
        <div class="container text-center">
            <small>© 2025 Phần mềm phát triển bởi PTTung 0359663439</small>
        </div>
    </footer>
    <script>
        const exampleModal = document.getElementById('exampleModal')
        if (exampleModal) {
            exampleModal.addEventListener('show.bs.modal', event => {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                const orderName = button.getAttribute('data-bs-name')
                const orderId = button.getAttribute('data-bs-idOrder')
                // If necessary, you could initiate an Ajax request here
                // and then do the updating in a callback.

                // Update the modal's content.
                const contentModal = document.getElementById('content-model');
                contentModal.textContent = `Are you sure you want to delete this ${orderName} order?`
                const btnConfirmDelete = document.querySelector('.btn-confirm-delete');
                btnConfirmDelete.addEventListener('click', () => {
                    // Call API to delete order
                    deleteOrder(orderId);
                });

            })
        }

        function deleteOrder(id) {
            // add alert processing
            Swal.fire({
                title: 'Processing...',
                text: 'Deleting order...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                },
            });
            // console.log('Delete order', id);
            $.ajax({
                url: 'backend/handle-delete.php',
                type: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    // Close modal
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        const modal = bootstrap.Modal.getInstance(exampleModal);
                        modal.hide();
                        // Reload page
                        location.reload();
                    });

                },
                error: function(error) {
                    // console.log('Delete order error', error);
                    alert('Delete order error', error);
                }
            });
            // Close modal
            const modal = bootstrap.Modal.getInstance(exampleModal);
            modal.hide();
        }

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



        });

        function openDetail(id) {
            window.location.href = 'detail-order.php?id=' + id;
        }
    </script>

</body>

</html>