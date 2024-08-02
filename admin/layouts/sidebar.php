<?php
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Define an array with the menu items and their corresponding href attributes
$menu_items = array(
    "index.php" => "Dashboard",
    "booked_list.php" => "Booking",
    "users.php" => "Users",
    "rooms.php" => "Rooms",
    "room_category.php" => "Room Category",
    "profile.php" => "Profile",
);

// Function to add the 'active' class if the current page matches the menu item
function is_active($page, $current_page)
{
    if ($page === $current_page) {
        echo 'active';
    }
}

?>
<ul class="sidebar-nav">
    <?php foreach ($menu_items as $href => $label) : ?>
        <li class="sidebar-item">
            <a href="<?php echo $href; ?>" class="<?php is_active($href, $current_page); ?> sidebar-link" style="padding-left: 1.7rem;">

                <?php if ($label === "Dashboard") : ?>
                    <i class="lni lni-grid-alt" style="font-size: 20px; margin-right: 15px;"></i>
                <?php elseif ($label === "Booking") : ?>
                    <i class="fa fa-book" style="font-size: 20px; margin-right: 15px;"></i>
                <?php elseif ($label === "Users") : ?>
                    <i class="fa fa-user" style="font-size: 20px; margin-right: 18px;"></i>
                <?php elseif ($label === "Rooms") : ?>
                    <i class="fa fa-bed" style="font-size: 18px; margin-right: 15px;"></i>
                <?php elseif ($label === "Room Category") : ?>
                    <i class="fa fa-list" style="font-size: 20px; margin-right: 15px;"></i>
                <?php elseif ($label === "Profile") : ?>
                    <i class="fa fa-address-card" style="font-size: 20px; margin-right: 15px;"></i>
                <?php endif; ?>

                <span><?php echo $label; ?></span>
            </a>

        </li>
    <?php endforeach; ?>

</ul>