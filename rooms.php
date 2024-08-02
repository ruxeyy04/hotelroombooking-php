<?php include('layouts/header.php'); ?>
<!-- Breadcrumb Section Begin -->
<div class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-text">
                    <h2>Our Rooms</h2>
                    <div class="bt-option">
                        <a href="/">Home</a>
                        <span>Rooms</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb Section End -->
<?php
$items_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

if (isset($_GET['checkin']) && isset($_GET['checkout'])) {
    $check_in = $_GET['checkin'];
    $check_out = $_GET['checkout'];
    if (strtotime($check_out) <= strtotime($check_in)) {
        // Invalid date range
        $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid Date Range',
            text: 'Check-out date must be after Check-in date.',
        })
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=index.php">';
        exit();
    }
}
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$checkin = isset($_GET['checkin']) ? date("Y-m-d", strtotime($_GET['checkin'])) : '';
$checkout = isset($_GET['checkout']) ? date("Y-m-d", strtotime($_GET['checkout'])) : '';
$guest = isset($_GET['guest']) ? $_GET['guest'] : '';
$room_type = isset($_GET['room_type']) ? (int)$_GET['room_type'] : '';



$conditions = [];
$params = [];
$types = '';

if (!empty($search_term)) {
    $like_search_term = "%" . $search_term . "%";
    $conditions[] = "(rt.category_name LIKE ? OR r.room_id = ?)";
    $params[] = $like_search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

if (!empty($room_type)) {
    $conditions[] = "r.category_id = ?";
    $params[] = $room_type;
    $types .= 'i';
}

if (!empty($checkin) && !empty($checkout)) {
    $conditions[] = "r.room_id NOT IN (
        SELECT room_id FROM booking_table 
        WHERE (
            (check_in BETWEEN ? AND ?)
            OR (check_out BETWEEN ? AND ?)
            OR (? BETWEEN check_in AND check_out)
            OR (check_in <= ? AND check_out >= ?)
        )
    )";
    $params[] = $checkin;
    $params[] = $checkout;
    $params[] = $checkin;
    $params[] = $checkout;
    $params[] = $checkin;
    $params[] = $checkin;
    $params[] = $checkout;
    $types .= 'sssssss';
}

$where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

$total_items_query = "SELECT COUNT(*) as total FROM rooms r 
                      INNER JOIN room_type rt ON r.category_id = rt.category_id 
                      $where";
$stmt = $conn->prepare($total_items_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_items_result = $stmt->get_result();
$total_items_row = $total_items_result->fetch_assoc();
$total_items = $total_items_row['total'];
$stmt->close();

// Calculate total pages
$total_pages = ceil($total_items / $items_per_page);

// Add pagination parameters
$params[] = $offset;
$params[] = $items_per_page;
$types .= 'ii';

$query = "SELECT r.*, rt.* FROM rooms r 
          INNER JOIN room_type rt ON r.category_id = rt.category_id 
          $where 
          LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
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
<?php include('layouts/footer.php'); ?>