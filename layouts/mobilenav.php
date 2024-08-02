    <!-- Offcanvas Menu Section Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="canvas-open">
        <i class="icon_menu"></i>
    </div>
    <div class="offcanvas-menu-wrapper">
        <div class="canvas-close">
            <i class="icon_close"></i>
        </div>
        <div class="search-icon  search-switch">
            <i class="icon_search"></i>
        </div>

        <nav class="mainmenu mobile-menu">
            <ul>
                <li class="<?php is_active("index.php", $current_page); ?>"><a href="/">Home</a></li>
                <li class="<?php is_active("rooms.php", $current_page); ?>"><a href="rooms.php">Rooms</a></li>
                <li class="<?php is_active("about-us.php", $current_page); ?>"><a href="about-us.php">About Us</a></li>
                <li class="<?php is_active("contact.php", $current_page); ?>"><a href="contact.php">Contact</a></li>
                <?php
                if (isset($_SESSION['userid'])) { ?>
                    <li><a href="#!">Profile</a>
                        <ul class="dropdown">
                            <li><a href="profile.php">My Profile</a></li>
                            <li><a href="myreservation.php">My Reservation</a></li>
                            <li><a href="?logout">Logout</a></li>
                        </ul>
                    </li>
                <?php  } else { ?>
                    <li class="<?php is_active("login.php", $current_page); ?>"><a href="login.php">Login</a></li>
              <?php   }
                ?>

            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>
    </div>
    <!-- Offcanvas Menu Section End -->