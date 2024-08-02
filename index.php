<?php
include('layouts/header.php');

?>
<!-- Hero Section Begin -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="hero-text">
                    <h1>Maximus A Luxury Hotel</h1>
                    <p>Here are the best hotel booking sites, including recommendations for international
                        travel and for finding low-priced hotel rooms.</p>
                    <a href="rooms.php" class="primary-btn">Discover Now</a>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 offset-xl-2 offset-lg-1">
                <div class="booking-form">
                    <h3>Booking Your Hotel</h3>
                    <form action="rooms.php" method="get">
                        <div class="check-date">
                            <label for="date-in">Check In:</label>
                            <input type="text" class="date-input" id="date-in" name="checkin">
                            <i class="icon_calendar"></i>
                        </div>
                        <div class="check-date">
                            <label for="date-out">Check Out:</label>
                            <input type="text" class="date-input" id="date-out" name="checkout">
                            <i class="icon_calendar"></i>
                        </div>
                        <div class="select-option">
                            <label for="room">Guest:</label>
                            <select id="room" name="guest">
                                <option value="2-3">2-3 Guest</option>
                                <option value="3-5">3-5 Guest</option>
                                <option value="4-6">4-6 Guest</option>
                            </select>
                        </div>
                        <div class="select-option">
                            <label for="room">Room Type:</label>
                            <select id="room" name="room_type">
                                <option value="1">Standard Room</option>
                                <option value="2">Deluxe Room</option>
                                <option value="3">Luxury Room</option>
                                <option value="4">Family Room</option>
                            </select>
                        </div>
                        <button type="submit">Check Availability</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-slider owl-carousel">
        <div class="hs-item set-bg" data-setbg="img/hero/hero-3.jpg"></div>
        <div class="hs-item set-bg" data-setbg="img/hero/hero-1.jpg"></div>
        <div class="hs-item set-bg" data-setbg="img/hero/hero-2.jpg"></div>
    </div>
</section>
<!-- Hero Section End -->

<!-- About Us Section Begin -->
<section class="aboutus-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="about-text">
                    <div class="section-title">
                        <span>About Us</span>
                        <h2>Maximus <br />Hotel</h2>
                    </div>
                    <p class="f-para">Maximus Hotel stands as a premier destination for travelers seeking exceptional accommodation experiences. We are dedicated to making travel seamless and enjoyable for millions of guests every day.</p>
                    <p class="s-para">Whether you are looking for a luxurious hotel, a cozy vacation rental, an all-inclusive resort, a spacious apartment, a welcoming guest house, or even a unique tree house, Maximus Hotel provides a diverse range of options to suit every traveler's needs.</p>
                    <a href="about-us.php" class="primary-btn about-btn">Read More</a>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="about-pic">
                    <div class="row">
                        <div class="col-sm-6">
                            <div style="background-image: url('img/about/about-1.jpg'); background-size: cover; background-position: center; height: 100%; width: 100% !important;"></div>

                        </div>
                        <div class="col-sm-6">
                            <div style="background-image: url('img/about/about-2.jpg'); background-size: cover; background-position: center; height: 420px; width: 100% !important;"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Us Section End -->

<!-- Home Room Section Begin -->
<section class="hp-room-section">
    <div class="container-fluid">
        <div class="hp-room-items">

            <div class="row">
                <?php

                $sql = "SELECT * FROM room_type";
                $res = $conn->query($sql);

                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) { ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="hp-room-item set-bg" data-setbg="img/room/<?= $row['image'] ?>">
                                <div class="hr-text">
                                    <h3><?= $row['category_name'] ?></h3>
                                    <h2>₱<?= number_format($row['price'], 2) ?><span>/Pernight</span></h2>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="r-o">Size:</td>
                                                <td>30 ft</td>
                                            </tr>
                                            <tr>
                                                <td class="r-o">Capacity:</td>
                                                <td><?= $row['capacity'] ?> Persons</td>
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
                                    <a href="room-details.php?room_type=<?=$row['category_name']?>" class="primary-btn">More Details</a>
                                </div>
                            </div>
                        </div>
                <?php    }
                }
                ?>

            </div>
        </div>
    </div>
</section>
<!-- Home Room Section End -->
<!-- Contact Section Begin -->
<section class="contact-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="contact-text">
                    <h2>Contact Info</h2>
                    <p>Contact us to hear from your suggesttions and conerns</p>
                    <table>
                        <tbody>
                            <tr>
                                <td class="c-o">Address:</td>
                                <td>H.T Feliciano St. Aguada Ozamiz City</td>
                            </tr>
                            <tr>
                                <td class="c-o">Tel:</td>
                                <td>(088) 521-1234</td>
                            </tr>
                            <tr>
                                <td class="c-o">Email:</td>
                                <td>maximus-hotel@gmail.com</td>
                            </tr>
                            <tr>
                                <td class="c-o">Phone:</td>
                                <td>+(63)93892123412</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--<div class="col-lg-7 offset-lg-1">-->
            <!--    <form action="#" class="contact-form">-->
            <!--        <div class="row">-->
            <!--            <div class="col-lg-6">-->
            <!--                <input type="text" placeholder="Your Name">-->
            <!--            </div>-->
            <!--            <div class="col-lg-6">-->
            <!--                <input type="text" placeholder="Your Email">-->
            <!--            </div>-->
            <!--            <div class="col-lg-12">-->
            <!--                <textarea placeholder="Your Message"></textarea>-->
            <!--                <button type="button">Submit Now</button>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </form>-->
            <!--</div>-->
            <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d15798.176235812838!2d123.82996325!3d8.147809449999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sph!4v1717592141880!5m2!1sen!2sph" width="900" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
        <!--<div class="map">-->
        <!--    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d15798.176235812838!2d123.82996325!3d8.147809449999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sph!4v1717592141880!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>-->
        <!--</div>-->
    </div>
</section>
<!-- Contact Section End -->
<?php include('layouts/footer.php'); ?>