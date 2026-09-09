<?php

session_start();

require_once "config/Database.php";
require_once "classes/Booking.php";


/* =========================================
   CHECK CUSTOMER LOGIN
========================================= */

if (!isset($_SESSION["customer_id"])) {

    header("Location: login.php");
    exit();

}


/* =========================================
   DATABASE CONNECTION
========================================= */

$database = new Database();

$db = $database->getConnection();


/* =========================================
   BOOKING OBJECT
========================================= */

$bookingObject = new Booking($db);

$customer_id = $_SESSION["customer_id"];


/* =========================================
   GET CUSTOMER BOOKINGS
========================================= */

$bookings = $bookingObject->getCustomerBookings($customer_id);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Bookings | Bill's Resort</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css?v=7"
    >

</head>


<body>


<!-- =========================================================
     HEADER
     ========================================================= -->

<header class="header">

    <div class="line"></div>


    <!-- LOGO -->

    <div class="logo-box">

        <img
            src="assets/images/mainlogo.jpg"
            alt="Bill's Resort Logo"
        >

    </div>


    <!-- NAVIGATION -->

    <nav class="navbar">

        <a href="index.php">
            HOME
        </a>

        <a href="index.php#about">
            ABOUT US
        </a>

        <a href="index.php#rooms">
            ROOMS & AMENITIES
        </a>

        <a href="index.php#gallery">
            GALLERY
        </a>

        <a href="index.php#contact">
            CONTACT US
        </a>

        <a href="book.php" class="book-btn">
            BOOK NOW!
        </a>

    </nav>

</header>



<!-- =========================================================
     MY BOOKINGS
     ========================================================= -->

<section class="my-bookings">


    <!-- PAGE HEADER -->

    <div class="my-bookings-header">

        <h1>
            MY BOOKINGS
        </h1>


        <p>

            Welcome,

            <?php echo htmlspecialchars($_SESSION["customer_name"]); ?>!

        </p>


        <!-- BOOK ANOTHER ROOM BUTTON -->

        <a
            href="book.php"
            class="book-another-button"
        >
            BOOK ANOTHER ROOM
        </a>

    </div>



    <!-- =====================================================
         NO BOOKINGS
         ===================================================== -->

    <?php if (empty($bookings)): ?>


        <div class="no-bookings">


            <h2>
                No Bookings Yet
            </h2>


            <p>
                You don't have any bookings yet.
            </p>


            <a
                href="book.php"
                class="book-btn"
            >

                BOOK A ROOM

            </a>


        </div>



    <?php else: ?>


        <!-- =================================================
             BOOKINGS CONTAINER
             ================================================= -->

        <div class="bookings-container">


            <?php foreach ($bookings as $booking): ?>


                <div class="booking-card">


                    <!-- =====================================
                         ROOM IMAGE
                         ===================================== -->

                    <div class="booking-image">

                        <img
                            src="assets/images/<?php echo htmlspecialchars($booking["image"]); ?>"
                            alt="<?php echo htmlspecialchars($booking["room_name"]); ?>"
                        >

                    </div>



                    <!-- =====================================
                         BOOKING INFORMATION
                         ===================================== -->

                    <div class="booking-info">


                        <!-- ROOM TYPE -->

                        <h2>

                            <?php echo htmlspecialchars($booking["room_name"]); ?>

                        </h2>



                        <!-- ROOM NUMBER -->

                        <p>

                            <strong>
                                Room Number:
                            </strong>

                            <?php echo htmlspecialchars($booking["room_number"]); ?>

                        </p>



                        <!-- CHECK-IN -->

                        <p>

                            <strong>
                                Check-in:
                            </strong>

                            <?php echo htmlspecialchars($booking["check_in"]); ?>

                        </p>



                        <!-- CHECK-OUT -->

                        <p>

                            <strong>
                                Check-out:
                            </strong>

                            <?php echo htmlspecialchars($booking["check_out"]); ?>

                        </p>



                        <!-- NUMBER OF DAYS -->

                        <p>

                            <strong>
                                Number of Days:
                            </strong>

                            <?php echo htmlspecialchars($booking["number_of_days"]); ?>

                        </p>



                        <!-- ROOM PRICE -->

                        <p>

                            <strong>
                                Room Price:
                            </strong>

                            ₱<?php echo number_format(
                                $booking["room_price"],
                                2
                            ); ?>

                        </p>



                        <!-- TOTAL COST -->

                        <p>

                            <strong>
                                Total Cost:
                            </strong>

                            ₱<?php echo number_format(
                                $booking["total_cost"],
                                2
                            ); ?>

                        </p>



                        <!-- STATUS -->

                        <p>

                            <strong>
                                Status:
                            </strong>


                            <span class="booking-status">

                                <?php
                                echo htmlspecialchars(
                                    $booking["status"]
                                );
                                ?>

                            </span>

                        </p>



                        <!-- =================================================
                             CONFIRMED MESSAGE
                             ================================================= -->

                        <?php if ($booking["status"] === "Confirmed"): ?>

                            <div class="booking-confirmed-message">

                                <strong>
                                    Booking Confirmed!
                                </strong>

                                <p>
                                    Your reservation is confirmed.
                                    We look forward to welcoming you on

                                    <?php
                                    echo date(
                                        "F, d, Y",
                                        strtotime($booking["check_in"])
                                    );
                                    ?>.
                                </p>

                            </div>


                        <!-- =================================================
                             PENDING MESSAGE
                             ================================================= -->

                        <?php elseif ($booking["status"] === "Pending"): ?>

                            <div class="booking-pending-message">

                                <strong>
                                    Booking Pending
                                </strong>

                                <p>
                                    Your reservation is waiting for confirmation.
                                </p>

                            </div>


                        <!-- =================================================
                             CANCELLED MESSAGE
                             ================================================= -->

                        <?php elseif ($booking["status"] === "Cancelled"): ?>

                            <div class="booking-cancelled-message">

                                <strong>
                                    Booking Cancelled
                                </strong>

                                <p>
                                    This reservation has been cancelled.
                                </p>

                            </div>

                        <?php endif; ?>



                        <!-- =================================================
                             WRITE A REVIEW BUTTON
                             ================================================= -->

                        <?php if ($booking["status"] === "Confirmed"): ?>

                            <a
                                href="review.php"
                                class="review-button"
                            >
                                WRITE A REVIEW
                            </a>

                        <?php endif; ?>


                    </div>


                </div>


            <?php endforeach; ?>


        </div>


    <?php endif; ?>


