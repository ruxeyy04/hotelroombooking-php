<?php include('layouts/header.php') ?>
<?php
// Sanitize input to prevent SQL injection
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
 $status = isset($_GET['status']) ? $_GET['status'] : '';
// Get total rows
$total_rows_sql = "SELECT COUNT(*) AS total FROM booking_table
                   INNER JOIN userinfo ON booking_table.userid = userinfo.userid
                   INNER JOIN rooms ON booking_table.room_id = rooms.room_id
                   INNER JOIN room_type ON rooms.category_id = room_type.category_id";
if (!empty($status)) {
    $total_rows_sql .= " WHERE booking_table.status = '$status'";
}                   
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

$sort_column = 'booking_table.status';
$sort_order = ($sorting == 'desc') ? 'DESC' : 'ASC';

$sql = "SELECT booking_table.*, userinfo.fname, userinfo.lname, rooms.room_id, room_type.category_name FROM booking_table
        INNER JOIN userinfo ON booking_table.userid = userinfo.userid
        INNER JOIN rooms ON booking_table.room_id = rooms.room_id
        INNER JOIN room_type ON rooms.category_id = room_type.category_id";

if (!empty($search)) {
    $sql .= " WHERE userinfo.fname LIKE '%$search%' OR userinfo.lname LIKE '%$search%'
            OR room_type.category_name LIKE '%$search%' OR rooms.room_id LIKE '%$search%'";
}
if (!empty($status)) {
    $sql .= " WHERE booking_table.status = '$status'";
} 
$sql .= " ORDER BY FIELD(booking_table.status, 'Pending', 'Approved', 'Reject', 'Cancelled') $sort_order, booking_table.datetime DESC
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
?>

<div class="main p-3">
<div class="search-bar">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-lg-3">
            <form class="d-flex" role="search" style="width: 100%; padding: 40px 30px;" method="get">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                <button class="btn btn-outline-success" type="submit" style="font-size: 18px; font-weight: bold;">Search</button>
            </form>
        </div>
        
            <div class="col-lg-3">
                <form action="" method="get">
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option <?php if (isset($_GET['status']) && $_GET['status'] == 'Pending') echo 'selected'; ?> value="Pending">Pending</option>
                    <option <?php if (isset($_GET['status']) && $_GET['status'] == 'Approved') echo 'selected'; ?> value="Approved">Approved</option>
                    <option <?php if (isset($_GET['status']) && $_GET['status'] == 'Reject') echo 'selected'; ?> value="Reject">Reject</option>
                    <option <?php if (isset($_GET['status']) && $_GET['status'] == 'Cancelled') echo 'selected'; ?> value="Cancelled">Cancelled</option>
                </select>
                </form>
            </div>
        

    </div>
</div>
    <div class="client-table">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Room No.</th>
                    <th scope="col">Room Type</th>
                    <th scope="col">Check In</th>
                    <th scope="col">Check Out</th>
                    <th scope="col">No. of Days</th>
                    <th scope="col">Status</th>
                    <th scope="col">Date Created</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $modal_id = 'updateStatus_' . $row['book_id'];
                        $status = $row['status'];
                ?>
                        <tr>
                            <th scope="row"><?= $row['book_id'] ?></th>
                            <td><?= $row['fname'] . ' ' . $row['lname'] ?></td>
                            <td><?= $row['room_id'] ?></td>
                            <td><?= $row['category_name'] ?></td>
                            <td><?= date('F j, Y', strtotime($row['check_in'])) ?></td>
                            <td><?= date('F j, Y', strtotime($row['check_out'])) ?></td>
                            <td><?= $row['no_of_days'] ?></td>
                            <td class="<?php echo ($row['status'] == 'checked_in') ? 'text-success fw-bold' : 'text-warning fw-bold'; ?>">
                                <?= ucfirst($row['status']); ?>
                            </td>
                            <td><?= date('F j, Y', strtotime($row['datetime'])) ?></td>
                            <td>
                                <button class="btn btn-success" data-bs-target="#<?= $modal_id ?>" data-bs-toggle="modal" <?=$row['status'] == 'Approved' || $row['status'] == 'Reject' || $row['status'] == 'Cancelled'? 'disabled' : ''?>>Update Book Status</button>
                            </td>
                        </tr>
                        <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Update Booking #<?= $row['book_id'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Book Status</label>
                                                <select class="form-select" name="status" id="status">
                                                    <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Approved" <?= $status == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                                    <option value="Reject" <?= $status == 'Reject' ? 'selected' : '' ?>>Reject</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                                            <button type="submit" class="btn btn-primary" name="update_status">Save changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="10" class="text-center">No data available</td></tr>';
                }
                ?>
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
                    $order_id = $_POST['book_id'];
                    $status = $_POST['status'];

                    $update_sql = "UPDATE booking_table SET status = ? WHERE book_id = ?";
                    if ($stmt = mysqli_prepare($conn, $update_sql)) {
                        mysqli_stmt_bind_param($stmt, 'si', $status, $order_id);
                        if (mysqli_stmt_execute($stmt)) {
                            $_SESSION['alert'] = "<script>
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Book status updated successfully.',
                                });
                            </script>";
                            echo '<meta http-equiv="refresh" content="0;url=booked_list.php">';
                            exit();
                        } else {
                            $_SESSION['alert'] = "<script>
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Error updating book status: " . mysqli_error($conn) . "',
                                });
                            </script>";
                            echo '<meta http-equiv="refresh" content="0;url=booked_list.php">';
                            exit();
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $_SESSION['alert'] = "<script>
                            Toast.fire({
                                icon: 'success',
                                title: 'Error preparing statement: " . mysqli_error($conn) . "',
                            });
                        </script>";
                        echo '<meta http-equiv="refresh" content="0;url=booked_list.php">';
                        exit();
                    }
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
<?php include('layouts/footer.php') ?>