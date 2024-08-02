    <!-- Header Section Begin -->
    <header class="header-section">
        <div class="menu-item">
            <div class="container">
                <div class="row">
                    <style>
                        /* Media query for screen sizes below 990px */
                        @media (max-width: 990px) {
                            .logo img {
                                width: 35%;
                                /* Adjust the width as needed */
                            }
                        }
                    </style>
                    <div class="col-lg-2">
                        <div class="logo">
                            <a href="/">
                                <img src="img/logo.png" alt="">
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-10">
                        <div class="nav-menu">
                            <nav class="mainmenu">
                                <ul>
                                    <li class="<?php is_active("index.php", $current_page); ?>"><a href="/">Home</a></li>
                                    <li class="<?php is_active("rooms.php", $current_page); ?>"><a href="rooms.php">Rooms</a></li>
                                    <li class="<?php is_active("about-us.php", $current_page); ?>"><a href="about-us.php">About Us</a></li>
                                    <li class="<?php is_active("contact.php", $current_page); ?>"><a href="contact.php">Contact</a></li>
                                    <?php
                                    if (isset($_SESSION['userid'])) { ?>
                                        <li class=""><a href="#!">Profile</a>
                                            <ul class="dropdown">
                                                <li><a href="profile.php">My Profile</a></li>
                                                <li><a href="myreservation.php">My Reservation</a></li>
                                                <li><a href="?logout">Logout</a></li>
                                            </ul>
                                        </li>
                                    <?php  } else { ?>
                                        <li class="<?php is_active("login.php", $current_page); ?>"><a href="login.php">Login</a></li>
                                  <?php  }
                                    ?>
                                </ul>
                            </nav>
                            <div class="nav-right search-switch">
                                <i class="icon_search"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header End -->