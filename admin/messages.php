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

$error = "";
$success = "";


/* =========================================================
   MARK MESSAGE AS READ
   ========================================================= */

if (isset($_GET["read"])) {

    $message_id = $_GET["read"];

    $query = "UPDATE messages
              SET status = 'Read'
              WHERE message_id = :message_id";

    $stmt = $db->prepare($query);

    $stmt->bindParam(":message_id", $message_id);

    if ($stmt->execute()) {
        $success = "Message marked as read.";
    } else {
        $error = "Unable to update message.";
    }
}


/* =========================================================
   DELETE MESSAGE
   ========================================================= */

if (isset($_GET["delete"])) {

    $message_id = $_GET["delete"];

    $query = "DELETE FROM messages
              WHERE message_id = :message_id";

    $stmt = $db->prepare($query);

    $stmt->bindParam(":message_id", $message_id);

    if ($stmt->execute()) {
        $success = "Message deleted successfully.";
    } else {
        $error = "Unable to delete message.";
    }
}


/* =========================================================
   GET ALL MESSAGES
   ========================================================= */

$query = "SELECT *
          FROM messages
          ORDER BY created_at DESC";

$stmt = $db->prepare($query);

$stmt->execute();

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Messages | Bill's Resort</title>

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


        <a href="rooms.php">
            ROOMS
        </a>


        <a href="messages.php"
           class="active">
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


        <h1>MESSAGES</h1>

        <p>
            View and manage messages submitted through the contact form.
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
             MESSAGE TABLE
             ============================================= -->

        <div class="admin-section">


            <div class="admin-section-header">

                <h2>
                    CUSTOMER MESSAGES
                </h2>

            </div>


            <div class="admin-table-container">

                <?php if (empty($messages)): ?>

                    <div class="no-data">

                        <h2>No Messages</h2>

                        <p>
                            There are currently no messages.
                        </p>

                    </div>

                <?php else: ?>


                    <table class="admin-table">


                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    NAME
                                </th>

                                <th>
                                    EMAIL
                                </th>

                                <th>
                                    PHONE
                                </th>

                                <th>
                                    MESSAGE
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    DATE
                                </th>

                                <th>
                                    ACTIONS
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach ($messages as $message): ?>


                            <tr>


                                <!-- MESSAGE ID -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $message["message_id"]
                                    );
                                    ?>

                                </td>


                                <!-- NAME -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $message["name"]
                                    );
                                    ?>

                                </td>


                                <!-- EMAIL -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $message["email"]
                                    );
                                    ?>

                                </td>


                                <!-- PHONE -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $message["phone"]
                                    );
                                    ?>

                                </td>


                                <!-- MESSAGE -->

                                <td>

                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $message["message"]
                                        )
                                    );
                                    ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <?php if ($message["status"] == "Unread"): ?>

                                        <span class="status pending">
                                            Unread
                                        </span>

                                    <?php else: ?>

                                        <span class="status confirmed">
                                            Read
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- DATE -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $message["created_at"]
                                    );
                                    ?>

                                </td>


                                <!-- ACTIONS -->

                                <td>

                                    <div class="action-buttons">


                                        <?php if ($message["status"] == "Unread"): ?>

                                            <a
                                                href="messages.php?read=<?php echo $message["message_id"]; ?>"
                                                class="action-btn confirm"
                                            >
                                                MARK READ
                                            </a>

                                        <?php endif; ?>


                                        <a
                                            href="messages.php?delete=<?php echo $message["message_id"]; ?>"
                                            class="action-btn delete"
                                            onclick="return confirm('Are you sure you want to delete this message?');"
                                        >
                                            DELETE
                                        </a>


                                    </div>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>


                <?php endif; ?>

            </div>

        </div>


    </main>

</div>


</body>
</html>