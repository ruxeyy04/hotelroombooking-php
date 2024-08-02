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
    <p class="title">Users</p>
    <div class="row">
      <div class="col-lg-6">
        <form class="d-flex" role="search" action="users.php" method="GET" style="width: 100%; padding: 40px 30px 20px;">
          <input class="form-control me-2" type="search" name="search" placeholder="Search User" aria-label="Search">
          <button class="btn btn-outline-success" type="submit" style="font-size: 18px; font-weight: bold;">Search</button>
          <button type="button" class="btn btn-primary custom-btn" data-toggle="modal" data-target="#addUser" style="margin-left: 10px ;width: 150px; padding: 0px;">Add User</button>
        </form>
      </div>
    </div>
  </div>
  <div class="client-table">
    <table class="table lms_table_active">
      <thead>
        <tr>
          <th>User ID</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Email</th>
          <th>Usertype</th>
          <th>Contact Number</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $total_rows_sql = "SELECT COUNT(*) AS total FROM userinfo ";

        if (!empty($search)) {
          $total_rows_sql .= " WHERE userinfo.username LIKE '%$search%' OR CONCAT(userinfo.fname, ' ', userinfo.lname) LIKE '%$search%'";
        }
        if (!empty($search)) {
          $total_items_query = "SELECT COUNT(*) as total FROM userinfo  WHERE userinfo.username LIKE '%$search%' OR CONCAT(userinfo.fname, ' ', userinfo.lname) LIKE '%$search%'";

          $stmt = $conn->prepare($total_items_query);
        } else {
          $total_items_query = "SELECT COUNT(*) as total FROM userinfo";

          $stmt = $conn->prepare($total_items_query);
        }
        $stmt->execute();
        $total_items_result = $stmt->get_result();
        $total_items_row = $total_items_result->fetch_assoc();

        $total_items = $total_items_row['total'];
        $stmt->close();

        $total_pages = ceil($total_items / $limit);

        $total_rows_result = mysqli_query($conn, $total_rows_sql);
        $total_rows = mysqli_fetch_assoc($total_rows_result)['total'];

        $sorting = isset($_GET['sort']) ? $_GET['sort'] : 'ASC';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $pages = ceil($total_rows / $limit);
        $offset = ($page - 1) * $limit;

        // Sort by user id
        $sort_column = 'userinfo.userid';
        $sort_order = ($sorting == 'desc') ? 'DESC' : 'ASC';

        $sql = "
                            SELECT *
                            FROM userinfo
                        ";

        if (!empty($search)) {
          // Add WHERE clause for search if a search query is provided
          $sql .= " WHERE userinfo.username LIKE '%$search%' OR CONCAT(userinfo.fname, ' ', userinfo.lname) LIKE '%$search%'";
        }

        $sql .= " ORDER BY $sort_column $sort_order
                            LIMIT $limit OFFSET $offset";

        $result = mysqli_query($conn, $sql);

        $table_rows = '';

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $modal_id = 'editUser_' . $row['userid'];
            $deleteuser = 'deleteUser_' . $row['userid'];
        ?>
            <tr>
              <td><a href="#"><?= $row['userid'] ?></a></td>
              <td> <?= $row['fname'] . ' ' . $row['lname'] ?></td>
              <td><?= $row['username'] ?></td>
              <td><?= $row['email'] ?></td>
              <td><?= $row['usertype'] ?></td>
              <td><?= $row['contact'] ?></td>
              <td class="text-center d-flex"><button class="btn btn-info me-1" data-toggle="modal" data-target="#<?= $modal_id ?>">Edit</button>
                <button class="btn btn-danger ml-1" data-toggle="modal" data-target="#<?= $deleteuser ?>">Delete</button>
              </td>
            </tr>
            <!-- Modal -->
            <div class="modal fade" id="<?= $deleteuser ?>" tabindex="-1" aria-labelledby="deleteUserLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserLabel">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want to delete this user#<?= $row['userid'] ?>? </p>
                  </div>
                  <div class="modal-footer">
                    <form action="" method="post">
                      <input type="hidden" name="userid" value="<?= $row['userid'] ?>">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-labelledby="<?= $modal_id ?>Label" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="<?= $modal_id ?>Label">Edit User</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                      <input type="hidden" name="userid" value="<?= $row['userid'] ?>">
                      <div class="form-floating mb-4">
                        <input type="text" name="fname" value="<?= $row['fname'] ?>" class="form-control form-control-sm" required />
                        <label for="fname">First Name</label>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="text" name="midname" value="<?= $row['midname'] ?>" class="form-control form-control-sm" />
                        <label for="midname">Middle Name</label>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="text" name="lname" value="<?= $row['lname'] ?>" class="form-control form-control-sm" required />
                        <label for="lname">Last Name</label>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="text" name="username" value="<?= $row['username'] ?>" class="form-control form-control-sm" required />
                        <label for="username">Username</label>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="email" name="email" value="<?= $row['email'] ?>" class="form-control form-control-sm" required />
                        <label for="email">Email</label>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="text" name="contact" value="<?= $row['contact'] ?>" class="form-control form-control-sm" />
                        <label for="contact">Contact Number</label>
                      </div>
                      <div class="form-floating mb-4">
                        <select class="form-control form-control-sm" name="gender" required>
                          <option disabled selected>--- Select Gender ---</option>
                          <option value="Male" <?= $row['gender'] == "Male" ? 'selected' : '' ?>>Male</option>
                          <option value="Female" <?= $row['gender'] == "Female" ? 'selected' : '' ?>>Female</option>
                        </select>
                        <label>Gender <?= $row['gender'] ?></label>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="password" name="resetpassword" class="form-control form-control-sm" />
                        <label for="resetpassword">Reset Password(Only for Resetting Password)</label>
                      </div>
                      <div class="form-floating mb-4">
                        <select class="form-control form-control-sm" name="usertype" required>
                          <option disabled selected>--- Select Usertype ---</option>
                          <option value="Client" <?= $row['usertype'] == 'Client' ? 'selected' : '' ?>>Client</option>
                          <option value="Incharge" <?= $row['usertype'] == 'Incharge' ? 'selected' : '' ?>>Incharge</option>
                          <option value="Admin" <?= $row['usertype'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <label>Usertype</label>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                      <button type="submit" name="update_user" class="btn btn-primary">Apply Changes</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        <?php
          }
        } else {
          echo '<tr><td colspan="7" class="text-center">No data available</td></tr>';
        }
        ?>
      </tbody>

    </table>


  </div>
  <div class="d-flex justify-content-center">
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Previous Page Link -->
            <?php
            $prev_page_url = '?' . http_build_query(array_merge($_GET, ['page' => $page - 1]));
            ?>
            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?= $page <= 1 ? '#' : $prev_page_url ?>" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                </a>
            </li>

            <!-- Page Numbers -->
            <?php
            $range = 2; // Number of pages to show on either side of the current page
            $start = max(1, $page - $range);
            $end = min($total_pages, $page + $range);

            if ($start > 1) {
                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a></li>';
                if ($start > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            for ($i = $start; $i <= $end; $i++) {
                echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '">' . $i . '</a></li>';
            }

            if ($end < $total_pages) {
                if ($end < $total_pages - 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages])) . '">' . $total_pages . '</a></li>';
            }
            ?>

            <!-- Next Page Link -->
            <?php
            $next_page_url = '?' . http_build_query(array_merge($_GET, ['page' => $page + 1]));
            ?>
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?= $page >= $total_pages ? '#' : $next_page_url ?>" aria-label="Next">
                    <span aria-hidden="true">»</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

