<?php include('layouts/header.php'); ?>
<!-- Breadcrumb Section Begin -->
<div class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-text">
                    <h2>Our Rooms</h2>
                    <div class="bt-option">
                        <a href="index.php">Home</a>
                        <span>Rooms</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb Section End -->
<?php
if (!isset($_GET['room_type']) && !isset($_GET['room_id'])) {
    echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
    exit;
}

$room_type = isset($_GET['room_type']) ? $_GET['room_type'] : null;
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : null;

if ($room_type !== null) {
    $sql = "SELECT a.*, b.* FROM rooms a INNER JOIN room_type b ON a.category_id=b.category_id WHERE b.category_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $room_type);
} elseif ($room_id !== null) {
    $sql = "SELECT a.*, b.* FROM rooms a INNER JOIN room_type b ON a.category_id=b.category_id WHERE a.room_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
} else {
    // Handle invalid input
    echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
    exit;
}

$stmt->execute();
$room_res = $stmt->get_result();

if ($room_res->num_rows == 0) {
    // Handle no matching records
    echo '<meta http-equiv="refresh" content="0;url=rooms.php">';
    exit;
}
if ($room_type !== null) {
    $type = $room_res->fetch_assoc();
} elseif ($room_id !== null) {
    $room = $room_res->fetch_assoc();
}

