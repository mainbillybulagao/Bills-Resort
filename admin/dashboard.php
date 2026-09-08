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
   GET DASHBOARD COUNTS
========================================= */

// Total Customers
$query = "SELECT COUNT(*) AS total FROM customers";

$stmt = $db->prepare($query);
$stmt->execute();

$total_customers = $stmt->fetch(PDO::FETCH_ASSOC)["total"];


// Total Rooms
$query = "SELECT COUNT(*) AS total FROM rooms";

$stmt = $db->prepare($query);
$stmt->execute();

$total_rooms = $stmt->fetch(PDO::FETCH_ASSOC)["total"];


// Total Bookings
$query = "SELECT COUNT(*) AS total FROM bookings";

$stmt = $db->prepare($query);
$stmt->execute();

$total_bookings = $stmt->fetch(PDO::FETCH_ASSOC)["total"];


// Pending Bookings
$query = "SELECT COUNT(*) AS total
          FROM bookings
          WHERE status = 'Pending'";

$stmt = $db->prepare($query);
$stmt->execute();

$pending_bookings = $stmt->fetch(PDO::FETCH_ASSOC)["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Bill's Resort</title>

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


    <!-- ADMIN NAME -->

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


    <a href="dashboard.php" class="active">
        DASHBOARD
    </a>


    <a href="bookings.php">
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
        DASHBOARD
    </h1>


    <p class="admin-welcome">

        Welcome to Bill's Resort Administration.

    </p>



    <!-- =====================================================
         DASHBOARD CARDS
         ===================================================== -->

    <div class="dashboard-cards">


        <!-- CUSTOMERS -->

        <div class="dashboard-card">

            <h3>
                CUSTOMERS
            </h3>

            <p>
                <?php echo $total_customers; ?>
            </p>

        </div>



        <!-- ROOMS -->

        <div class="dashboard-card">

            <h3>
                ROOMS
            </h3>

            <p>
                <?php echo $total_rooms; ?>
            </p>

        </div>



        <!-- BOOKINGS -->

        <div class="dashboard-card">

            <h3>
                TOTAL BOOKINGS
            </h3>

            <p>
                <?php echo $total_bookings; ?>
            </p>

        </div>



        <!-- PENDING BOOKINGS -->

        <div class="dashboard-card">

            <h3>
                PENDING BOOKINGS
            </h3>

            <p>
                <?php echo $pending_bookings; ?>
            </p>

        </div>


    </div>



    <!-- =====================================================
         QUICK ACTIONS
         ===================================================== -->

    <div class="admin-section">


        <h2>
            QUICK ACTIONS
        </h2>


        <div class="quick-actions">


            <a href="bookings.php">
                VIEW BOOKINGS
            </a>


            <a href="rooms.php">
                MANAGE ROOMS
            </a>


            <a href="../index.php">
                VIEW WEBSITE
            </a>


        </div>


    </div>


</main>



</body>

</html>