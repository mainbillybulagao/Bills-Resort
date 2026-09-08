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
   DELETE ROOM
========================================= */

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"] ?? "";
    $room_id = $_POST["room_id"] ?? "";


    if ($action == "delete" && !empty($room_id)) {

        /*
         * Check if the room has bookings first.
         */

        $query = "SELECT COUNT(*) AS total
                  FROM bookings
                  WHERE room_id = :room_id";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":room_id", $room_id);

        $stmt->execute();

        $booking_count = $stmt->fetch(PDO::FETCH_ASSOC)["total"];


        if ($booking_count > 0) {

            $error = "This room cannot be deleted because it has booking records.";

        } else {

            $query = "DELETE FROM rooms
                      WHERE room_id = :room_id";

            $stmt = $db->prepare($query);

            $stmt->bindParam(":room_id", $room_id);


            if ($stmt->execute()) {

                $message = "Room deleted successfully.";

            } else {

                $error = "Unable to delete the room.";

            }

        }

    }

}


/* =========================================
   GET ALL ROOMS
========================================= */

$query = "SELECT *
          FROM rooms
          ORDER BY room_number ASC";

$stmt = $db->prepare($query);

$stmt->execute();

$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Rooms | Bill's Resort</title>

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


    <a href="bookings.php">
        BOOKINGS
    </a>


    <a href="rooms.php" class="active">
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


    <div class="admin-title-row">


        <div>

            <h1>
                MANAGE ROOMS
            </h1>

            <p class="admin-welcome">

                Manage the resort's physical rooms.

            </p>

        </div>


        <a href="add-room.php" class="admin-add-button">

            + ADD ROOM

        </a>


    </div>



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
         ROOMS TABLE
         ===================================================== -->

    <div class="admin-table-container">


        <?php if (empty($rooms)): ?>


            <div class="no-bookings">

                <h2>
                    No Rooms Found
                </h2>

                <p>
                    Add your first room to the system.
                </p>


                <a href="add-room.php" class="book-btn">

                    ADD ROOM

                </a>

            </div>


        <?php else: ?>


            <table class="admin-table">


                <thead>

                    <tr>

                        <th>
                            Room #
                        </th>

                        <th>
                            Room Type
                        </th>

                        <th>
                            Price
                        </th>

                        <th>
                            Capacity
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


                    <?php foreach ($rooms as $room): ?>


                        <tr>


                            <!-- ROOM NUMBER -->

                            <td>

                                <strong>

                                    <?php echo htmlspecialchars($room["room_number"]); ?>

                                </strong>

                            </td>



                            <!-- ROOM TYPE -->

                            <td>

                                <?php echo htmlspecialchars($room["room_name"]); ?>

                            </td>



                            <!-- PRICE -->

                            <td>

                                ₱<?php echo number_format($room["price"], 2); ?>

                            </td>



                            <!-- CAPACITY -->

                            <td>

                                <?php echo htmlspecialchars($room["capacity"]); ?>

                                guests

                            </td>



                            <!-- STATUS -->

                            <td>

                                <span class="booking-status">

                                    <?php echo htmlspecialchars($room["status"]); ?>

                                </span>

                            </td>



                            <!-- ACTION -->

                            <td>


                                <a
                                    href="edit-room.php?id=<?php echo $room["room_id"]; ?>"
                                    class="admin-action-button"
                                >

                                    EDIT

                                </a>



                                <form
                                    method="POST"
                                    action=""
                                    style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this room?');"
                                >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="room_id"
                                        value="<?php echo $room["room_id"]; ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="admin-delete-button"
                                    >

                                        DELETE

                                    </button>

                                </form>


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