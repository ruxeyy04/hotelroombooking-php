<?php include('layouts/header.php'); ?>
<!-- Breadcrumb Section Begin -->
<div class="breadcrumb-section pb-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-text">
                    <h2>My Reservation</h2>
                    <div class="bt-option">
                        <a href="/">Home</a>
                        <span>My Reservation</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb Section End -->
<section class="contact-section">
    <div class="container">

        <div class="row">
            <div class="col-md-12">
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
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Room No.</th>
                            <th scope="col">Room Type</th>
                            <th scope="col">Check-In</th>
                            <th scope="col">Check-Out</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $status = isset($_GET['status']) ? $_GET['status'] : '';
                        $sorting = isset($_GET['sort']) ? $_GET['sort'] : 'ASC';
                        $sort_order = ($sorting == 'desc') ? 'DESC' : 'ASC';
                        $sql = "SELECT 
                                a.*, a.status AS book_status,
                                b.*, 
                                c.* 
                                FROM 
                                booking_table a 
                                INNER JOIN 
                                rooms b 
                                ON 
                                a.room_id = b.room_id 
                                INNER JOIN 
                                room_type c 
                                ON 
                                b.category_id = c.category_id 
                                WHERE 
                                a.userid = '$userid'";
                        if (!empty($status)) {
                            $sql .= " AND a.status = '$status'";
                        }
                        $sql .= " ORDER BY FIELD(a.status, 'Pending', 'Approved', 'Reject', 'Cancelled') $sort_order, a.datetime DESC";
                        $res = $conn->query($sql);


                        if ($res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['book_id'] ?></td>
                                    <td>Room #<?= $row['room_id'] ?></td>
                                    <td><?= $row['category_name'] ?></td>
                                    <td><?= $row['check_in'] ?></td>
                                    <td><?= $row['check_out'] ?></td>
                                    <td><?= $row['book_status'] ?></td>
                                    <td>
                                        <?php if ($row['book_status'] == 'Pending') : ?>
                                            <form method="post" action="">
                                                <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                                                <button type="submit" name="cancel" class="btn btn-danger">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                        <?php    }
                        }

                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
                            $book_id = $_POST['book_id'];

                            // Check if the booking status is still pending
                            $check_sql = "SELECT `status` FROM booking_table WHERE book_id = ?";
                            $stmt = $conn->prepare($check_sql);
                            $stmt->bind_param("i", $book_id);
                            $stmt->execute();
                            $stmt->bind_result($book_status);
                            $stmt->fetch();
                            $stmt->close();

                            if ($book_status == 'Pending') {
                                // Update the booking status to cancelled
                                $update_sql = "UPDATE booking_table SET `status` = 'Cancelled' WHERE book_id = ?";
                                $stmt = $conn->prepare($update_sql);
                                $stmt->bind_param("i", $book_id);
                                $stmt->execute();
                                $stmt->close();
                                $_SESSION['alert'] = "<script>
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Booking cancelled successfully.',
                                });
                            </script>";
                                echo '<meta http-equiv="refresh" content="0;url=myreservation.php">';
                                exit();
                            } else {
                                $_SESSION['alert'] = "<script>
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Unable to cancel the booking. It is already approved or processed.',
                                });
                            </script>";
                                echo '<meta http-equiv="refresh" content="0;url=myreservation.php">';
                                exit();
                            }
                        }
                        ?>

                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include('layouts/footer.php'); ?>