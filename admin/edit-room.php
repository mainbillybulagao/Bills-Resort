<?php

session_start();

require_once "../config/Database.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$error = "";
$success = "";

$room_id = $_GET["id"] ?? "";

if (empty($room_id)) {
    header("Location: rooms.php");
    exit();
}


/* =========================================================
   GET ROOM INFORMATION
   ========================================================= */

$query = "SELECT * FROM rooms WHERE room_id = :room_id";

$stmt = $db->prepare($query);

$stmt->bindParam(":room_id", $room_id);

$stmt->execute();

$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    header("Location: rooms.php");
    exit();
}


/* =========================================================
   UPDATE ROOM
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $room_number = trim($_POST["room_number"] ?? "");
    $room_name = trim($_POST["room_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $capacity = $_POST["capacity"] ?? "";
    $image = trim($_POST["image"] ?? "");
    $status = $_POST["status"] ?? "Available";


    /* =====================================================
       VALIDATION
       ===================================================== */

    if (
        empty($room_number) ||
        empty($room_name) ||
        empty($price) ||
        empty($capacity)
    ) {

        $error = "Please complete all required fields.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $error = "Please enter a valid room price.";

    } elseif (!is_numeric($capacity) || $capacity <= 0) {

        $error = "Please enter a valid room capacity.";

    } elseif (
        $status !== "Available" &&
        $status !== "Unavailable"
    ) {

        $error = "Invalid room status.";

    } else {


        /* =================================================
           CHECK DUPLICATE ROOM NUMBER
           ================================================= */

        $query = "SELECT room_id
                  FROM rooms
                  WHERE room_number = :room_number
                  AND room_id != :room_id";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":room_number", $room_number);
        $stmt->bindParam(":room_id", $room_id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $error = "Room number already exists.";

        } else {


            /* =============================================
               UPDATE DATABASE
               ============================================= */

            $query = "UPDATE rooms
                      SET
                          room_number = :room_number,
                          room_name = :room_name,
                          description = :description,
                          price = :price,
                          capacity = :capacity,
                          image = :image,
                          status = :status
                      WHERE room_id = :room_id";

            $stmt = $db->prepare($query);

            $stmt->bindParam(":room_number", $room_number);
            $stmt->bindParam(":room_name", $room_name);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":price", $price);
            $stmt->bindParam(":capacity", $capacity);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":room_id", $room_id);

            if ($stmt->execute()) {

                $success = "Room updated successfully!";


                /* =========================================
                   UPDATE DISPLAYED DATA
                   ========================================= */

                $room["room_number"] = $room_number;
                $room["room_name"] = $room_name;
                $room["description"] = $description;
                $room["price"] = $price;
                $room["capacity"] = $capacity;
                $room["image"] = $image;
                $room["status"] = $status;

            } else {

                $error = "Something went wrong. Please try again.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Room | Bill's Resort</title>

    <link rel="stylesheet"
          href="../assets/css/admin.css">

</head>


<body>


<!-- =====================================================
     ADMIN HEADER
     ===================================================== -->

<header class="admin-header">

    <div class="admin-logo">

        <a href="dashboard.php">

            <img
                src="../assets/images/mainlogo.jpg"
                alt="Bill's Resort Logo"
            >

        </a>

    </div>


    <div class="admin-user">

        <span>
            Admin:
            <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>
        </span>

        <a
            href="logout.php"
            class="logout-btn"
        >
            LOGOUT
        </a>

    </div>

</header>



<!-- =====================================================
     ADMIN LAYOUT
     ===================================================== -->

<div class="admin-layout">


    <!-- =================================================
         LEFT ADMIN PANEL
         ================================================= -->

    <aside class="admin-sidebar">

        <h3>ADMIN PANEL</h3>


        <a href="dashboard.php">
            DASHBOARD
        </a>


        <a href="bookings.php">
            BOOKINGS
        </a>


        <a href="rooms.php"
           class="active">
            ROOMS
        </a>


        <a href="messages.php">
            MESSAGES
        </a>


        <a href="reviews.php">
            REVIEWS
        </a>


        <a href="../index.php"
           target="_blank">
            VIEW WEBSITE
        </a>


        <div class="admin-logout">

            <a href="logout.php">
                LOGOUT
            </a>

        </div>

    </aside>



    <!-- =================================================
         RIGHT CONTENT
         ================================================= -->

    <main class="admin-content">


        <h1>EDIT ROOM</h1>

        <p>
            Update the information for this room.
        </p>


        <!-- =============================================
             SUCCESS MESSAGE
             ============================================= -->

        <?php if (!empty($success)): ?>

            <div class="admin-alert success">

                <?php echo htmlspecialchars($success); ?>

            </div>

        <?php endif; ?>


        <!-- =============================================
             ERROR MESSAGE
             ============================================= -->

        <?php if (!empty($error)): ?>

            <div class="admin-alert error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>



        <!-- =============================================
             EDIT ROOM FORM
             ============================================= -->

        <div class="admin-form-container">

            <form method="POST">


                <!-- ROOM NUMBER -->

                <div class="admin-form-group">

                    <label for="room_number">
                        Room Number *
                    </label>

                    <input
                        type="text"
                        id="room_number"
                        name="room_number"
                        value="<?php echo htmlspecialchars($room["room_number"]); ?>"
                        required
                    >

                </div>



                <!-- ROOM TYPE -->

                <div class="admin-form-group">

                    <label for="room_name">
                        Room Type *
                    </label>

                    <input
                        type="text"
                        id="room_name"
                        name="room_name"
                        value="<?php echo htmlspecialchars($room["room_name"]); ?>"
                        required
                    >

                </div>



                <!-- DESCRIPTION -->

                <div class="admin-form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                    ><?php echo htmlspecialchars($room["description"]); ?></textarea>

                </div>



                <!-- PRICE -->

                <div class="admin-form-group">

                    <label for="price">
                        Price Per Night *
                    </label>

                    <input
                        type="number"
                        id="price"
                        name="price"
                        step="0.01"
                        min="0"
                        value="<?php echo htmlspecialchars($room["price"]); ?>"
                        required
                    >

                </div>



                <!-- CAPACITY -->

                <div class="admin-form-group">

                    <label for="capacity">
                        Guest Capacity *
                    </label>

                    <input
                        type="number"
                        id="capacity"
                        name="capacity"
                        min="1"
                        value="<?php echo htmlspecialchars($room["capacity"]); ?>"
                        required
                    >

                </div>



                <!-- IMAGE -->

                <div class="admin-form-group">

                    <label for="image">
                        Image Filename
                    </label>

                    <input
                        type="text"
                        id="image"
                        name="image"
                        value="<?php echo htmlspecialchars($room["image"]); ?>"
                        placeholder="example: room1.jpg"
                    >

                </div>



                <!-- IMAGE PREVIEW -->

                <?php if (!empty($room["image"])): ?>

                    <div class="room-image-preview">

                        <img
                            src="../assets/images/<?php echo htmlspecialchars($room["image"]); ?>"
                            alt="Room Image"
                            onerror="this.style.display='none';"
                        >

                    </div>

                <?php endif; ?>



                <!-- STATUS -->

                <div class="admin-form-group">

                    <label for="status">
                        Room Status *
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option
                            value="Available"
                            <?php
                            if ($room["status"] == "Available") {
                                echo "selected";
                            }
                            ?>
                        >
                            Available
                        </option>

                        <option
                            value="Unavailable"
                            <?php
                            if ($room["status"] == "Unavailable") {
                                echo "selected";
                            }
                            ?>
                        >
                            Unavailable
                        </option>

                    </select>

                </div>



                <!-- BUTTONS -->

                <div class="form-buttons">

                    <button
                        type="submit"
                        class="admin-btn"
                    >
                        UPDATE ROOM
                    </button>


                    <a
                        href="rooms.php"
                        class="admin-btn secondary"
                    >
                        CANCEL
                    </a>

                </div>

            </form>

        </div>

    </main>

</div>


</body>
</html>