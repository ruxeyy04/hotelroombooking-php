<?php
require('../config.php');
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

$menu_items = array(
    "index.php" => "Dashboard",
    "booked_list.php" => "Booking",
    "users.php" => "Users",
    "rooms.php" => "Rooms",
    "room_category.php" => "Room Category",
    "profile.php" => "Profile",

);

$page_title = "Dashboard";

foreach ($menu_items as $href => $label) {
    if ($href === $current_page) {
        $page_title = $label;
        break;
    }
}
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

if (isset($_SESSION['userid'])) {
    $userinfo_sql = "SELECT * FROM userinfo WHERE userid = ?";
    $userinfo_stmt = $conn->prepare($userinfo_sql);
    $userinfo_stmt->bind_param("i", $userid);
    $userinfo_stmt->execute();
    $userinfo_res = $userinfo_stmt->get_result();
    $userinfo = $userinfo_res->fetch_assoc();
}
if (isset($_SESSION['usertype'])) {
    if ($_SESSION['usertype'] == 'Admin') {
        echo '<meta http-equiv="refresh" content="0;url=/admin/index.php">';
        exit();
    } else if ($_SESSION['usertype'] == 'Client') {
        echo '<meta http-equiv="refresh" content="0;url=/index.php">';
        exit();
    }
}
if (!isset($_SESSION['userid'])) {
    echo '<meta http-equiv="refresh" content="0;url=/login.php">';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style2.css">
    <title>Maximus | Admin | <?php echo $page_title; ?></title>
    <style>
        .title {
            margin-top: 10px;
        }

        ul {
            padding-right: 2rem;
        }

        .custom-btn {
            background-color: #275773;
        }

        .custom-btn:hover {
            background-color: #1a3b4d;
        }

        span.card-icon {
            position: absolute;
            font-size: 3em;
            bottom: .3em;
            color: #ffffff80;
        }

        .box-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 35px;
        }

        .box-info li {
            padding: 24px;
            background: #FFFF;
            border-radius: 20px;
            display: flex;
            align-items: center;
            grid-gap: 24px;
        }

        .box-info li .fa {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            font-size: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .box-info li:nth-child(1) .fa {
            background: #CFE8FF;
            color: #3C91E6;
        }

        .box-info li:nth-child(2) .fa {
            background: #8FFFF2;
            color: #018F7E;
        }

        .box-info li:nth-child(3) .fa {
            background: #FFE0D3;
            color: #FD7238;
        }

        .box-info li:nth-child(4) .fa {
            background: #7bffa7;
            color: #00BF41;
        }

        .box-info li:nth-child(5) .fa {
            background: #E3CDFF;
            color: #9747FF;
        }

        .box-info li:nth-child(6) .fa {
            background: #FFF2C6;
            color: #FFCE26;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    </script>
</head>

<body>

    <div class="container-fluid mt-10" style="--bs-gutter-x: 0.30rem;">
        <nav class="navbar navbar-light fixed-top navbar-expand" style="margin: 5px 0px 0px;">
            <img src="images/hotel logo.png" width="38" height="38" class="logo align-top" alt="" style=" margin-left: 20px;">
            <a class="navbar-brand" href="#" style="color: white; font-size: 28px; font-weight: bold;">
                AXIMUS
            </a>
        </nav>
        <div class="wrapper" style="margin-top: 75px;">
            <aside id="sidebar">
                <div class="d-flex">
                    <button class="toggle-btn" type="button" style="padding-left: 1.8rem;">
                        <i class="fa fa-bars" style="font-size: 25px;"></i>
                    </button>
                </div>
                <?php include('layouts/sidebar.php') ?>
                <div class="sidebar-footer">
                    <a href="?logout" class="sidebar-link" style="padding-left: 1.7rem;">
                        <i class="lni lni-exit" style="font-size: 20px; margin-right: 15px;"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </aside>