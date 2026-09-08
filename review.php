<?php

session_start();

require_once "config/Database.php";
require_once "classes/Booking.php";


// =========================================================
// CHECK CUSTOMER LOGIN
// =========================================================

if (!isset($_SESSION["customer_id"])) {

    header("Location: login.php");
    exit();

}


// =========================================================
// DATABASE CONNECTION
// =========================================================

$database = new Database();
$db = $database->getConnection();


// =========================================================
// CUSTOMER INFORMATION
// =========================================================

$customer_id = $_SESSION["customer_id"];


// =========================================================
// VARIABLES
// =========================================================

$success = "";
$error = "";


// =========================================================
// SUBMIT REVIEW
// =========================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form values safely
    $rating = intval($_POST["rating"] ?? 0);
    $comment = trim($_POST["comment"] ?? "");


    // =====================================================
    // VALIDATION
    // =====================================================

    if ($rating < 1 || $rating > 5) {

        $error = "Please select a rating from 1 to 5.";

    } elseif ($comment === "") {

        $error = "Please write a comment.";

    } else {

        try {

            // =================================================
            // INSERT REVIEW
            // =================================================

            $query = "INSERT INTO reviews
                      (customer_id, rating, comment)
                      VALUES
                      (:customer_id, :rating, :comment)";

            $stmt = $db->prepare($query);

            $stmt->bindParam(":customer_id", $customer_id);
            $stmt->bindParam(":rating", $rating);
            $stmt->bindParam(":comment", $comment);


            if ($stmt->execute()) {

                $success =
                    "Thank you! Your review has been submitted and is waiting for approval.";

                // Clear the form after successful submission
                $rating = 0;
                $comment = "";

            } else {

                $error =
                    "Something went wrong. Please try again.";

            }

        } catch (PDOException $e) {

            // Log the technical error
            error_log($e->getMessage());

            // Show only a friendly message to the customer
            $error = "Unable to submit your review.";

        }

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Write a Review - Bill's Resort</title>


    <!-- =====================================================
         GOOGLE FONTS
         ===================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         CSS
         ===================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/style.css?v=4"
    >


    <style>

        /* =================================================
           REVIEW PAGE
           ================================================= */

        .review-page {

            min-height: 80vh;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 60px 20px;

            background-color: var(--bg-color);

        }


        /* =================================================
           REVIEW BOX
           ================================================= */

        .review-box {

            width: 100%;

            max-width: 650px;

            background-color: var(--white);

            padding: 45px;

            border-radius: 15px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.10);

        }


        /* =================================================
           TITLE
           ================================================= */

        .review-box h1 {

            color: var(--primary-color);

            font-family: var(--font-heading);

            text-align: center;

            margin-bottom: 10px;

        }


        /* =================================================
           INTRODUCTION
           ================================================= */

        .review-box .intro {

            text-align: center;

            margin-bottom: 30px;

            color: var(--text-color);

            font-family: var(--font-body);

        }


        /* =================================================
           RATING LABEL
           ================================================= */

        .rating-label {

            display: block;

            margin-bottom: 10px;

            font-family: var(--font-body);

            font-weight: 600;

        }


        /* =================================================
           RATING SELECT
           ================================================= */

        .rating-select {

            width: 100%;

            padding: 13px;

            border: 1px solid var(--border-color);

            border-radius: 6px;

            margin-bottom: 20px;

            font-family: var(--font-body);

            font-size: 15px;

            background-color: var(--white);

        }


        /* =================================================
           COMMENT LABEL
           ================================================= */

        .comment-label {

            display: block;

            margin-bottom: 10px;

            font-family: var(--font-body);

            font-weight: 600;

        }


        /* =================================================
           COMMENT BOX
           ================================================= */

        .comment-box {

            width: 100%;

            min-height: 150px;

            padding: 15px;

            border: 1px solid var(--border-color);

            border-radius: 6px;

            resize: vertical;

            font-family: var(--font-body);

            font-size: 15px;

            margin-bottom: 20px;

            box-sizing: border-box;

        }


        /* =================================================
           REVIEW SUBMIT BUTTON
           ================================================= */

        .review-submit {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 6px;

            background-color: var(--primary-color);

            color: var(--white);

            font-family: var(--font-body);

            font-weight: 600;

            cursor: pointer;

            transition: 0.3s ease;

        }


        .review-submit:hover {

            background-color: var(--primary-dark);

        }


        /* =================================================
           SUCCESS MESSAGE
           ================================================= */

        .success-message {

            padding: 12px;

            margin-bottom: 20px;

            background-color: #d4edda;

            color: #155724;

            border-radius: 6px;

            font-family: var(--font-body);

        }


        /* =================================================
           ERROR MESSAGE
           ================================================= */

        .error-message {

            padding: 12px;

            margin-bottom: 20px;

            background-color: #f8d7da;

            color: #721c24;

            border-radius: 6px;

            font-family: var(--font-body);

        }


        /* =================================================
           BACK LINK
           ================================================= */

        .back-link {

            display: block;

            text-align: center;

            margin-top: 20px;

            color: var(--primary-color);

            text-decoration: none;

            font-family: var(--font-body);

            font-weight: 600;

            transition: 0.3s ease;

        }


        .back-link:hover {

            color: var(--primary-dark);

            text-decoration: underline;

        }


        /* =================================================
           MOBILE
           ================================================= */

        @media (max-width: 600px) {

            .review-box {

                padding: 25px;

            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     HEADER
     ========================================================= -->

<header class="header">


    <div class="line"></div>


    <!-- LOGO -->

    <div class="logo-box">

        <a href="index.php">

            <img
                src="assets/images/mainlogo.jpg"
                alt="Bill's Resort Logo"
            >

        </a>

    </div>


    <!-- NAVIGATION -->

    <nav class="navbar">

        <a href="index.php#home">
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
     REVIEW PAGE
     ========================================================= -->

<section class="review-page">


    <div class="review-box">


        <h1>
            WRITE A REVIEW
        </h1>


        <p class="intro">
            Share your experience at Bill's Resort.
        </p>


        <!-- =================================================
             SUCCESS
             ================================================= -->

        <?php if ($success !== ""): ?>

            <p class="success-message">

                <?php
                echo htmlspecialchars($success);
                ?>

            </p>

        <?php endif; ?>


        <!-- =================================================
             ERROR
             ================================================= -->

        <?php if ($error !== ""): ?>

            <p class="error-message">

                <?php
                echo htmlspecialchars($error);
                ?>

            </p>

        <?php endif; ?>


        <!-- =================================================
             REVIEW FORM
             ================================================= -->

        <form
            method="POST"
            action="review.php"
        >


            <!-- RATING -->

            <label
                for="rating"
                class="rating-label"
            >
                Rating
            </label>


            <select
                id="rating"
                name="rating"
                class="rating-select"
                required
            >

                <option value="">
                    Select Rating
                </option>

                <option value="5">
                    ⭐⭐⭐⭐⭐ - Excellent
                </option>

                <option value="4">
                    ⭐⭐⭐⭐ - Very Good
                </option>

                <option value="3">
                    ⭐⭐⭐ - Good
                </option>

                <option value="2">
                    ⭐⭐ - Fair
                </option>

                <option value="1">
                    ⭐ - Poor
                </option>

            </select>


            <!-- COMMENT -->

            <label
                for="comment"
                class="comment-label"
            >
                Your Review
            </label>


            <textarea
                id="comment"
                name="comment"
                class="comment-box"
                placeholder="Write your experience here..."
                required
            ></textarea>


            <!-- SUBMIT -->

            <button
                type="submit"
                class="review-submit"
            >
                SUBMIT REVIEW
            </button>


        </form>


        <!-- =================================================
             BACK TO MY BOOKINGS
             ================================================= -->

        <a
            href="my-bookings.php"
            class="back-link"
        >
            ← BACK TO MY BOOKINGS
        </a>


    </div>


</section>


<!-- =========================================================
     FOOTER
     ========================================================= -->

<footer class="footer">


    <!-- FOOTER BRAND -->

    <div class="footer-brand">


        <!-- LOGO -->

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


        <a href="index.php#home">
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