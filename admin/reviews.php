<?php

session_start();

require_once "../config/Database.php";


// =========================================================
// CHECK ADMIN LOGIN
// =========================================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit();

}


// =========================================================
// DATABASE CONNECTION
// =========================================================

$database = new Database();
$db = $database->getConnection();


// =========================================================
// SUCCESS / ERROR MESSAGES
// =========================================================

$success = "";
$error = "";


// =========================================================
// APPROVE REVIEW
// =========================================================

if (isset($_GET["approve"])) {

    $review_id = intval($_GET["approve"]);

    try {

        $query = "UPDATE reviews
                  SET status = 'Approved'
                  WHERE review_id = :review_id";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":review_id", $review_id);

        if ($stmt->execute()) {

            $success = "Review approved successfully.";

        } else {

            $error = "Unable to approve the review.";

        }

    } catch (PDOException $e) {

        $error = "Something went wrong.";

    }

}


// =========================================================
// REJECT REVIEW
// =========================================================

if (isset($_GET["reject"])) {

    $review_id = intval($_GET["reject"]);

    try {

        $query = "UPDATE reviews
                  SET status = 'Rejected'
                  WHERE review_id = :review_id";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":review_id", $review_id);

        if ($stmt->execute()) {

            $success = "Review rejected successfully.";

        } else {

            $error = "Unable to reject the review.";

        }

    } catch (PDOException $e) {

        $error = "Something went wrong.";

    }

}


// =========================================================
// DELETE REVIEW
// =========================================================

if (isset($_GET["delete"])) {

    $review_id = intval($_GET["delete"]);

    try {

        $query = "DELETE FROM reviews
                  WHERE review_id = :review_id";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":review_id", $review_id);

        if ($stmt->execute()) {

            $success = "Review deleted successfully.";

        } else {

            $error = "Unable to delete the review.";

        }

    } catch (PDOException $e) {

        $error = "Something went wrong.";

    }

}


// =========================================================
// GET ALL REVIEWS
// =========================================================

