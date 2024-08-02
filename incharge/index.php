<?php include('layouts/header.php') ?>
<div class="main">
  <p class="title">Dashboard</p>
  <?php
  // Total Rooms
  $totalRoomsQuery = "SELECT COUNT(*) as total_rooms FROM rooms";
  $totalRoomsResult = $conn->query($totalRoomsQuery);
  $totalRooms = $totalRoomsResult->fetch_assoc()['total_rooms'];

  // Clients
  $totalClientsQuery = "SELECT COUNT(*) as total_clients FROM userinfo WHERE usertype = 'client'";
  $totalClientsResult = $conn->query($totalClientsQuery);
  $totalClients = $totalClientsResult->fetch_assoc()['total_clients'];

  // Booked Rooms
  $bookedRoomsQuery = "SELECT COUNT(*) as booked_rooms FROM booking_table WHERE status = 'Approved'";
  $bookedRoomsResult = $conn->query($bookedRoomsQuery);
  $bookedRooms = $bookedRoomsResult->fetch_assoc()['booked_rooms'];

  // Available Rooms
  $availableRoomsQuery = "SELECT COUNT(*) as available_rooms FROM rooms WHERE status = 'available'";
  $availableRoomsResult = $conn->query($availableRoomsQuery);
  $availableRooms = $availableRoomsResult->fetch_assoc()['available_rooms'];

  // Checked In
  $checkedInQuery = "SELECT COUNT(*) as checked_in FROM booking_table WHERE status = 'checked_in'";
  $checkedInResult = $conn->query($checkedInQuery);
  $checkedIn = $checkedInResult->fetch_assoc()['checked_in'];
  ?>

  <ul class="box-info">
    <li>
      <i class="fa fa-bed"></i>
      <span class="text">
        <h3><?php echo $totalRooms; ?></h3>
        <p>Total Rooms</p>
      </span>
    </li>
    <li>
      <i class="fa fa-users"></i>
      <span class="text">
        <h3><?php echo $totalClients; ?></h3>
        <p>Clients</p>
      </span>
    </li>
    <li>
      <i class="fa fa-book"></i>
      <span class="text">
        <h3><?php echo $bookedRooms; ?></h3>
        <p>Booked Rooms</p>
      </span>
    </li>
    <li>
      <i class="fa fa-check-circle"></i>
      <span class="text">
        <h3><?php echo $availableRooms; ?></h3>
        <p>Available Rooms</p>
      </span>
    </li>
    <li>
      <i class="fa fa-calendar-check"></i>
      <span class="text">
        <h3><?php echo $checkedIn; ?></h3>
        <p>Checked In</p>
      </span>
    </li>
  </ul>
  <?php
  // Sanitize input to prevent SQL injection
  $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

  // Get total rows
  $total_rows_sql = "SELECT COUNT(*) AS total FROM booking_table
                   INNER JOIN userinfo ON booking_table.userid = userinfo.userid
                   INNER JOIN rooms ON booking_table.room_id = rooms.room_id
                   INNER JOIN room_type ON rooms.category_id = room_type.category_id";
  if (!empty($search)) {
    $total_rows_sql .= " WHERE userinfo.fname LIKE '%$search%' OR userinfo.lname LIKE '%$search%'
                       OR room_type.category_name LIKE '%$search%' OR rooms.room_id LIKE '%$search%'";
  }

  $total_rows_result = mysqli_query($conn, $total_rows_sql);
  $total_rows = mysqli_fetch_assoc($total_rows_result)['total'];

  $limit = isset($_GET['limit']) ? $_GET['limit'] : 5;
  $sorting = isset($_GET['sort']) ? $_GET['sort'] : 'ASC';
  $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

  $pages = ceil($total_rows / $limit);
  $offset = ($current_page - 1) * $limit;

  $sort_column = 'booking_table.book_id';
  $sort_order = ($sorting == 'desc') ? 'DESC' : 'ASC';

  $sql = "SELECT booking_table.*, userinfo.fname, userinfo.lname, rooms.room_id, room_type.category_name FROM booking_table
        INNER JOIN userinfo ON booking_table.userid = userinfo.userid
        INNER JOIN rooms ON booking_table.room_id = rooms.room_id
        INNER JOIN room_type ON rooms.category_id = room_type.category_id";

  if (!empty($search)) {
    $sql .= " WHERE userinfo.fname LIKE '%$search%' OR userinfo.lname LIKE '%$search%'
            OR room_type.category_name LIKE '%$search%' OR rooms.room_id LIKE '%$search%'";
  }

  $sql .= " ORDER BY $sort_column $sort_order
          LIMIT $limit OFFSET $offset";

  $result = mysqli_query($conn, $sql);
  ?>

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <table class="table lms_table_active">
          <thead>
            <tr>
              <th scope="col">No.</th>
              <th scope="col">Customer Name</th>
              <th scope="col">Room #</th>
              <th scope="col">Room Type</th>
              <th scope="col">Check-In</th>
              <th scope="col">Check-Out</th>
              <th scope="col">Status</th>
              <th scope="col">Created At</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr>
                  <th scope="row"><?= $row['book_id'] ?></th>
                  <td><?= $row['fname'] . ' ' . $row['lname'] ?></td>
                  <td><?= $row['room_id'] ?></td>
                  <td><?= $row['category_name'] ?></td>
                  <td><?= date('F j, Y', strtotime($row['check_in'])) ?></td>
                  <td><?= date('F j, Y', strtotime($row['check_out'])) ?></td>
                  <td class="<?php echo ($row['status'] == 'checked_in') ? 'text-success fw-bold' : 'text-warning fw-bold'; ?>">
                    <?= ucfirst($row['status']); ?>
                  </td>
                  <td><?= date('F j, Y', strtotime($row['datetime'])) ?></td>
                </tr>
            <?php
              }
            } else {
              echo '<tr><td colspan="8" class="text-center">No data available</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include('layouts/footer.php') ?>