?>
<?php
if (!isset($_GET['room_id'])) { ?>
    <!-- Room Type Details Section Begin -->
    <section class="room-details-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="room-details-item">
                        <img src="img/room/standard.jpg" alt="">
                        <div class="rd-text">
                            <div class="rd-title">
                                <h3><?= $type['category_name'] ?></h3>
                            </div>
                            <h2>₱<?= $type['price'] ?><span>/Pernight</span></h2>
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="r-o">Size:</td>
                                        <td>30 ft</td>
                                    </tr>
                                    <tr>
                                        <td class="r-o">Capacity:</td>
                                        <td><?= $type['capacity'] ?> Person</td>
                                    </tr>
                                    <tr>
                                        <td class="r-o">Bed:</td>
                                        <td><?= $type['bed'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="r-o">Services:</td>
                                        <td><?= $type['services'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="f-para"><?= $type['description'] ?>
                            </p>
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="room-booking">
                        <h3>Your Reservation</h3>
                        <form action="rooms.php" method="get">
                            <div class="check-date">
                                <label for="date-in">Check In:</label>
                                <input type="text" class="date-input" id=."date-in" name="checkin" required>
                                <i class="icon_calendar"></i>
                            </div>
                            <div class="check-date">
                                <label for="date-out">Check Out:</label>
                                <input type="text" class="date-input" id="date-out" name="checkout" required>
                                <i class="icon_calendar"></i>
                            </div>
                            <div class="select-option">
                                <label for="room">Guest:</label>
                                <select id="room" name="guest">
                                    <option value="<?= $type['capacity'] ?>"><?= $type['capacity'] ?> Guest</option>
                                </select>
                            </div>
                            <div class="select-option">
                                <label for="room">Room Type:</label>
                                <select id="room" name="room_type">
                                    <option value="<?= $type['category_id'] ?>"><?= $type['category_name'] ?></option>
                                </select>
                            </div>
                            <button type="submit">Check Availability</button>
                        </form>



                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Room Details Section End -->

<?php } else { ?>
    <!-- Room ID Details Section Begin -->
    <section class="room-details-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="room-details-item">
                        <img src="img/room/standard.jpg" alt="">
                        <div class="rd-text">
                            <div class="rd-title">
                                <h3>Room <?= $room['room_id'] ?> (<?= $room['category_name'] ?>)</h3>

                            </div>

                            <h2>₱<?= $room['price'] ?><span>/Pernight</span></h2>
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="r-o">Size:</td>
                                        <td>30 ft</td>
                                    </tr>
                                    <tr>
                                        <td class="r-o">Capacity:</td>
                                        <td><?= $room['capacity'] ?> Person</td>
                                    </tr>
                                    <tr>
                                        <td class="r-o">Bed:</td>
                                        <td><?= $room['bed'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="r-o">Services:</td>
                                        <td><?= $room['services'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="f-para"><?= $room['description'] ?>
                            </p>
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="room-booking">
                        <h3>Your Reservation</h3>
                        <form action="room-details.php?room_id=<?= $room_id ?>" method="post">
                            <div class="check-date">
                                <label for="date-in">Check In:</label>
                                <input type="text" class="date-input" id="date-in" name="date_in" required value="<?= isset($_GET['checkin']) ? $_GET['checkin'] : '' ?>">
                                <i class="icon_calendar"></i>
                            </div>
                            <div class="check-date">
                                <label for="date-out">Check Out:</label>
                                <input type="text" class="date-input" id="date-out" name="date_out" required value="<?= isset($_GET['checkin']) ? $_GET['checkout'] : '' ?>">
                                <i class="icon_calendar"></i>
                            </div>
                            <div class="select-option">
                                <label for="room">Guest:</label>
                                <select id="room">
                                    <option value="<?= $room['capacity'] ?>"><?= $room['capacity'] ?> Guest</option>
                                </select>
                            </div>
                            <div class="select-option">
                                <label for="room">Room Type:</label>
                                <select id="room">
                                    <option value="<?= $room['category_id'] ?>"><?= $room['category_name'] ?></option>
                                </select>
                            </div>
                            <?php
                            if (isset($_GET['available']) == true) { ?>
                                <button type="submit" name="book_now">Book Now</button>
                            <?php  } else { ?>
                                <button type="submit" name="check_availability">Check Availability</button>
                            <?php   }
                            ?>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Room ID Section End -->

<?php
} ?>


<?php
if (!isset($_GET['room_id'])) { ?>
    <?php
    $items_per_page = 6;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;


    if (!empty($room_type)) {
        $total_items_query = "SELECT COUNT(*) as total FROM rooms r INNER JOIN room_type rt ON r.category_id = rt.category_id WHERE rt.category_name= ?";
        $stmt = $conn->prepare($total_items_query);
        $stmt->bind_param("s", $room_type);
    }
    $stmt->execute();
    $total_items_result = $stmt->get_result();
    $total_items_row = $total_items_result->fetch_assoc();
    $total_items = $total_items_row['total'];
    $stmt->close();

    // Calculate total pages
    $total_pages = ceil($total_items / $items_per_page);

    if (!empty($room_type)) {
        $query = "SELECT * FROM rooms r INNER JOIN room_type rt ON r.category_id = rt.category_id WHERE rt.category_name= ? LIMIT ?, ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $room_type, $offset, $items_per_page);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <!-- Rooms Section Begin -->
    <section class="rooms-section spad">
        <div class="container">
            <div class="row">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="room-item">
                                <img src="img/room/<?= $row['image'] ?>" alt="" height="250">
                                <div class="ri-text">
                                    <h4>Room <?= $row['room_id'] ?> (<?= $row['category_name'] ?>)</h4>
                                    <h3>₱<?= $row['price'] ?><span>/Pernight</span></h3>

                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="r-o">Size:</td>
                                                <td>30 ft</td>
                                            </tr>
                                            <tr>
                                                <td class="r-o">Capacity:</td>
                                                <td><?= $row['capacity'] ?> Person</td>
                                            </tr>
                                            <tr>
                                                <td class="r-o">Bed:</td>
                                                <td><?= $row['bed'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="r-o">Services:</td>
                                                <td><?= strlen($row['services']) > 30 ? substr($row['services'], 0, 30) . '...' : $row['services'] ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <a href="room-details.php?room_id=<?= $row['room_id'] ?>" class="primary-btn">More Details</a>
                                </div>
                            </div>
                        </div>
                <?php    }
                }
                ?>


                <div class="col-lg-12">
                    <div class="room-pagination">
                        <?php if ($page > 1) { ?>
                            <a href="?page=<?= $page - 1 ?>"><i class="fa fa-long-arrow-left"></i> Prev</a>
                        <?php } ?>

                        <?php if ($total_pages <= 5) { ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                <a class="<?= $page == $i ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ($page <= 3) { ?>
                                <?php for ($i = 1; $i <= 3; $i++) { ?>
                                    <a class="<?= $page == $i ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                                <?php } ?>
                                <li><span>...</span></li>
                                <li><a href="?page=<?= $total_pages ?>"><?= $total_pages ?></a></li>
                            <?php } elseif ($page >= $total_pages - 2) { ?>
                                <a href="?page=1">1</a>
                                <a href="#!">...</a>
                                <?php for ($i = $total_pages - 2; $i <= $total_pages; $i++) { ?>
                                    <a class="<?= $page == $i ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                                <?php } ?>
                            <?php } else { ?>
                                <li><a href="?page=1">1</a></li>
                                <a href="#!">...</a>
                                <?php for ($i = $page - 1; $i <= $page + 1; $i++) { ?>
                                    <a class="<?= $page == $i ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                                <?php } ?>
                                <a href="#!">...</a>
                                <a href="?page=<?= $total_pages ?>"><?= $total_pages ?></a>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($page < $total_pages) { ?>
                            <a href="?page=<?= $page + 1 ?>">Next <i class="fa fa-long-arrow-right"></i></a>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Rooms Section End -->
<?php }
?>
<?php
// Assuming you have already established a database connection stored in $conn

if (isset($_POST['check_availability'])) {
    $room_id = $_GET['room_id']; // Get the room_id from the URL

    // Sanitize and validate input
    $check_in = $_POST['date_in'];
    $check_out = $_POST['date_out'];

    // Validate if checkout is before checkin
    if (strtotime($check_out) <= strtotime($check_in)) {
        // Invalid date range
        $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid Date Range',
            text: 'Check-out date must be after Check-in date.',
        })
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=room-details.php?room_id=' . $room_id . '">';
        exit();
    }

    $checkin = date("Y-m-d", strtotime($check_in));
    $checkout = date("Y-m-d", strtotime($check_out));

    // Check for conflicts
    $query = "SELECT * FROM booking_table WHERE room_id = '$room_id' AND status = 'Pending'
                            AND (
                                (check_in BETWEEN '$checkin' AND '$checkout')
                                OR (check_out BETWEEN '$checkin' AND '$checkout')
                                OR ('$checkin' BETWEEN check_in AND check_out)
                                OR (check_in <= '$checkin' AND check_out >= '$checkout')
                            )";

    // Execute the query
    $result = $conn->query($query);

    // Check if there are any conflicting bookings
    if ($result->num_rows > 0) {
        $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'info',
            title: 'Not Available',
            text: 'Sorry, the Room #$room_id is not available for the selected dates.',
        })
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=room-details.php?room_id=' . $room_id . '">';
        exit();
    } else {
        $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'success',
            title: 'Available',
            text: 'Room #$room_id is available for booking!',
        })
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=room-details.php?room_id=' . $room_id . '&available=true&checkin=' . $check_in . '&checkout=' . $check_out . '">';
        exit();
    }

    // Free the result set
    $result->free_result();
}

if (isset($_POST['book_now'])) {
    // Check if user is logged in
    if (!isset($_SESSION['userid'])) {
        echo '<meta http-equiv="refresh" content="0;url=login.php">';
        exit();
    }
    if (empty($userinfo['contact']) || empty($userinfo['gender'])) {
        $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'info',
            title: 'Fill-in',
            text: 'Complete your profile details first. Contact number and gender',
        })
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit();        
    }

    // Get room_id from the URL
    $room_id = $_GET['room_id'];

    // Sanitize and validate input
    $check_in = $_POST['date_in'];
    $check_out = $_POST['date_out'];

    // Validate if checkout is before checkin
    if (strtotime($check_out) <= strtotime($check_in)) {
        // Invalid date range
        $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid Date Range',
            text: 'Check-out date must be after Check-in date.',
        })
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=room-details.php?room_id=' . $room_id . '">';
        exit();
    }

    // Calculate number of days
    $checkin = new DateTime($check_in);
    $checkout = new DateTime($check_out);
    $interval = $checkin->diff($checkout);
    $no_of_days = $interval->days;

    // Get userid from session
    $userid = $_SESSION['userid'];
    $checkin = date("Y-m-d", strtotime($check_in));
    $checkout = date("Y-m-d", strtotime($check_out));

    // Insert booking record into booking_table
    $query = "INSERT INTO booking_table (userid, room_id, check_in, check_out, no_of_days) 
              VALUES ('$userid', '$room_id', '$checkin', '$checkout', '$no_of_days')";

    // Execute the query
    if ($conn->query($query) === TRUE) {
        // Booking successful
        $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'success',
            title: 'Booking Successful',
            text: 'Your booking has been confirmed!',
        })
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=room-details.php?room_id=' . $room_id . '">';
        exit();
    } else {
        // Error occurred while booking
        $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'error',
            title: 'Booking Error',
            text: 'An error occurred while processing your booking. Please try again later.',
        })
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=room-details.php?room_id=' . $room_id . '">';
        exit();
    }
}
?>


<?php include('layouts/footer.php'); ?>