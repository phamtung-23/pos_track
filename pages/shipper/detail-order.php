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
$userEmail = $_SESSION['user_id']; // operator_email matches user_id

$currentYear = date('Y');
$orderId = $_GET['id'];
$orderRes = getDataFromJson("../../database/orders/{$currentYear}/{$orderId}.json");
if ($orderRes['status'] === 'success') {
  $orderData = $orderRes['data'];
} else {
  echo "<script>alert('Error reading order data!');</script>";
  exit();
}

$resData = getDataFromJson('../../database/users.json');
if ($resData['status'] === 'success') {
  $users = $resData['data'];
} else {
  echo "<script>alert('Error reading users data!');</script>";
  exit();
}

$shipperData = array_filter($users, function ($user) use ($orderData) {
  return $user['id'] === $orderData['deliveryPersonId'];
});

echo "<script>";
echo "const shipperData = " . json_encode(array_values($shipperData)) . ";";
echo "const users = " . json_encode(array_values($users)) . ";";
echo "const orderData = " . json_encode($orderData) . ";";
echo "</script>";


?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
  <title>Order Detail</title>
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
      <h2>Order Detail</h2>
      <p>Welcome, <?php echo $fullName; ?>!</p>
    </div>
    <div class="container" style="padding-bottom: 100px; overflow: auto;">
      <div class="row">
        <div class="col-md-12">
          <div><b>Status:</b> <span class="<?php echo getColorByStatus($orderData['status']); ?>"><?php echo getStatusName($orderData['status']) ?></span></div>
        </div>
      </div>
      <div class="row">
        <form id="formSubmit" class="row g-3 needs-validation" novalidate>
          <div class="col-md-6">
            <label for="itemName" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="itemName" name="itemName" required value="<?php echo $orderData['itemName']; ?>" disabled>
            <div class="invalid-feedback">
              Please provide a valid item name.
            </div>
          </div>
          <div class="col-md-6">
            <label for="customerName" class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customerName" name="customerName" required value="<?php echo $orderData['customerName']; ?>" disabled>
            <div class="invalid-feedback">
              Please provide a valid customer name.
            </div>
          </div>
          <div class="col-md-6">
            <label for="weight" class="form-label">Weight</label>
            <div class="input-group has-validation">
              <span class="input-group-text" id="inputGroupPrepend">kg</span>
              <input type="number" class="form-control" id="weight" name="weight" aria-describedby="inputGroupPrepend" required value="<?php echo $orderData['weight']; ?>" disabled>
              <div class="invalid-feedback">
                Please provide a valid weight.
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <label for="validationCustom04" class="form-label">Assign Delivery Person</label>

            <?php
            foreach ($shipperData as $shipper) {
              echo "<input type='text' class='form-control'  aria-describedby='inputGroupPrepend' required value='{$shipper['fullname']} - {$shipper['email']}' disabled>";
            }
            ?>

            <div class="invalid-feedback">
              Please select a valid delivery person.
            </div>
          </div>
          <div class="col-md-6">
            <label for="address" class="form-label">Address (<span class="text-primary">
                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $orderData['address']; ?>" target="_blank">
                  Go to map
                  <img width="20" src="../../public/images/icon-map.png" />
                </a>
              </span>)
            </label>
            <textarea class="form-control" id="address" placeholder="Required enter address" required disabled>
              <?php echo $orderData['address']; ?>
            </textarea>
            <div class="invalid-feedback">
              Please enter a valid address.
            </div>
          </div>

          <?php
          if ($orderData['status'] === 'delivered') {
            echo "<div class='col-md-6'>";
            echo "<label for='note' class='form-label'>Note</label>";
            echo "<input type='text' class='form-control' id='note' name='note' required value='{$orderData['note']}' disabled>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<label for='images' class='form-label'>Images</label>";
            echo "<div class='d-flex flex-wrap gap-2'>";
            foreach ($orderData['images'] as $image) {
              echo "<a href='../../uploads/{$orderData['idOrder']}/{$image}' target='_blank' style='height: fix-content;'>";
              echo "<img src='../../uploads/{$orderData['idOrder']}/{$image}' class='img-thumbnail' style='max-width: 100px; max-height: 100px; object-fit: cover;'>";
              echo "</a>";
            }
            echo "</div>";
            echo "</div>";
          }
          ?>
        </form>
        <div class="col-12 d-flex justify-content-end gap-2 pe-4 pt-3">
          <?php
          if ($orderData['status'] === 'waiting_shipper_accept') {
            echo "<button class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#exampleModal'>Cancel</button>";
            echo "<button class='btn btn-success btn-order-accept' type='submit'>Accept</button>";
          } else if ($orderData['status'] === 'shipping') {
            echo "<button class='btn btn-warning btn-order-delivered' data-bs-toggle='modal' data-bs-target='#deliveredModal' >Confirm delivered</button>";
          }
          ?>
          <!-- <button class="btn btn-danger btn-order-cancel" type="submit">Cancel</button> -->
          <!-- <button class="btn btn-success btn-order-accept" type="submit">Accept</button> -->
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Confirm</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to cancel this order?
          </div>
          <div class="modal-footer">
            <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">cancel</button> -->
            <button type="button" class="btn btn-primary btn-order-cancel">Confirm</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="deliveredModal" tabindex="-1" aria-labelledby="deliveredModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="deliveredModalLabel">Confirm Delivered</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12 text-center">
                <!-- Image Previews Container -->
                <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2"></div>
              </div>
              <div class="col-md-12 mt-3">
                <label for="imageUpload" class="form-label">Upload Images</label>
                <input type="file" class="form-control" id="imageUpload" accept="image/*" multiple>
              </div>
              <div class="col-md-12 mt-3">
                <label for="note" class="form-label">Note</label>
                <input type="text" class="form-control" id="note" name="note" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success btn-confirm-delivered">Submit</button>
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
    $(document).ready(function() {
      let locationInterval = null;

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
        var user = users.find(user => user.id === id);
        return user;
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
      $('.btn-order-accept').click(function(e) {
        if (!formSubmit.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
          formSubmit.classList.add("was-validated");
        } else {
          e.preventDefault();

          // add alert processing
          Swal.fire({
            title: 'Processing...',
            text: 'Accepting order...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
              Swal.showLoading();
            },
          });

          // console.log(orderData);

          $.ajax({
            url: 'backend/handle-accept.php',
            type: 'POST',
            data: {
              id: '<?php echo $orderId; ?>'
            },
            success: async function(response) {
              // console.log("response: ", response);
              if (response.success === true) {
                // send message to telegram
                const userShop = getUserInfoByEmail(response.data.createdBy);
                const shipperInfo = getUserInfoByEmail(response.data.deliveryPersonId);
                let telegramMessage = '';

                telegramMessage = `**The Order Accepted!**\n` +
                  `Shipper: ${shipperInfo.fullname} - ${shipperInfo.phone}\n` +
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
                    id_telegram: userShop.idTelegram // Truyền thêm thông tin operator_phone
                  })
                });
                Swal.fire({
                  position: "center",
                  icon: "success",
                  text: response.message,
                  showConfirmButton: false,
                  timer: 2000
                }).then(() => {
                  // Reload page
                  location.reload();
                });
              } else {
                Swal.close();
                alert(response.message);
              }
            },
            error: function() {
              Swal.close();
              alert('Error accepting order');
            }
          });
        }

      });

      // Cancel order
      $('.btn-order-cancel').click(function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Processing...',
          text: 'Canceling order...',
          allowOutsideClick: false,
          showConfirmButton: false,
          willOpen: () => {
            Swal.showLoading();
          },
        });

        $.ajax({
          url: 'backend/handle-cancel.php',
          type: 'POST',
          data: {
            id: '<?php echo $orderId; ?>'
          },
          success: async function(response) {
            if (response.success === true) {
              // send message to telegram
              const userShop = getUserInfoByEmail(response.data.createdBy);
              const shipperInfo = getUserInfoByEmail(response.data.deliveryPersonId);
              let telegramMessage = '';

              telegramMessage = `**The Order Canceled!**\n` +
                `Shipper: ${shipperInfo.fullname} - ${shipperInfo.phone}\n` +
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
                  id_telegram: userShop.idTelegram // Truyền thêm thông tin operator_phone
                })
              });
              Swal.fire({
                position: "center",
                icon: "success",
                text: response.message,
                showConfirmButton: false,
                timer: 2000
              }).then(() => {
                // Reload page
                location.reload();
              });
            } else {
              Swal.close();
              const modal = bootstrap.Modal.getInstance(document.getElementById('exampleModal'));
              modal.hide();
              alert(response.message);
            }
          },
          error: function() {
            Swal.close();
            const modal = bootstrap.Modal.getInstance(document.getElementById('exampleModal'));
            modal.hide();
            alert('Error canceling order');
          }
        });
      });

      // Confirm delivered
      $('.btn-confirm-delivered').click(function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Processing...',
          text: 'Confirming delivered...',
          allowOutsideClick: false,
          showConfirmButton: false,
          willOpen: () => {
            Swal.showLoading();
          },
        });

        const formData = new FormData();
        const files = document.getElementById('imageUpload').files;
        const note = document.getElementById('note').value;

        if (files.length === 0) {
          Swal.close();
          alert('Please upload at least one image');
          return;
        }

        formData.append('id', '<?php echo $orderId; ?>');
        formData.append('note', note);
        Array.from(files).forEach(file => {
          formData.append('images[]', file);
        });

        $.ajax({
          url: 'backend/handle-delivered.php',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: async function(response) {
            if (response.success === true) {
              // send message to telegram
              const userShop = getUserInfoByEmail(response.data.createdBy);
              const shipperInfo = getUserInfoByEmail(response.data.deliveryPersonId);
              let telegramMessage = '';

              telegramMessage = `**The Order Delivered!**\n` +
                `Shipper: ${shipperInfo.fullname} - ${shipperInfo.phone}\n` +
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
                  id_telegram: userShop.idTelegram // Truyền thêm thông tin operator_phone
                })
              });
              Swal.fire({
                position: "center",
                icon: "success",
                text: response.message,
                showConfirmButton: false,
                timer: 2000
              }).then(() => {
                // clear interval
                if (locationInterval) {
                  clearInterval(locationInterval);
                }
                // Reload page
                location.reload();
              });
            } else {
              Swal.close();
              const modal = bootstrap.Modal.getInstance(document.getElementById('deliveredModal'));
              modal.hide();
              alert(response.message);
            }
          },
          error: function() {
            Swal.close();
            const modal = bootstrap.Modal.getInstance(document.getElementById('deliveredModal'));
            modal.hide();
            alert('Error confirming delivered');
          }
        });
      });

      // Show image preview
      document.getElementById('imageUpload').addEventListener('change', function(event) {
        const files = event.target.files;
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        imagePreviewContainer.innerHTML = ''; // Clear previous previews

        if (files.length > 0) {
          Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) { // Ensure it's an image file
              const reader = new FileReader();
              reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('img-thumbnail');
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.objectFit = 'cover';
                imagePreviewContainer.appendChild(img);
              };
              reader.readAsDataURL(file);
            }
          });
        }
      });


      function getLocation() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            (position) => {
              console.log("Latitude:", position.coords.latitude);
              console.log("Longitude:", position.coords.longitude);

              // Send location to server
              $.ajax({
                url: 'backend/handle-location.php',
                type: 'POST',
                data: {
                  id: '<?php echo $orderId; ?>',
                  latitude: position.coords.latitude,
                  longitude: position.coords.longitude
                },
                success: function(response) {
                  // console.log("Location sent successfully");
                },
                error: function() {
                  console.error("Error sending location");
                }
              });
            },
            (error) => {
              console.error("Error getting location:", error.message);
            }
          );
        } else {
          console.error("Geolocation is not supported by this browser.");
        }
      }

      // Call getLocation() immediately, then every 1 minute (60000 ms)
      getLocation(); // First call immediately

      if (orderData.status == "shipping") {
        locationInterval = setInterval(getLocation, 60000);
      }

      // Cleanup interval when leaving the page
      window.addEventListener('beforeunload', function() {
        if (locationInterval) {
          clearInterval(locationInterval);
        }
      });
    });
  </script>

</body>

</html>