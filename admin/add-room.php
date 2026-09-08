<?php

session_start();

require_once "../config/Database.php";


/* =========================================================
   CHECK ADMIN LOGIN
   ========================================================= */

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit();

}


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

$database = new Database();

$db = $database->getConnection();


/* =========================================================
   VARIABLES
   ========================================================= */

$room_number = "";
$room_name = "";
$description = "";
$price = "";
$capacity = "";
$image = "";
$status = "Available";

$success = "";
$error = "";


/* =========================================================
   ADD ROOM
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $room_number = trim($_POST["room_number"] ?? "");
    $room_name = trim($_POST["room_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $capacity = trim($_POST["capacity"] ?? "");
    $image = trim($_POST["image"] ?? "");
    $status = $_POST["status"] ?? "Available";


    /* =====================================================
       VALIDATION
       ===================================================== */

    if (
        $room_number === "" ||
        $room_name === "" ||
        $description === "" ||
        $price === "" ||
        $capacity === ""
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!is_numeric($room_number)) {

        $error = "Room number must contain numbers only.";

    } elseif (!is_numeric($price) || $price < 0) {

        $error = "Please enter a valid price.";

    } elseif (!is_numeric($capacity) || $capacity <= 0) {

        $error = "Please enter a valid guest capacity.";

    } elseif (!in_array($status, ["Available", "Unavailable"], true)) {

        $error = "Invalid room status.";

    } else {


        /* =================================================
           CHECK DUPLICATE ROOM NUMBER
           ================================================= */

        $query = "SELECT room_id
                  FROM rooms
                  WHERE room_number = :room_number";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":room_number", $room_number);

        $stmt->execute();


        if ($stmt->rowCount() > 0) {

            $error = "Room number already exists.";

        } else {


            /* =============================================
               INSERT ROOM
               ============================================= */

            try {

                $query = "INSERT INTO rooms
                          (
                              room_number,
                              room_name,
                              description,
                              price,
                              capacity,
                              image,
                              status
                          )
                          VALUES
                          (
                              :room_number,
                              :room_name,
                              :description,
                              :price,
                              :capacity,
                              :image,
                              :status
                          )";

                $stmt = $db->prepare($query);

                $stmt->bindParam(":room_number", $room_number);
                $stmt->bindParam(":room_name", $room_name);
                $stmt->bindParam(":description", $description);
                $stmt->bindParam(":price", $price);
                $stmt->bindParam(":capacity", $capacity);
                $stmt->bindParam(":image", $image);
                $stmt->bindParam(":status", $status);


                if ($stmt->execute()) {

                    $success = "Room added successfully.";

                    $room_number = "";
                    $room_name = "";
                    $description = "";
                    $price = "";
                    $capacity = "";
                    $image = "";
                    $status = "Available";

                } else {

                    $error = "Unable to add the room. Please try again.";

                }

            } catch (PDOException $e) {

                error_log($e->getMessage());

                $error = "Unable to add the room right now.";

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

    <title>Add Room | Bill's Resort</title>

    <link
        rel="stylesheet"
        href="../assets/css/admin.css?v=2"
    >


    <style>

        /* =====================================================
           ADD ROOM PAGE FIX
           ===================================================== */

        .admin-sidebar h2 {
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 25px;
            padding-left: 5px;
        }


        /* =====================================================
           ADMIN HEADER LOGOUT
           ===================================================== */

        .admin-header-right {
            display: flex;
            align-items: center;
        }

        .admin-header-right a {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 10px 20px;

            background-color: #17606b;
            color: #ffffff;

            border-radius: 8px;

            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 600;

            transition: 0.3s ease;
        }

        .admin-header-right a:hover {
            background-color: #124d57;
        }


        /* =====================================================
           PAGE HEADER
           ===================================================== */

        .admin-page-header {
            margin-bottom: 30px;
        }

        .admin-page-header h1 {
            color: #17606b;
            font-family: 'Lora', serif;
            font-size: 38px;
            margin-bottom: 10px;
        }

        .admin-page-header p {
            color: #555555;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
        }


        /* =====================================================
           ADMIN SECTION
           ===================================================== */

        .admin-section {
            width: 100%;
            max-width: 950px;

            background-color: #ffffff;

            padding: 40px;

            border-radius: 18px;

            margin-top: 20px;

            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }


        /* =====================================================
           ROOM FORM
           ===================================================== */

        .room-form {
            width: 100%;

            display: block;
        }


        /* =====================================================
           FORM GROUP
           ===================================================== */

        .room-form-group {
            width: 100%;

            margin-bottom: 22px;
        }


        /* =====================================================
           LABEL
           ===================================================== */

        .room-form-group label {
            display: block;

            margin-bottom: 8px;

            color: #111111;

            font-family: 'Montserrat', sans-serif;

            font-size: 14px;

            font-weight: 600;
        }


        /* =====================================================
           INPUTS
           ===================================================== */

        .room-form-group input,
        .room-form-group select,
        .room-form-group textarea {

            display: block;

            width: 100%;

            min-height: 48px;

            padding: 13px 15px;

            border: 1px solid #b5c9cc;

            border-radius: 8px;

            background-color: #ffffff;

            color: #111111;

            font-family: 'Montserrat', sans-serif;

            font-size: 14px;

            outline: none;

            box-sizing: border-box;

            transition: 0.3s ease;
        }


        /* =====================================================
           INPUT FOCUS
           ===================================================== */

        .room-form-group input:focus,
        .room-form-group select:focus,
        .room-form-group textarea:focus {

            border-color: #17606b;

            box-shadow:
                0 0 0 3px rgba(23, 96, 107, 0.10);

        }


        /* =====================================================
           TEXTAREA
           ===================================================== */

        .room-form-group textarea {

            min-height: 140px;

            resize: vertical;

            line-height: 1.5;
        }


        /* =====================================================
           HELP TEXT
           ===================================================== */

        .room-form-group small {

            display: block;

            margin-top: 6px;

            color: #666666;

            font-family: 'Montserrat', sans-serif;

            font-size: 12px;

            line-height: 1.5;
        }


        /* =====================================================
           BUTTONS
           ===================================================== */

        .room-form-actions {

            display: flex;

            align-items: center;

            gap: 12px;

            margin-top: 5px;
        }


        .room-form-actions button,
        .room-form-actions a {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-width: 145px;

            min-height: 48px;

            padding: 12px 22px;

            border: none;

            border-radius: 9px;

            font-family: 'Montserrat', sans-serif;

            font-size: 14px;

            font-weight: 600;

            text-decoration: none;

            cursor: pointer;

            box-sizing: border-box;

            transition: 0.3s ease;
        }


        /* =====================================================
           ADD BUTTON
           ===================================================== */

        .room-form-actions .add-room-btn {

            background-color: #17606b;

            color: #ffffff;
        }


        .room-form-actions .add-room-btn:hover {

            background-color: #124d57;

            transform: translateY(-2px);
        }


        /* =====================================================
           CANCEL BUTTON
           ===================================================== */

        .room-form-actions .cancel-room-btn {

            background-color: #b5c9cc;

            color: #111111;
        }


        .room-form-actions .cancel-room-btn:hover {

            background-color: #9db5ba;

            transform: translateY(-2px);
        }


        /* =====================================================
           ALERT
           ===================================================== */

        .room-alert {

            width: 100%;

            padding: 14px 18px;

            margin-bottom: 20px;

            border-radius: 8px;

            font-family: 'Montserrat', sans-serif;

            font-size: 14px;

            box-sizing: border-box;
        }


        .room-alert.success {

            background-color: #d9f0df;

            color: #24723b;

            border: 1px solid #b8ddc1;
        }


        .room-alert.error {

            background-color: #f5dddd;

            color: #9b3333;

            border: 1px solid #e4baba;
        }


        /* =====================================================
           MOBILE
           ===================================================== */

        @media (max-width: 700px) {

            .admin-section {
                padding: 25px;
            }

            .room-form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .room-form-actions button,
            .room-form-actions a {
                width: 100%;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     ADMIN HEADER
     ========================================================= -->

<header class="admin-header">


    <div class="admin-logo">

        <a href="dashboard.php">

            <img
                src="../assets/images/mainlogo.jpg"
                alt="Bill's Resort Logo"
            >

        </a>

    </div>


    <div class="admin-header-right">

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


    <a href="../index.php" target="_blank">
        VIEW WEBSITE
    </a>

</aside>


<!-- =========================================================
     MAIN CONTENT
     ========================================================= -->

<main class="admin-content">


    <!-- PAGE HEADER -->

    <div class="admin-page-header">

        <h1>
            ADD ROOM
        </h1>

        <p>
            Add a new physical room to Bill's Resort.
        </p>

    </div>


    <!-- =====================================================
         SUCCESS MESSAGE
         ===================================================== -->

    <?php if (!empty($success)): ?>

        <div class="room-alert success">

            <?php echo htmlspecialchars($success); ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         ERROR MESSAGE
         ===================================================== -->

    <?php if (!empty($error)): ?>

        <div class="room-alert error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         FORM
         ===================================================== -->

    <section class="admin-section">


        <form
            method="POST"
            action=""
            class="room-form"
        >


            <!-- ROOM NUMBER -->

            <div class="room-form-group">

                <label for="room_number">
                    Room Number *
                </label>

                <input
                    type="text"
                    id="room_number"
                    name="room_number"
                    value="<?php echo htmlspecialchars($room_number); ?>"
                    placeholder="Example: 301"
                    required
                >

            </div>


            <!-- ROOM TYPE -->

            <div class="room-form-group">

                <label for="room_name">
                    Room Type *
                </label>

                <input
                    type="text"
                    id="room_name"
                    name="room_name"
                    value="<?php echo htmlspecialchars($room_name); ?>"
                    placeholder="Example: Deluxe Room"
                    required
                >

            </div>


            <!-- DESCRIPTION -->

            <div class="room-form-group">

                <label for="description">
                    Description *
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Enter room description"
                    rows="5"
                    required
                ><?php echo htmlspecialchars($description); ?></textarea>

            </div>


            <!-- PRICE -->

            <div class="room-form-group">

                <label for="price">
                    Price Per Night *
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    value="<?php echo htmlspecialchars($price); ?>"
                    placeholder="Example: 2500"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <!-- CAPACITY -->

            <div class="room-form-group">

                <label for="capacity">
                    Guest Capacity *
                </label>

                <input
                    type="number"
                    id="capacity"
                    name="capacity"
                    value="<?php echo htmlspecialchars($capacity); ?>"
                    placeholder="Example: 4"
                    min="1"
                    required
                >

            </div>


            <!-- IMAGE -->

            <div class="room-form-group">

                <label for="image">
                    Image Filename
                </label>

                <input
                    type="text"
                    id="image"
                    name="image"
                    value="<?php echo htmlspecialchars($image); ?>"
                    placeholder="Example: room1.jpg"
                >

                <small>
                    Put the image inside
                    <strong>assets/images/</strong>
                </small>

            </div>


            <!-- STATUS -->

            <div class="room-form-group">

                <label for="status">
                    Room Status
                </label>

                <select
                    id="status"
                    name="status"
                >

                    <option
                        value="Available"
                        <?php echo ($status === "Available") ? "selected" : ""; ?>
                    >
                        Available
                    </option>

                    <option
                        value="Unavailable"
                        <?php echo ($status === "Unavailable") ? "selected" : ""; ?>
                    >
                        Unavailable
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->

            <div class="room-form-actions">

                <button
                    type="submit"
                    class="add-room-btn"
                >
                    ADD ROOM
                </button>


                <a
                    href="rooms.php"
                    class="cancel-room-btn"
                >
                    CANCEL
                </a>

            </div>


        </form>


    </section>


</main>


</body>

</html>