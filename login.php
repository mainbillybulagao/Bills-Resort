<?php

session_start();

require_once "config/Database.php";
require_once "classes/Customer.php";


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

$database = new Database();

$db = $database->getConnection();


/* =========================================================
   CUSTOMER OBJECT
   ========================================================= */

$customer = new Customer($db);


/* =========================================================
   ERROR MESSAGE
   ========================================================= */

$error = "";


/* =========================================================
   LOGIN FORM
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* =====================================================
       GET FORM VALUES SAFELY
       ===================================================== */

    $email = trim($_POST["email"] ?? "");

    $password = $_POST["password"] ?? "";


    /* =====================================================
       VALIDATION
       ===================================================== */

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {


        /* =================================================
           CHECK LOGIN
           ================================================= */

        $loggedInCustomer = $customer->login(
            $email,
            $password
        );


        if ($loggedInCustomer) {


            /* =============================================
               REGENERATE SESSION ID
               ============================================= */

            session_regenerate_id(true);


            /* =============================================
               STORE CUSTOMER INFORMATION
               ============================================= */

            $_SESSION["customer_id"] =
                $loggedInCustomer["customer_id"];


            $_SESSION["customer_name"] =
                $loggedInCustomer["first_name"] . " " .
                $loggedInCustomer["last_name"];


            $_SESSION["customer_email"] =
                $loggedInCustomer["email"];


            /* =============================================
               GO TO MY BOOKINGS
               ============================================= */

            header("Location: my-bookings.php");

            exit();


        } else {

            $error = "Invalid email or password.";

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

    <title>Login - Bill's Resort</title>


    <!-- =====================================================
         CSS
         ===================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/style.css?v=4"
    >

</head>


<body>


<!-- =========================================================
     LOGO ABOVE LOGIN BOX
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
     LOGIN BOX
     ========================================================= -->

<div class="register-container">


    <h1>
        Welcome Back
    </h1>


    <p>
        Login to view your bookings.
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
         LOGIN FORM
         ===================================================== -->

    <form
        method="POST"
        action=""
    >


        <!-- EMAIL -->

        <label for="email">
            Email
        </label>


        <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($email ?? ""); ?>"
            required
        >


        <!-- PASSWORD -->

        <label for="password">
            Password
        </label>


        <input
            type="password"
            id="password"
            name="password"
            required
        >


        <!-- LOGIN BUTTON -->

        <button type="submit">
            LOGIN
        </button>


    </form>


    <!-- =====================================================
         REGISTER LINK
         ===================================================== -->

    <p>

        Don't have an account?

        <a href="register.php">
            Register here
        </a>

    </p>


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