<?php include('layouts/header.php') ?>
<?php
// Sanitize input to prevent SQL injection
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get total rows
$total_rows_sql = "SELECT COUNT(*) AS total FROM rooms
                   INNER JOIN room_type ON rooms.category_id = room_type.category_id";
if (!empty($search)) {
  $total_rows_sql .= " WHERE room_type.category_name LIKE '%$search%' OR rooms.room_id LIKE '%$search%'";
}

$total_rows_result = mysqli_query($conn, $total_rows_sql);
$total_rows = mysqli_fetch_assoc($total_rows_result)['total'];

$limit = isset($_GET['limit']) ? $_GET['limit'] : 5;
$sorting = isset($_GET['sort']) ? $_GET['sort'] : 'ASC';
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

$pages = ceil($total_rows / $limit);
$offset = ($current_page - 1) * $limit;

$sort_column = 'rooms.room_id';
$sort_order = ($sorting == 'desc') ? 'DESC' : 'ASC';

$sql = "SELECT rooms.room_id, rooms.status, rooms.created_at, room_type.category_name FROM rooms
        INNER JOIN room_type ON rooms.category_id = room_type.category_id";

if (!empty($search)) {
  $sql .= " WHERE room_type.category_name LIKE '%$search%' OR rooms.room_id LIKE '%$search%'";
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
          <button type="button" class="btn btn-primary custom-btn" data-toggle="modal" data-target="#addRoom" style="margin-left: 10px ;width: 250px; padding: 0px;">Add Room</button>
        </form>

      </div>
    </div>
  </div>
  <div class="client-table">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Room No.</th>
          <th scope="col">Room Type</th>
          <th scope="col">Status</th>
          <th scope="col">Date Created</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $modal_id_update = 'updateInfo_' . $row['room_id'];
            $modal_id_delete = 'deleteRoom_' . $row['room_id'];
            $status = $row['status'];
        ?>
            <tr>
              <th scope="row"><?= $row['room_id'] ?></th>
              <td><?= $row['category_name'] ?></td>
              <td class="<?php echo ($row['status'] == 'Available') ? 'text-success fw-bold' : 'text-warning fw-bold'; ?>">
                <?= ucfirst($row['status']); ?>
              </td>
              <td><?= date('F j, Y', strtotime($row['created_at'])) ?></td>
              <td>
                <button class="btn btn-primary" data-bs-target="#<?= $modal_id_update ?>" data-bs-toggle="modal">Update Info</button>
                <button class="btn btn-danger" data-bs-target="#<?= $modal_id_delete ?>" data-bs-toggle="modal">Delete</button>
              </td>
            </tr>

            <!-- Update Info Modal -->
            <div class="modal fade" id="<?= $modal_id_update ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Room #<?= $row['room_id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form method="post" action="">
                      <div class="mb-3">
                        <label for="room_id" class="form-label">Room ID</label>
                        <input type="text" class="form-control" name="room_id" value="<?= $row['room_id'] ?>" readonly>
                      </div>
                      <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" name="category">
                          <?php
                          // Fetch room types for the select options
                          $category_sql = "SELECT * FROM room_type";
                          $category_result = mysqli_query($conn, $category_sql);
                          while ($category_row = mysqli_fetch_assoc($category_result)) {
                            $selected = $category_row['category_id'] == $row['category_id'] ? 'selected' : '';
                            echo "<option value='{$category_row['category_id']}' $selected>{$category_row['category_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="status" class="form-label">Room Status</label>
                        <select class="form-select" name="status">
                          <option value="Available" <?= $status == 'Available' ? 'selected' : '' ?>>Available</option>
                          <option value="Unavailable" <?= $status == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                        </select>
                      </div>
                      <input type="hidden" name="original_room_id" value="<?= $row['room_id'] ?>">
                      <button type="submit" class="btn btn-primary" name="update_info">Save changes</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Delete Room #<?= $row['room_id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form method="post" action="">
                      <p>Are you sure you want to delete Room #<?= $row['room_id'] ?>?</p>
                      <input type="hidden" name="room_id" value="<?= $row['room_id'] ?>">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger" name="delete_room">Delete</button>
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
<!-- Add Rooms Modal -->
<div class="modal fade" id="addRoom" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addRoomLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addRoomLabel">Add Room</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST">
        <div class="modal-body">
          <div class="form-group mb-4">
            <label>Category</label>
            <?php
            // Fetch room types
            $sql = "SELECT * FROM room_type";
            $result = mysqli_query($conn, $sql);

            ?>
            <select name="category" class="form-control form-control">
              <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <option value="<?= $row['category_id'] ?>"><?php echo htmlspecialchars($row['category_name']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group mb-4">
            <label>Rooms</label>
            <input type="text" name="room_id" class="form-control" placeholder="Enter Room Number">
          </div>
          <div class="form-group mb-3">
            <label>Category</label>
            <select name="status" class="form-control form-control">
              <option>Available</option>
              <option>Unavailable</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" name="addroom" class="btn btn-primary custom-btn">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
if (isset($_POST['update_info'])) {
  // Sanitize input
  $original_room_id = mysqli_real_escape_string($conn, $_POST['original_room_id']);
  $category_id = mysqli_real_escape_string($conn, $_POST['category']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);
  $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
  // Update query
  $update_sql = "UPDATE rooms SET category_id = '$category_id', status = '$status', rooms = '$room_id' WHERE room_id = '$original_room_id'";

  if (mysqli_query($conn, $update_sql)) {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'success',
            title: 'Room info updated successfully!',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
    exit();
  } else {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Error. Please Try Again',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
    exit();
  }
}

if (isset($_POST['delete_room'])) {
  // Sanitize input
  $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);

  // Delete query
  $delete_sql = "DELETE FROM rooms WHERE room_id = '$room_id'";

  if (mysqli_query($conn, $delete_sql)) {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'success',
            title: 'Room deleted successfully!',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
    exit();
  } else {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Error. Please Try Again',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
    exit();
  }
}

if (isset($_POST['addroom'])) {
  // Sanitize input
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);

  // Check if room_id already exists
  $check_sql = "SELECT * FROM rooms WHERE room_id = '$room_id'";
  $check_result = mysqli_query($conn, $check_sql);

  if (mysqli_num_rows($check_result) > 0) {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Room ID already exists. Please choose a different Room ID.',
        });
        </script>";
    echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
    exit();
  } else {
    // Insert query
    $sql = "INSERT INTO rooms (room_id, category_id, status, rooms) VALUES ('$room_id', '$category', '$status', '$room_id')";

    if (mysqli_query($conn, $sql)) {
      $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'success',
                title: 'New room added successfully!',
            });
            </script>";
      echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
      exit();
    } else {
      $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'Error. Please Try Again',
            });
            </script>";
      echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
      exit();
    }
  }
}
?>


<?php include('layouts/footer.php') ?>