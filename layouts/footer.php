<!-- Footer Section Begin -->
<footer class="footer-section">
    <div class="container">
        <div class="footer-text">
            <div class="row">
                <div class="col-lg-4">
                    <div class="ft-about">
                        <div class="logo">
                            <a href="#">
                                <img src="img/footer-logo.png" alt="">
                            </a>
                        </div>
                        <p>Maximus Hotel stands as a premier destination for travelers<br /> making travel seamless and enjoyable</p>
                        <div class="fa-social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-tripadvisor"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                            <a href="#"><i class="fa fa-youtube-play"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 offset-lg-1">
                    <div class="ft-contact">
                        <h6>Contact Us</h6>
                        <ul>
                            <li>+(63)93892123412</li>
                            <li>maximus-hotel@gmail.com</li>
                            <li>H.T Feliciano St. Aguada Ozamiz City</li>
                        </ul>
                    </div>
                </div>
                <!--<div class="col-lg-3 offset-lg-1">-->
                <!--    <div class="ft-newslatter">-->
                <!--        <h6>New latest</h6>-->
                <!--        <p>Get the latest updates and offers.</p>-->
                <!--        <form action="#" class="fn-form">-->
                <!--            <input type="text" placeholder="Email">-->
                <!--            <button type="button"><i class="fa fa-send"></i></button>-->
                <!--        </form>-->
                <!--    </div>-->
                <!--</div>-->
                <div class="col-sm-4">
                    <div style="background-image: url('img/floral-bg.jpg'); border-radius: 5px; background-size: cover; background-position: center; height: 100%; width: 100% !important;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                </div>
                <div class="col-lg-5">
                    <div class="co-text">
                        <p>ITP4 | David Eya - Final Exam</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->

<!-- Search model Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch"><i class="icon_close"></i></div>
        <form class="search-model-form" action="rooms.php">
            <input type="text" id="search-input" placeholder="Search here....." name="search">
        </form>
    </div>
</div>
<!-- Search model end -->

<!-- Js Plugins -->
<?php
if (isset($_SESSION['alert'])) {
    echo $_SESSION['alert'];
    unset($_SESSION['alert']);
}
?>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/jquery.nice-select.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/jquery.slicknav.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
<script>
    $(function() {
        // Initialize the check-in date picker
        $("#date-in").datepicker({
            minDate: 0,
            dateFormat: 'dd MM, yy',
            onSelect: function(selectedDate) {
                // Parse the selected check-in date
                const checkInDate = $(this).datepicker('getDate');
                
                if (checkInDate) {
                    // Calculate the minimum check-out date (one day after check-in)
                    const minCheckOutDate = new Date(checkInDate);
                    minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);

                    // Update the date picker to reflect the new minimum date
                    $("#date-out").datepicker("option", "minDate", minCheckOutDate);

                    // Set the check-out date automatically if it's not valid
                    const currentCheckOutDate = $("#date-out").datepicker('getDate');
                    if (!currentCheckOutDate || currentCheckOutDate < minCheckOutDate) {
                        // Automatically set the check-out date to one day after the check-in date
                        $("#date-out").datepicker("setDate", minCheckOutDate);
                    }
                }
            }
        });

        // Initialize the check-out date picker
        $("#date-out").datepicker({
            minDate: 1, // Initially disable today
            dateFormat: 'dd MM, yy'
        });
    });
</script>
</body>

</html>