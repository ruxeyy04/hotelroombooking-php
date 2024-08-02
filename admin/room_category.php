<?php include('layouts/header.php') ?>
<?php
// Sanitize input to prevent SQL injection
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get total rows
$total_rows_sql = "SELECT COUNT(*) AS total FROM room_type";
if (!empty($search)) {
  $total_rows_sql .= " WHERE category_name LIKE '%$search%'";
}

$total_rows_result = mysqli_query($conn, $total_rows_sql);
$total_rows = mysqli_fetch_assoc($total_rows_result)['total'];

$limit = isset($_GET['limit']) ? $_GET['limit'] : 5;
$sorting = isset($_GET['sort']) ? $_GET['sort'] : 'ASC';
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

$pages = ceil($total_rows / $limit);
$offset = ($current_page - 1) * $limit;

$sort_column = 'category_name';
$sort_order = ($sorting == 'desc') ? 'DESC' : 'ASC';

$sql = "SELECT * FROM room_type";

if (!empty($search)) {
  $sql .= " WHERE category_name LIKE '%$search%'";
}

$sql .= " ORDER BY $sort_column $sort_order
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
?>


<div class="main p-3">
  <div class="search-bar">
    <div class="row">
      <div class="col-lg-4">
        <form class="d-flex" role="search" style="width: 100%; padding: 40px 30px;" method="get">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
          <button class="btn btn-outline-success" type="submit" style="font-size: 18px; font-weight: bold;">Search</button>
          <button type="button" class="btn btn-primary custom-btn" data-toggle="modal" data-target="#addRoomtype" style="margin-left: 10px ;width: 380px; padding: 0px;">Add Category</button>
        </form>

      </div>
    </div>
  </div>
  <div class="client-table">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Room Type ID.</th>
          <th scope="col">Name</th>
          <th scope="col">Description</th>
          <th scope="col">Price</th>
          <th scope="col">Capacity</th>
          <th scope="col">Services</th>
          <th scope="col">Bed</th>
          <th scope="col">Date Created</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $modal_id_update = 'updateInfo_' . $row['category_id'];
            $modal_id_delete = 'deleteType_' . $row['category_id'];
        ?>
            <tr>
              <th scope="row"><?= $row['category_id'] ?></th>
              <td><?= $row['category_name'] ?></td>
              <td><?= $row['description'] ?></td>
              <td><?= $row['price'] ?></td>
              <td><?= $row['capacity'] ?></td>
              <td><?= $row['services'] ?></td>
              <td><?= $row['bed'] ?></td>
              <td><?= date('F j, Y', strtotime($row['created_at'])) ?></td>
              <td>
                <button style="margin-bottom: 10px;" class="btn btn-primary" data-bs-target="#<?= $modal_id_update ?>" data-bs-toggle="modal">Update</button>
                <button class="btn btn-danger" data-bs-target="#<?= $modal_id_delete ?>" data-bs-toggle="modal">Delete</button>
              </td>
            </tr>

            <!-- Update Info Modal -->
            <div class="modal fade" id="<?= $modal_id_update ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Room Type #<?= $row['category_id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form method="post" action="" enctype="multipart/form-data">
                      <div class="mb-3">
                        <label for="bed" class="form-label">Room Type Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                      </div>
                      <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="category_name" value="<?= $row['category_name'] ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" required><?= $row['description'] ?></textarea>
                      </div>
                      <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" name="price" value="<?= $row['price'] ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="text" class="form-control" name="capacity" value="<?= $row['capacity'] ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="services" class="form-label">Services</label>
                        <input type="text" class="form-control" name="services" value="<?= $row['services'] ?>">
                      </div>
                      <div class="mb-3">
                        <label for="bed" class="form-label">Bed</label>
                        <input type="text" class="form-control" name="bed" value="<?= $row['bed'] ?>">
                      </div>
                      <button type="submit" class="btn btn-primary" name="update_info">Save changes</button>
                      <input type="hidden" name="category_id" value="<?= $row['category_id'] ?>">
                    </form>
                  </div>
                </div>
              </div>
            </div>


            <!-- Delete Room Modal -->
            <div class="modal fade" id="<?= $modal_id_delete ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Room Type #<?= $row['category_id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form method="post" action="">
                      <p>Are you sure you want to delete Room Type #<?= $row['category_id'] ?>?</p>
                      <input type="hidden" name="category_id" value="<?= $row['category_id'] ?>">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger" name="delete_type">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
        <?php
          }
        } else {
          echo '<tr><td colspan="5" class="text-center">No data available</td></tr>';
        }
        ?>
      </tbody>
    </table>


  </div>
  <div class="d-flex justify-content-center">
    <nav class="mt-4">
      <ul class="pagination justify-content-center">
        <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
          <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <?php for ($i = 1; $i <= $pages; $i++) : ?>
          <li class="page-item <?php echo $current_page == $i ? 'active' : ''; ?>">
            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?php echo $current_page == $pages ? 'disabled' : ''; ?>">
          <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>
