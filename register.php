<?php

session_start();

require_once "config/Database.php";
require_once "classes/Customer.php";


// Connect to database
$database = new Database();

$db = $database->getConnection();


// Create Customer object
$customer = new Customer($db);


$error = "";

$success = "";


// Default form values
$first_name = "";

$last_name = "";

$email = "";

$phone = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    /* =====================================================
       GET FORM VALUES
       ===================================================== */

    $first_name = trim($_POST["first_name"] ?? "");

    $last_name = trim($_POST["last_name"] ?? "");

    $email = trim($_POST["email"] ?? "");

    $phone = trim($_POST["phone"] ?? "");

    $password = $_POST["password"] ?? "";

    $confirm_password = $_POST["confirm_password"] ?? "";


    /* =====================================================
       CHECK REQUIRED FIELDS
       ===================================================== */

    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all required fields.";

    }


    /* =====================================================
       EMAIL VALIDATION
       ===================================================== */

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }


    /* =====================================================
       PASSWORD MATCH
       ===================================================== */

    elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    }


    /* =====================================================
       PASSWORD LENGTH
       ===================================================== */

    elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    }


    /* =====================================================
       REGISTER CUSTOMER
       ===================================================== */

    else {

        if (
            $customer->register(
                $first_name,
                $last_name,
                $email,
                $password,
                $phone
            )
        ) {


            /* =============================================
               AUTOMATIC LOGIN
               ============================================= */

            $customerData = $customer->login(
                $email,
                $password
            );


            if ($customerData) {


                /* =========================================
                   REGENERATE SESSION ID
                   ========================================= */

                session_regenerate_id(true);


                /* =========================================
                   CREATE CUSTOMER SESSION
                   ========================================= */

                $_SESSION["customer_id"] =
                    $customerData["customer_id"];


                $_SESSION["customer_name"] =
                    $customerData["first_name"] . " " .
                    $customerData["last_name"];


                $_SESSION["customer_email"] =
                    $customerData["email"];


                /* =========================================
                   GO TO BOOKING PAGE
                   ========================================= */

                header("Location: book.php");

                exit();

            }


            else {

                $error =
                    "Registration successful, but automatic login failed.";

            }

        }


        else {

            $error =
                "Email already exists. Please use another email.";

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

    <title>Register - Bill's Resort</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css?v=5"
    >

</head>


<body>


<!-- =========================================================
     HEADER
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
     REGISTER
     ========================================================= -->

<div class="register-container">


    <h1>
        Create an Account
    </h1>


    <p>
        Register to book your stay at Bill's Resort.
    </p>



    <!-- =====================================================
         ERROR MESSAGE
         ===================================================== -->

    <?php if (!empty($error)): ?>

        <p class="form-error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </p>

    <?php endif; ?>



    <!-- =====================================================
         SUCCESS MESSAGE
         ===================================================== -->

    <?php if (!empty($success)): ?>

        <p class="form-success">

            <?php
            echo htmlspecialchars($success);
            ?>

        </p>

    <?php endif; ?>



    <!-- =====================================================
         REGISTER FORM
         ===================================================== -->

    <form
        method="POST"
        action=""
    >


        <!-- FIRST NAME -->

        <label for="first_name">
            First Name
        </label>

        <input
            type="text"
            name="first_name"
            id="first_name"
            value="<?php
                echo htmlspecialchars($first_name);
            ?>"
            required
        >



        <!-- LAST NAME -->

        <label for="last_name">
            Last Name
        </label>

        <input
            type="text"
            name="last_name"
            id="last_name"
            value="<?php
                echo htmlspecialchars($last_name);
            ?>"
            required
        >



        <!-- EMAIL -->

        <label for="email">
            Email
        </label>

        <input
            type="email"
            name="email"
            id="email"
            value="<?php
                echo htmlspecialchars($email);
            ?>"
            required
        >



        <!-- PHONE -->

        <label for="phone">
            Phone
        </label>

        <input
            type="text"
            name="phone"
            id="phone"
            value="<?php
                echo htmlspecialchars($phone);
            ?>"
        >



        <!-- PASSWORD -->

        <label for="password">
            Password
        </label>

        <input
            type="password"
            name="password"
            id="password"
            required
        >



        <!-- CONFIRM PASSWORD -->

        <label for="confirm_password">
            Confirm Password
        </label>

        <input
            type="password"
            name="confirm_password"
            id="confirm_password"
            required
        >



        <!-- SUBMIT -->

        <button type="submit">

            REGISTER

        </button>


    </form>



    <!-- =====================================================
         LOGIN LINK
         ===================================================== -->

    <p>

        Already have an account?

        <a href="login.php">
            Login here
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