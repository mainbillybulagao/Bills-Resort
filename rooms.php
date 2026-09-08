<?php

require_once "config/Database.php";
require_once "classes/Room.php";

// Connect to database
$database = new Database();
$db = $database->getConnection();

// Create Room object
$roomObject = new Room($db);

// Get all rooms
$rooms = $roomObject->getAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rooms & Amenities - Bill's Resort</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<!-- HEADER -->

<header class="header">

    

<div class="line"></div>

    <div class="logo-box">

        <img src="assets/images/mainlogo.jpg" alt="Bill's Resort Logo">

    </div>


    <nav class="navbar">

        <a href="#home">HOME</a>

        <a href="#about">ABOUT US</a>

        <a href="#rooms">ROOMS & AMENITIES</a>

        <a href="#gallery">GALLERY</a>

        <a href="#contact">CONTACT US</a>

       <a href="login.php" class="book-btn">
    BOOK NOW!
</a>

    </nav>

</header>

    <!-- ROOMS -->

    <main>

        <section class="rooms-page">

            <div class="section-title">

                <h1>ROOMS & AMENITIES</h1>

                <p>
                    Choose the perfect room for your stay at Bill's Resort.
                </p>

            </div>


            <div class="rooms-page-grid">

                <?php if (!empty($rooms)): ?>

                    <?php foreach ($rooms as $room): ?>

                        <div class="room-card">

                            <div class="room-image">

                                <img
                                    src="assets/images/<?php echo htmlspecialchars($room['image']); ?>"
                                    alt="<?php echo htmlspecialchars($room['room_name']); ?>"
                                >

                            </div>


                            <div class="room-info">

                                <h2>
                                    <?php echo htmlspecialchars($room['room_name']); ?>
                                </h2>

                                <p>
                                    <?php echo htmlspecialchars($room['description']); ?>
                                </p>

                                <p class="room-capacity">
                                    Capacity:
                                    <?php echo htmlspecialchars($room['capacity']); ?>
                                    guests
                                </p>

                                <h3 class="room-price">
                                    ₱<?php echo number_format($room['price'], 2); ?>
                                    <span>/ night</span>
                                </h3>


                                <a href="login.php" class="main-btn">
                                    BOOK THIS ROOM
                                </a>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>No rooms are currently available.</p>

                <?php endif; ?>

            </div>

        </section>

    </main>

<!-- =========================================================
     FOOTER
     ========================================================= -->

<footer class="footer">


    <!-- =====================================================
         FOOTER BRAND
         ===================================================== -->

    <div class="footer-brand">

        <div class="footer-logo">

            <img 
                src="assets/images/mainlogo.jpg" 
                alt="Bill's Resort Logo"
            >

        </div>


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



    <!-- =====================================================
         VERTICAL LINE
         ===================================================== -->

    <div class="footer-line"></div>



    <!-- =====================================================
         QUICK LINKS
         ===================================================== -->

    <div class="quicklinks">


        <h3>
            QUICKLINKS
        </h3>


        <a href="#home">
            HOME
        </a>

        <a href="#about">
            ABOUT US
        </a>

        <a href="#rooms">
            ROOMS & AMENITIES
        </a>

        <a href="#gallery">
            GALLERY
        </a>

        <a href="#contact">
            CONTACT US
        </a>

       <a href="login.php">
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