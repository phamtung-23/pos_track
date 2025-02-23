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

$resData = getDataFromJson('../../database/users.json');
if ($resData['status'] === 'success') {
  $users = $resData['data'];
} else {
  echo "<script>alert('Error reading users data!');</script>";
  exit();
}

$shipperData = array_filter($users, function ($user) {
  return $user['role'] === 'shipper';
});

echo "<script>";
echo "const shipperData = " . json_encode(array_values($shipperData)) . ";";
echo "</script>";


?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
  <title>Create new order</title>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <main class="">
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
      <h2>Create New Order</h2>
      <p>Welcome, <?php echo $fullName; ?>!</p>
    </div>
    <div class="container" style="padding-bottom: 100px; overflow: auto;">
      <div class="row">
        <form id="formSubmit" class="row g-3 needs-validation" novalidate>
          <div class="col-md-6">
            <label for="itemName" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="itemName" name="itemName" required>
            <div class="invalid-feedback">
              Please provide a valid item name.
            </div>
          </div>
          <div class="col-md-6">
            <label for="customerName" class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customerName" name="customerName" required>
            <div class="invalid-feedback">
              Please provide a valid customer name.
            </div>
          </div>
          <div class="col-md-6">
            <label for="weight" class="form-label">Weight</label>
            <div class="input-group has-validation">
              <span class="input-group-text" id="inputGroupPrepend">kg</span>
              <input type="number" class="form-control" id="weight" name="weight" aria-describedby="inputGroupPrepend" required>
              <div class="invalid-feedback">
                Please provide a valid weight.
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <label for="validationCustom04" class="form-label">Assign Delivery Person</label>
            <select class="form-select" id="validationCustom04" required>
              <option selected disabled value="">Select a delivery person</option>
              <?php
              foreach ($shipperData as $shipper) {
                echo "<option value='{$shipper['id']}'>{$shipper['fullname']} - {$shipper['email']}</option>";
              }
              ?>
            </select>
            <div class="invalid-feedback">
              Please select a valid delivery person.
            </div>
          </div>
          <div class="col-md-6">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" placeholder="Required enter address" required></textarea>
            <div class="invalid-feedback">
              Please enter a valid address.
            </div>
          </div>

          <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary btn-create-order" type="submit">Create order</button>
          </div>
        </form>
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

      // function get information from json file of user by email
      function getUserInfoByEmail(id) {
        var shipperInfo = shipperData.find(user => user.id === id);
        return shipperInfo;
      }

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      const forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }

          form.classList.add('was-validated')
        }, false)
      })

      // Create order
      const formSubmit = document.getElementById('formSubmit');
      $('.btn-create-order').click(function(e) {
        if (!formSubmit.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
          formSubmit.classList.add("was-validated");
        } else {
          e.preventDefault();
          const itemName = $('#itemName').val();
          const customerName = $('#customerName').val();
          const weight = $('#weight').val();
          const deliveryPerson = $('#validationCustom04').val();
          const address = $('#address').val();

          if (!itemName || !customerName || !weight || !deliveryPerson || !address) {
            alert('Please fill in all fields!');
            return;
          }

          const orderData = {
            itemName,
            customerName,
            weight,
            deliveryPerson,
            address
          };

          // add alert processing
          Swal.fire({
            title: 'Processing...',
            text: 'Creating order...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
              Swal.showLoading();
            },
          });

          // console.log(orderData);

          $.ajax({
            url: 'backend/handle-submit.php',
            type: 'POST',
            data: orderData,
            success: async function(response) {
              // console.log("response: ", response);
              if (response.success === true) {
                // send message to telegram
                const shipperInfo = getUserInfoByEmail(response.data.deliveryPersonId);
                let telegramMessage = '';

                telegramMessage = `**New Order Created!**\n` +
                  `Order ID: ${response.data.idOrder}\n` +
                  `Item Name: ${response.data.itemName}\n` +
                  `Weight: ${response.data.weight} kg\n` +
                  `Customer: ${response.data.customerName}\n` +
                  `Address: ${response.data.address}\n` +
                  `Created At: ${response.data.createdAt}\n`;

                // Gửi tin nhắn đến Telegram
                await fetch('../../sendTelegram.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({
                    message: telegramMessage,
                    id_telegram: shipperInfo.idTelegram // Truyền thêm thông tin operator_phone
                  })
                });
                Swal.fire({
                  position: "center",
                  icon: "success",
                  text: "Create order successfully!",
                  showConfirmButton: false,
                  timer: 2000
                }).then(() => {
                  window.location.href = 'index.php';
                });
              } else {
                Swal.close();
                alert('Error creating order!');
              }
            },
            error: function() {
              Swal.close();
              alert('Error creating order!');
            }
          });
        }

      });
    });
  </script>

</body>

</html>