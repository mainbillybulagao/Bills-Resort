<?php

session_start();

require_once "config/Database.php";
require_once "classes/Room.php";
require_once "classes/Booking.php";


/* =========================================================
   CHECK CUSTOMER LOGIN
   ========================================================= */

if (!isset($_SESSION["customer_id"])) {

    header("Location: login.php");
    exit();

}


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

$database = new Database();

$db = $database->getConnection();


/* =========================================================
   OBJECTS
   ========================================================= */

$roomObject = new Room($db);

$bookingObject = new Booking($db);


/* =========================================================
   GET ALL ROOMS
   ========================================================= */

$rooms = $roomObject->getAll();


/* =========================================================
   MESSAGES
   ========================================================= */

$error = "";

$success = "";


/* =========================================================
   BOOKING FORM
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* =====================================================
       GET FORM VALUES
       ===================================================== */

    $room_name = trim($_POST["room_name"] ?? "");

    $check_in = trim($_POST["check_in"] ?? "");

    $check_out = trim($_POST["check_out"] ?? "");


    /* =====================================================
       REQUIRED FIELD VALIDATION
       ===================================================== */

    if (
        empty($room_name) ||
        empty($check_in) ||
        empty($check_out)
    ) {

        $error = "Please complete all booking information.";

    }


    /* =====================================================
       CHECK-IN DATE VALIDATION
       ===================================================== */

    elseif ($check_in < date("Y-m-d")) {

        $error = "Check-in date cannot be in the past.";

    }


    /* =====================================================
       CHECK-OUT DATE VALIDATION
       ===================================================== */

    elseif ($check_out <= $check_in) {

        $error = "Check-out date must be after check-in date.";

    }


    /* =====================================================
       FIND AVAILABLE ROOM
       ===================================================== */

    else {

        $room = $bookingObject->findAvailableRoom(
            $room_name,
            $check_in,
            $check_out
        );


        /* =================================================
           NO AVAILABLE ROOM
           ================================================= */

        if (!$room) {

            $error =
                "Sorry, no rooms of this type are available for your selected dates.";

        }


        /* =================================================
           ROOM AVAILABLE
           ================================================= */

        else {


            /* =============================================
               CALCULATE NUMBER OF DAYS
               ============================================= */

            $checkInDate = new DateTime($check_in);

            $checkOutDate = new DateTime($check_out);

            $difference = $checkInDate->diff($checkOutDate);

            $number_of_days = $difference->days;


            /* =============================================
               GET ROOM PRICE
               ============================================= */

            $room_price = $room["price"];


            /* =============================================
               CALCULATE TOTAL COST
               ============================================= */

            $total_cost = $number_of_days * $room_price;


            /* =============================================
               CREATE BOOKING
               ============================================= */

            $result = $bookingObject->createBooking(
                $_SESSION["customer_id"],
                $room["room_id"],
                $check_in,
                $check_out,
                $number_of_days,
                $room_price,
                $total_cost
            );


            /* =============================================
               SUCCESS
               ============================================= */

            if ($result) {

                // Automatically go to My Bookings
                header("Location: my-bookings.php");
                exit();

            }


            /* =============================================
               DATABASE ERROR
               ============================================= */

            else {

                $error =
                    "Something went wrong. Please try again.";

            }

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

    <title>Book Your Stay - Bill's Resort</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<!-- =========================================================
     LOGO
     ========================================================= -->

<header class="register-header">

    <a href="index.php">

        <img
            src="assets/images/mainlogo.jpg"
            alt="Bill's Resort Logo"
        >

    </a>

</header>


<!-- =========================================================
     BOOKING
     ========================================================= -->

<div class="register-container">


    <h1>
        Book Your Stay
    </h1>


    <p>

        Welcome,

        <?php
        echo htmlspecialchars(
            $_SESSION["customer_name"]
        );
        ?>!

    </p>


    <!-- =====================================================
         ERROR MESSAGE
         ===================================================== -->

    <?php if (!empty($error)): ?>

        <p style="color: red;">

            <?php
            echo htmlspecialchars($error);
            ?>

        </p>

    <?php endif; ?>


    <!-- =====================================================
         SUCCESS MESSAGE
         ===================================================== -->

    <?php if (!empty($success)): ?>

        <p style="color: green;">

            <?php
            echo htmlspecialchars($success);
            ?>

        </p>

    <?php endif; ?>


    <!-- =====================================================
         BOOKING FORM
         ===================================================== -->

    <form
        method="POST"
        action=""
    >


        <!-- =================================================
             ROOM TYPE
             ================================================= -->

        <label>
            Select Room Type
        </label>


        <select
            name="room_name"
            required
        >

            <option value="">

                -- Select a Room Type --

            </option>


            <?php

            $roomTypes = [];

            foreach ($rooms as $room) {

                if (
                    !in_array(
                        $room["room_name"],
                        $roomTypes
                    )
                ) {

                    $roomTypes[] = $room["room_name"];

            ?>

                    <option
                        value="<?php
                        echo htmlspecialchars(
                            $room["room_name"]
                        );
                        ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $room["room_name"]
                        );
                        ?>

                        -

                        ₱<?php
                        echo number_format(
                            $room["price"],
                            2
                        );
                        ?>

                        / night

                    </option>

            <?php

                }

            }

            ?>

        </select>


        <!-- =================================================
             CHECK-IN
             ================================================= -->

        <label>
            Check-in Date
        </label>


        <input
            type="date"
            name="check_in"
            min="<?php echo date('Y-m-d'); ?>"
            required
        >


        <!-- =================================================
             CHECK-OUT
             ================================================= -->

        <label>
            Check-out Date
        </label>


        <input
            type="date"
            name="check_out"
            min="<?php echo date('Y-m-d'); ?>"
            required
        >


        <!-- =================================================
             BUTTON
             ================================================= -->

        <button type="submit">

            CONFIRM BOOKING

        </button>


    </form>


    <!-- =====================================================
         BACK TO HOME
         ===================================================== -->

    <p>

        <a href="index.php">

            Back to Home

        </a>

    </p>


</div>


</body>

</html>