</section>



<!-- =========================================================
     LOGOUT BUTTON
     ========================================================= -->

<div class="my-bookings-logout">

    <a href="logout.php">
        LOGOUT
    </a>

</div>



<!-- =========================================================
     FOOTER
     ========================================================= -->

<footer class="footer">


    <!-- FOOTER BRAND -->

    <div class="footer-brand">


        <!-- FOOTER LOGO -->

        <div class="footer-logo">

            <img
                src="assets/images/mainlogo.jpg"
                alt="Bill's Resort Logo"
            >

        </div>



        <!-- LOCATION -->

        <p class="footer-location">

            San Jose, Negros Oriental

        </p>



        <!-- SOCIAL MEDIA -->

        <div class="social">


            <!-- FACEBOOK -->

            <div class="social-item">

                <span class="social-icon">
                    f
                </span>


                <span class="social-name">
                    Bill's Resort
                </span>

            </div>



            <!-- INSTAGRAM -->

            <div class="social-item">

                <span class="social-icon">
                    ◎
                </span>


                <span class="social-name">
                    Bill's Resort2006
                </span>

            </div>


        </div>


    </div>



    <!-- VERTICAL LINE -->

    <div class="footer-line"></div>



    <!-- QUICK LINKS -->

    <div class="quicklinks">


        <h3>
            QUICKLINKS
        </h3>


        <a href="index.php">
            HOME
        </a>


        <a href="index.php#about">
            ABOUT US
        </a>


        <a href="index.php#rooms">
            ROOMS & AMENITIES
        </a>


        <a href="index.php#gallery">
            GALLERY
        </a>


        <a href="index.php#contact">
            CONTACT US
        </a>


        <a href="book.php">
            BOOK NOW!
        </a>


    </div>


</footer>



<!-- =========================================================
     BOTTOM FOOTER
     ========================================================= -->

<div class="bottom-footer">

    ALL RIGHTS RESERVED.

    &nbsp;

    Bill's Resort, San Jose, Negros Oriental

</div>



</body>

</html>