try {

    $query = "SELECT
                reviews.review_id,
                reviews.rating,
                reviews.comment,
                reviews.status,
                reviews.created_at,
                customers.first_name,
                customers.last_name,
                customers.email
              FROM reviews
              INNER JOIN customers
                  ON reviews.customer_id = customers.customer_id
              ORDER BY reviews.created_at DESC";

    $stmt = $db->prepare($query);

    $stmt->execute();

    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $reviews = [];

    $error = "Unable to load reviews.";

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reviews - Admin | Bill's Resort</title>


    <!-- ADMIN CSS -->

    <link rel="stylesheet" href="../assets/css/admin.css">


</head>


<body>


<!-- =========================================================
     ADMIN HEADER
     ========================================================= -->

<header class="admin-header">


    <div class="admin-logo">

        <img
            src="../assets/images/mainlogo.jpg"
            alt="Bill's Resort Logo"
        >

    </div>


    <div class="admin-title">

        <h1>
            BILL'S RESORT
        </h1>

        <p>
            ADMIN PANEL
        </p>

    </div>


    <div class="admin-user">

        <span>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>
        </span>

    </div>


</header>



<!-- =========================================================
     ADMIN LAYOUT
     ========================================================= -->

<div class="admin-layout">


    <!-- =====================================================
         SIDEBAR
         ===================================================== -->

    <aside class="admin-sidebar">


        <div class="sidebar-title">

            ADMIN MENU

        </div>


        <nav class="sidebar-nav">


            <!-- DASHBOARD -->

            <a href="dashboard.php">

                <span class="nav-icon">
                    ▣
                </span>

                DASHBOARD

            </a>


            <!-- BOOKINGS -->

            <a href="bookings.php">

                <span class="nav-icon">
                    ▤
                </span>

                BOOKINGS

            </a>


            <!-- ROOMS -->

            <a href="rooms.php">

                <span class="nav-icon">
                    ▦
                </span>

                ROOMS

            </a>


            <!-- MESSAGES -->

            <a href="messages.php">

                <span class="nav-icon">
                    ✉
                </span>

                MESSAGES

            </a>


            <!-- REVIEWS -->

            <a href="reviews.php" class="active">

                <span class="nav-icon">
                    ★
                </span>

                REVIEWS

            </a>


            <!-- VIEW WEBSITE -->

            <a href="../index.php">

                <span class="nav-icon">
                    ◉
                </span>

                VIEW WEBSITE

            </a>


            <!-- LOGOUT -->

            <a href="logout.php">

                <span class="nav-icon">
                    ⇥
                </span>

                LOGOUT

            </a>


        </nav>


    </aside>



    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->

    <main class="admin-content">


        <!-- PAGE HEADER -->

        <div class="page-header">

            <div>

                <h2>
                    CUSTOMER REVIEWS
                </h2>

                <p>
                    Manage customer reviews submitted through the website.
                </p>

            </div>

        </div>



        <!-- =================================================
             SUCCESS MESSAGE
             ================================================= -->

        <?php if ($success != ""): ?>

            <div class="alert success">

                <?php echo htmlspecialchars($success); ?>

            </div>

        <?php endif; ?>



        <!-- =================================================
             ERROR MESSAGE
             ================================================= -->

        <?php if ($error != ""): ?>

            <div class="alert error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>



        <!-- =================================================
             REVIEWS SECTION
             ================================================= -->

        <section class="admin-section">


            <div class="section-header">

                <h3>
                    REVIEWS
                </h3>

            </div>



            <?php if (count($reviews) > 0): ?>


                <div class="table-container">


                    <table class="admin-table">


                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    CUSTOMER
                                </th>

                                <th>
                                    EMAIL
                                </th>

                                <th>
                                    RATING
                                </th>

                                <th>
                                    COMMENT
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


                            <?php foreach ($reviews as $review): ?>


                                <tr>


                                    <!-- REVIEW ID -->

                                    <td>

                                        <?php echo $review["review_id"]; ?>

                                    </td>



                                    <!-- CUSTOMER -->

                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $review["first_name"] .
                                            " " .
                                            $review["last_name"]
                                        );

                                        ?>

                                    </td>



                                    <!-- EMAIL -->

                                    <td>

                                        <?php echo htmlspecialchars($review["email"]); ?>

                                    </td>



                                    <!-- RATING -->

                                    <td>

                                        <span class="review-stars">

                                            <?php

                                            for ($i = 1; $i <= 5; $i++) {

                                                if ($i <= $review["rating"]) {

                                                    echo "★";

                                                } else {

                                                    echo "☆";

                                                }

                                            }

                                            ?>

                                        </span>

                                        <br>

                                        <?php echo $review["rating"]; ?>/5

                                    </td>



                                    <!-- COMMENT -->

                                    <td>

                                        <div class="review-comment">

                                            <?php

                                            echo nl2br(
                                                htmlspecialchars($review["comment"])
                                            );

                                            ?>

                                        </div>

                                    </td>



                                    <!-- STATUS -->

                                    <td>


                                        <?php if ($review["status"] === "Approved"): ?>

                                            <span class="status confirmed">
                                                Approved
                                            </span>


                                        <?php elseif ($review["status"] === "Rejected"): ?>

                                            <span class="status cancelled">
                                                Rejected
                                            </span>


                                        <?php else: ?>

                                            <span class="status pending">
                                                Pending
                                            </span>

                                        <?php endif; ?>


                                    </td>



                                    <!-- DATE -->

                                    <td>

                                        <?php

                                        echo date(
                                            "M d, Y",
                                            strtotime($review["created_at"])
                                        );

                                        ?>

                                        <br>

                                        <small>

                                            <?php

                                            echo date(
                                                "h:i A",
                                                strtotime($review["created_at"])
                                            );

                                            ?>

                                        </small>

                                    </td>



                                    <!-- ACTIONS -->

                                    <td>


                                        <div class="action-buttons">


                                            <?php if ($review["status"] !== "Approved"): ?>

                                                <a
                                                    href="reviews.php?approve=<?php echo $review["review_id"]; ?>"
                                                    class="action-btn confirm"
                                                    onclick="return confirm('Are you sure you want to approve this review?');"
                                                >
                                                    APPROVE
                                                </a>

                                            <?php endif; ?>



                                            <?php if ($review["status"] !== "Rejected"): ?>

                                                <a
                                                    href="reviews.php?reject=<?php echo $review["review_id"]; ?>"
                                                    class="action-btn cancel"
                                                    onclick="return confirm('Are you sure you want to reject this review?');"
                                                >
                                                    REJECT
                                                </a>

                                            <?php endif; ?>



                                            <a
                                                href="reviews.php?delete=<?php echo $review["review_id"]; ?>"
                                                class="action-btn delete"
                                                onclick="return confirm('Are you sure you want to permanently delete this review?');"
                                            >
                                                DELETE
                                            </a>


                                        </div>


                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php else: ?>


                <!-- NO REVIEWS -->

                <div class="no-data">

                    <div class="no-data-icon">
                        ★
                    </div>

                    <h3>
                        No Reviews Yet
                    </h3>

                    <p>
                        Customer reviews will appear here after they are submitted.
                    </p>

                </div>


            <?php endif; ?>


        </section>


    </main>


</div>


</body>

</html>