</div>
<!-- Add User Modal -->
<div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserLabel">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-floating mb-4">
            <input type="text" name="fname" class="form-control form-control-sm" required />
            <label for="fname">First Name</label>
          </div>
          <div class="form-floating mb-4">
            <input type="text" name="lname" class="form-control form-control-sm" required />
            <label for="lname">Last Name</label>
          </div>
          <div class="form-floating mb-4">
            <input type="text" name="username" class="form-control form-control-sm" required />
            <label for="username">Username</label>
          </div>
          <div class="form-floating mb-4">
            <input type="email" name="email" class="form-control form-control-sm" required />
            <label for="email">Email</label>
          </div>
          <div class="form-floating mb-4">
            <input type="text" name="contact" class="form-control form-control-sm" required />
            <label for="contact">Contact Number</label>
          </div>
          <div class="form-floating mb-4">
            <input type="password" name="password" class="form-control form-control-sm" required />
            <label for="password">Password</label>
          </div>
          <div class="form-floating mb-4">
            <select class="form-control form-control-sm" name="gender" id="gender">
              <option disabled selected>--- Select Gender ---</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
            <label for="gender">Gender</label>
          </div>
          <div class="form-floating mb-4">
            <select class="form-control form-control-sm" name="usertype" id="usertype" required>
              <option disabled selected>--- Select Usertype ---</option>
              <option value="Client">Client</option>
              <option value="Incharge">Incharge</option>
              <option value="Admin">Admin</option>
            </select>
            <label for="usertype">Usertype</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
  // Connect to the database (make sure $conn is defined somewhere before this block)
  // $conn = new mysqli($servername, $username, $password, $dbname);

  $fname = mysqli_real_escape_string($conn, $_POST['fname']);
  $lname = mysqli_real_escape_string($conn, $_POST['lname']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $contact = mysqli_real_escape_string($conn, $_POST['contact']); // Add contact number
  $gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);


  // Insert into userinfo table
  $insert_info_query = "
        INSERT INTO userinfo (fname, lname, username, email, contact, gender, password, usertype) 
        VALUES ('$fname', '$lname', '$username', '$email', '$contact', '$gender', '$password', '$usertype')
    ";

  // Execute the query
  if (mysqli_query($conn, $insert_info_query)) {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'success',
            title: 'New user added successfully!',
        });
    </script>";
    echo '<meta http-equiv="refresh" content="0;url=users.php">';
    exit();
  } else {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Error adding user info: " . mysqli_error($conn) . "',
        });
    </script>";
    echo '<meta http-equiv="refresh" content="0;url=users.php">';
    exit();
  }
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
  $userid = mysqli_real_escape_string($conn, $_POST['userid']);
  $fname = mysqli_real_escape_string($conn, $_POST['fname']);
  $midname = mysqli_real_escape_string($conn, $_POST['midname']); // Add midname
  $lname = mysqli_real_escape_string($conn, $_POST['lname']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $contact = mysqli_real_escape_string($conn, $_POST['contact']); // Add contact number
  $gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';
  $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);
  $resetpassword = mysqli_real_escape_string($conn, $_POST['resetpassword']);

  // Construct the update query for userinfo
  $update_info_query = "
        UPDATE userinfo 
        SET fname = '$fname', midname = '$midname', lname = '$lname', username = '$username', 
            email = '$email', contact = '$contact', gender = '$gender', usertype = '$usertype'";

  // If resetpassword is provided, include it in the update query without hashing
  if (!empty($resetpassword)) {
    $update_info_query .= ", password = '$resetpassword'";
  }

  $update_info_query .= " WHERE userid = '$userid'";

  // Execute the query
  if (mysqli_query($conn, $update_info_query)) {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'success',
            title: 'User information updated successfully!',
        });
    </script>";
    echo '<meta http-equiv="refresh" content="0;url=users.php">';
    exit();
  } else {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Error updating user information: " . mysqli_error($conn) . "',
        });
    </script>";
    echo '<meta http-equiv="refresh" content="0;url=users.php">';
    exit();
  }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $userid = $_POST['userid'];

  $stmt = $conn->prepare("DELETE FROM userinfo WHERE userid = ?");
  $stmt->bind_param("i", $userid);

  if ($stmt->execute()) {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'success',
            title: 'User deleted successfully!',
        });
    </script>";
    echo '<meta http-equiv="refresh" content="0;url=users.php">';
    exit();
  } else {
    $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Cannot delete user',
        });
    </script>";
    echo '<meta http-equiv="refresh" content="0;url=users.php">';
    exit();
  }

  $stmt->close();
}
?>

<?php include('layouts/footer.php') ?>