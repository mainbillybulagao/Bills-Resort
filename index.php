<?php

require_once "config/Database.php";

$database = new Database();
$db = $database->getConnection();

$success = "";
$error = "";


/* =========================================================
   CONTACT FORM
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");


    /* =====================================================
       VALIDATION
       ===================================================== */

    if ($name === "" || $email === "" || $message === "") {

        $error = "Please fill in all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        try {

            $query = "INSERT INTO messages 
                      (name, email, message)
                      VALUES 
                      (:name, :email, :message)";

            $stmt = $db->prepare($query);

            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":message", $message);


            if ($stmt->execute()) {

                $success = "Your message has been sent successfully!";

            } else {

                $error = "Something went wrong. Please try again.";

            }

        } catch (PDOException $e) {

            $error = "Unable to send your message.";

        }

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bill's Resort</title>


    <!-- GOOGLE FONTS -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet">


    <!-- CSS -->

    <link rel="stylesheet" href="assets/css/style.css">

</head>


<body>


<!-- =========================================================
     HEADER
     ========================================================= -->

<header class="header">


    <div class="line"></div>


    <div class="logo-box">

        <img 
            src="assets/images/mainlogo.jpg" 
            alt="Bill's Resort Logo"
        >

    </div>


    <nav class="navbar">

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

        <a href="login.php" class="book-btn">
            BOOK NOW!
        </a>

    </nav>

</header>



<!-- =========================================================
     HERO
     ========================================================= -->

<section class="hero" id="home">


    <!-- LEFT SIDE -->

    <div class="hero-visual">


        <!-- Smaller image on top -->

        <div class="hero-image">

            <img
                src="assets/images/hero.jpg"
                alt="Bill's Resort"
            >

        </div>

    </div>


    <!-- RIGHT SIDE -->

    <div class="hero-content">

        <h1>
            Welcome to Bill's Resort
        </h1>

        <h3>
            San Jose, Negros Oriental
        </h3>

        <p>
            Enjoy a relaxing stay right<br>
            by the beach. Come and<br>
            experience our comfortable<br>
            rooms, beautiful views, and<br>
            a quiet place to unwind.
        </p>

    </div>

</section>



<!-- =========================================================
     ABOUT
     ========================================================= -->

<section class="about section-line" id="about">


    <div class="about-content">

        <h2>
            ABOUT BILL'S RESORT
        </h2>


        <p>
            Bill's Resort is a relaxing destination
            located in San Jose, Negros Oriental.
            Enjoy comfortable rooms, beautiful
            surroundings, refreshing views, and
            a peaceful place to spend time with
            family and friends.
        </p>


        <a href="#about" class="main-btn">
            MORE ABOUT US
        </a>

    </div>


    <div class="about-image">

        <img
            src="assets/images/about.jpg"
            alt="About Bill's Resort"
        >

    </div>


</section>



<!-- =========================================================
     ROOMS & AMENITIES
     ========================================================= -->

<section class="rooms section-line" id="rooms">


    <h2>
        ROOMS & AMENITIES
    </h2>


    <div class="rooms-grid">


        <!-- ROOM 1 -->

        <div class="room-item">

            <img
                src="assets/images/room1.jpg"
                alt="Bill's Resort Room"
            >

            <div class="room-text">

                <h3>
                    COMFORTABLE ROOMS
                </h3>

                <p>
                    Relax in our comfortable rooms
                    designed for a peaceful and
                    enjoyable stay.
                </p>

            </div>

        </div>



        <!-- AMENITY 1 -->

        <div class="room-item">

            <img
                src="assets/images/amenity1.jpg"
                alt="Bill's Resort Activity"
            >

            <div class="room-text">

                <h3>
                    WATER ACTIVITIES
                </h3>

                <p>
                    Enjoy exciting water activities
                    and make your stay more
                    memorable.
                </p>

            </div>

        </div>



        <!-- ROOM 2 -->

        <div class="room-item">

            <img
                src="assets/images/room2.jpg"
                alt="Bill's Resort Activity"
            >

            <div class="room-text">

                <h3>
                    COZY ACCOMMODATION
                </h3>

                <p>
                    Experience a cozy atmosphere
                    where you can rest and enjoy
                    your vacation.
                </p>

            </div>

        </div>



        <!-- AMENITY 2 -->

        <div class="room-item">

            <img
                src="assets/images/amenity2.jpg"
                alt="Bill's Resort Activity"
            >

            <div class="room-text">

                <h3>
                    FUN ACTIVITIES
                </h3>

                <p>
                    Spend quality time with family
                    and friends through our
                    enjoyable resort activities.
                </p>

            </div>

        </div>


    </div>


    <a href="#gallery" class="main-btn rooms-btn">
        SHOW MORE
    </a>


</section>



<!-- =========================================================
     GALLERY
     ========================================================= -->

<section class="gallery" id="gallery">


    <h2>
        GALLERY
    </h2>


    <div class="gallery-grid">


        <img
            src="assets/images/gallery1.jpg"
            alt="Bill's Resort Gallery"
        >


        <img
            src="assets/images/gallery2.jpg"
            alt="Bill's Resort Gallery"
        >


        <img
            src="assets/images/gallery3.jpg"
            alt="Bill's Resort Gallery"
        >


        <img
            src="assets/images/gallery4.jpg"
            alt="Bill's Resort Gallery"
        >


        <img
            src="assets/images/gallery5.jpg"
            alt="Bill's Resort Gallery"
        >


        <img
            src="assets/images/gallery6.jpg"
            alt="Bill's Resort Gallery"
        >


    </div>


   
</section>



<!-- =========================================================
     CONTACT
     ========================================================= -->

<section class="contact section-line" id="contact">


    <!-- CONTACT FORM -->

    <div class="contact-form">


        <h2>
            Send Us a Message
        </h2>


        <!-- SUCCESS MESSAGE -->

        <?php if ($success != ""): ?>

            <p class="success-message">
                <?php echo htmlspecialchars($success); ?>
            </p>

        <?php endif; ?>


        <!-- ERROR MESSAGE -->

        <?php if ($error != ""): ?>

            <p class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>


        <form method="POST" action="#contact">


            <input
                type="text"
                name="name"
                placeholder="Name"
                required
            >


            <input
                type="email"
                name="email"
                placeholder="Email Address"
                required
            >


            <textarea
                name="message"
                placeholder="Message"
                required
            ></textarea>


            <button type="submit">
                SEND MESSAGE
            </button>


        </form>


    </div>



    <!-- CONTACT INFORMATION -->

    <div class="contact-info">


        <h2>
            CONTACT INFORMATION
        </h2>


        <h3>
            We're Here to Help
        </h3>


        <p>
            Have questions about our rooms,
            amenities, or your stay? Feel free
            to contact us. We are happy to help
            you plan a relaxing visit to Bill's Resort.
        </p>


        <div class="info-item">

            <span class="icon">
                📍
            </span>

            <span>
                San Jose, Negros Oriental
            </span>

        </div>


        <div class="info-item">

            <span class="icon">
                ☎
            </span>

            <span>
                0955 666 7777
            </span>

        </div>


        <div class="info-item">

            <span class="icon">
                ✉
            </span>

            <span>
                Billsresortinfo@gmail.com
            </span>

        </div>


    </div>


</section>



<!-- =========================================================
     FOOTER
     ========================================================= -->

<footer class="footer">


    <!-- FOOTER BRAND -->

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



    <!-- VERTICAL LINE -->

    <div class="footer-line"></div>



    <!-- QUICK LINKS -->

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


</body>

</html>