<!-- Add Room Type Modal -->
<div class="modal fade" id="addRoomtype" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addRoomLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addRoomLabel">Add Room Type</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group mb-4">
            <label>Category Name</label>
            <input type="text" name="category_name" class="form-control" placeholder="Enter Category Name" required>
          </div>
          <div class="form-group mb-4">
            <label>Description</label>
            <textarea name="description" class="form-control" placeholder="Enter Description" required></textarea>
          </div>
          <div class="form-group mb-4">
            <label>Price</label>
            <input type="number" name="price" class="form-control" placeholder="Enter Price" required>
          </div>
          <div class="form-group mb-4">
            <label>Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
          </div>
          <div class="form-group mb-4">
            <label>Capacity</label>
            <input type="text" name="capacity" class="form-control" placeholder="Enter Capacity" required>
          </div>
          <div class="form-group mb-4">
            <label>Services</label>
            <input type="text" name="services" class="form-control" placeholder="Enter Services">
          </div>
          <div class="form-group mb-4">
            <label>Bed</label>
            <input type="text" name="bed" class="form-control" placeholder="Enter Bed Type">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" name="addroomtype" class="btn btn-primary custom-btn">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
if (isset($_POST['update_info'])) {

  $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
  $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  $price = mysqli_real_escape_string($conn, $_POST['price']);
  $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
  $services = mysqli_real_escape_string($conn, $_POST['services']);
  $bed = mysqli_real_escape_string($conn, $_POST['bed']);

  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

    $fetch_image_sql = "SELECT image FROM room_type WHERE category_id = '$category_id'";
    $fetch_image_result = mysqli_query($conn, $fetch_image_sql);
    $image_row = mysqli_fetch_assoc($fetch_image_result);
    $previous_image = $image_row['image'];

    if (file_exists("../img/room/$previous_image")) {
      unlink("../img/room/$previous_image");
    }

    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_unique_name = uniqid() . '_' . $image_name;
    $image_path = '../img/room/' . $image_unique_name;

    if (move_uploaded_file($image_tmp, $image_path)) {

      $update_sql = "UPDATE room_type SET category_name = '$category_name', description = '$description', price = '$price', capacity = '$capacity', services = '$services', bed = '$bed', image = '$image_unique_name' WHERE category_id = '$category_id'";
    } else {

      $_SESSION['alert'] = "<script>
          Toast.fire({
              icon: 'error',
              title: 'Error uploading new image file. Room type info not updated.',
          });
          </script>";
      echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
      exit();
    }
  } else {

    $update_sql = "UPDATE room_type SET category_name = '$category_name', description = '$description', price = '$price', capacity = '$capacity', services = '$services', bed = '$bed' WHERE category_id = '$category_id'";
  }


  if (mysqli_query($conn, $update_sql)) {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'success',
            title: 'Room type info updated successfully!',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
    exit();
  } else {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Error. Please Try Again',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
    exit();
  }
}


if (isset($_POST['delete_type'])) {

  $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

  $fetch_image_sql = "SELECT image FROM room_type WHERE category_id = '$category_id'";
  $fetch_image_result = mysqli_query($conn, $fetch_image_sql);
  $image_row = mysqli_fetch_assoc($fetch_image_result);
  $image_filename = $image_row['image'];

  $check_rooms_sql = "SELECT COUNT(*) AS total_rooms FROM rooms WHERE category_id = '$category_id'";
  $check_rooms_result = mysqli_query($conn, $check_rooms_sql);
  $total_rooms = mysqli_fetch_assoc($check_rooms_result)['total_rooms'];

  if ($total_rooms > 0) {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Cannot delete room type. Rooms are associated with this category.',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
    exit();
  }

  $delete_sql = "DELETE FROM room_type WHERE category_id = '$category_id'";

  if (mysqli_query($conn, $delete_sql)) {

    if (unlink("../img/room/$image_filename")) {
      $_SESSION['alert'] = "<script>
          Toast.fire({
              icon: 'success',
              title: 'Room type and associated image deleted successfully!',
          });
          </script>";
      echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
      exit();
    } else {

      $_SESSION['alert'] = "<script>
          Toast.fire({
              icon: 'error',
              title: 'Error deleting image file',
          });
          </script>";
      echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
      exit();
    }
  } else {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Error. Please Try Again',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
    exit();
  }
}



if (isset($_POST['addroomtype'])) {

  $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  $price = mysqli_real_escape_string($conn, $_POST['price']);
  $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
  $services = mysqli_real_escape_string($conn, $_POST['services']);
  $bed = mysqli_real_escape_string($conn, $_POST['bed']);

  if (isset($_FILES['image'])) {
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_unique_name = uniqid() . '_' . $image_name;
    $image_path = '../img/room/' . $image_unique_name;

    if (move_uploaded_file($image_tmp, $image_path)) {

      $sql = "INSERT INTO room_type (category_name, description, price, image, capacity, services, bed) VALUES ('$category_name', '$description', '$price', '$image_unique_name', '$capacity', '$services', '$bed')";

      if (mysqli_query($conn, $sql)) {
        $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'success',
                title: 'New room type added successfully!',
            });
            </script>";
        echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
        exit();
      } else {
        $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'Error. Please Try Again',
            });
            </script>";
        echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
        exit();
      }
    } else {
      $_SESSION['alert'] = "<script>
          Toast.fire({
              icon: 'error',
              title: 'Error uploading image. Please try again.',
          });
          </script>";
      echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
      exit();
    }
  } else {
    $_SESSION['alert'] = "<script>
          Toast.fire({
              icon: 'error',
              title: 'Image not uploaded. Please choose an image.',
          });
          </script>";
    echo '<meta http-equiv="refresh" content="0;url=room_category.php">';
    exit();
  }
}

?>


<?php include('layouts/footer.php') ?>