<?php

session_start();

require_once "../config/Database.php";


/* =========================================
   CHECK ADMIN LOGIN
========================================= */

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit();

}


/* =========================================
   DATABASE CONNECTION
========================================= */

$database = new Database();
$db = $database->getConnection();


/* =========================================
   HANDLE BOOKING STATUS
========================================= */

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $booking_id = $_POST["booking_id"] ?? "";
    $status = $_POST["status"] ?? "";


    if (!empty($booking_id) && in_array($status, ["Confirmed", "Cancelled"])) {

        $query = "UPDATE bookings
                  SET status = :status
                  WHERE booking_id = :booking_id";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":booking_id", $booking_id);


        if ($stmt->execute()) {

            if ($status == "Confirmed") {

                $message = "Booking confirmed successfully.";

            } else {

                $message = "Booking cancelled successfully.";

            }

        } else {

            $error = "Unable to update the booking.";

        }

    }

}


/* =========================================
   GET ALL BOOKINGS
========================================= */

$query = "SELECT
            bookings.booking_id,
            bookings.check_in,
            bookings.check_out,
            bookings.number_of_days,
            bookings.room_price,
            bookings.total_cost,
            bookings.status,
            bookings.created_at,

            customers.first_name,
            customers.last_name,
            customers.email,
            customers.phone,

            rooms.room_name,
            rooms.room_number,
            rooms.image

          FROM bookings

          INNER JOIN customers
          ON bookings.customer_id = customers.customer_id

          INNER JOIN rooms
          ON bookings.room_id = rooms.room_id

          ORDER BY bookings.created_at DESC";


$stmt = $db->prepare($query);
$stmt->execute();

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Bookings | Bill's Resort</title>

   <link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<!-- =========================================================
     ADMIN HEADER
     ========================================================= -->

<header class="admin-header">


    <!-- LOGO -->

    <div class="admin-logo">

        <img
            src="../assets/images/mainlogo.jpg"
            alt="Bill's Resort Logo"
        >

    </div>


    <!-- ADMIN USER -->

    <div class="admin-user">

        <span>

            Welcome,
            <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>

        </span>


        <a href="logout.php">
            LOGOUT
        </a>

    </div>


</header>



<!-- =========================================================
     ADMIN SIDEBAR
     ========================================================= -->

<aside class="admin-sidebar">


    <h2>
        ADMIN PANEL
    </h2>


    <a href="dashboard.php">
        DASHBOARD
    </a>


    <a href="bookings.php" class="active">
        BOOKINGS
    </a>


    <a href="rooms.php">
        ROOMS
    </a>


    <a href="messages.php">
        MESSAGES
    </a>


    <a href="reviews.php">
        REVIEWS
    </a>


    <a href="../index.php">
        VIEW WEBSITE
    </a>


</aside>



<!-- =========================================================
     MAIN CONTENT
     ========================================================= -->

<main class="admin-content">


    <h1>
        MANAGE BOOKINGS
    </h1>


    <p class="admin-welcome">

        View and manage all customer bookings.

    </p>



    <!-- =====================================================
         SUCCESS MESSAGE
         ===================================================== -->

    <?php if (!empty($message)): ?>

        <div class="admin-message success-message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>



    <!-- =====================================================
         ERROR MESSAGE
         ===================================================== -->

    <?php if (!empty($error)): ?>

        <div class="admin-message error-message">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>



    <!-- =====================================================
         BOOKINGS TABLE
         ===================================================== -->

    <div class="admin-table-container">


        <?php if (empty($bookings)): ?>


            <div class="no-bookings">

                <h2>
                    No Bookings Found
                </h2>

                <p>
                    There are currently no customer bookings.
                </p>

            </div>


        <?php else: ?>


            <table class="admin-table">


                <thead>

                    <tr>

                        <th>
                            Booking ID
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Contact
                        </th>

                        <th>
                            Room
                        </th>

                        <th>
                            Check-in
                        </th>

                        <th>
                            Check-out
                        </th>

                        <th>
                            Days
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($bookings as $booking): ?>


                        <tr>


                            <!-- BOOKING ID -->

                            <td>

                                #
                                <?php echo htmlspecialchars($booking["booking_id"]); ?>

                            </td>



                            <!-- CUSTOMER -->

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $booking["first_name"] . " " . $booking["last_name"]
                                    );
                                    ?>

                                </strong>

                                <br>

                                <?php echo htmlspecialchars($booking["email"]); ?>

                            </td>



                            <!-- CONTACT -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $booking["phone"] ?? "N/A"
                                );
                                ?>

                            </td>



                            <!-- ROOM -->

                            <td>

                                <strong>

                                    <?php echo htmlspecialchars($booking["room_name"]); ?>

                                </strong>

                                <br>

                                Room
                                <?php echo htmlspecialchars($booking["room_number"]); ?>

                            </td>



                            <!-- CHECK-IN -->

                            <td>

                                <?php echo htmlspecialchars($booking["check_in"]); ?>

                            </td>



                            <!-- CHECK-OUT -->

                            <td>

                                <?php echo htmlspecialchars($booking["check_out"]); ?>

                            </td>



                            <!-- DAYS -->

                            <td>

                                <?php echo htmlspecialchars($booking["number_of_days"]); ?>

                            </td>



                            <!-- TOTAL -->

                            <td>

                                ₱<?php echo number_format($booking["total_cost"], 2); ?>

                            </td>



                            <!-- STATUS -->

                            <td>

                                <span class="booking-status">

                                    <?php echo htmlspecialchars($booking["status"]); ?>

                                </span>

                            </td>



                            <!-- ACTION -->

                            <td>


                                <?php if ($booking["status"] == "Pending"): ?>


                                    <!-- CONFIRM -->

                                    <form
                                        method="POST"
                                        action=""
                                        style="display:inline;"
                                    >

                                        <input
                                            type="hidden"
                                            name="booking_id"
                                            value="<?php echo $booking["booking_id"]; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="Confirmed"
                                        >

                                        <button type="submit">
                                            CONFIRM
                                        </button>

                                    </form>


                                    <!-- CANCEL -->

                                    <form
                                        method="POST"
                                        action=""
                                        style="display:inline;"
                                    >

                                        <input
                                            type="hidden"
                                            name="booking_id"
                                            value="<?php echo $booking["booking_id"]; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="Cancelled"
                                        >

                                        <button type="submit">
                                            CANCEL
                                        </button>

                                    </form>


                                <?php else: ?>


                                    <span>

                                        No Action

                                    </span>


                                <?php endif; ?>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>


            </table>


        <?php endif; ?>


    </div>


</main>



</body>

</html>