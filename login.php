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
   FORM VALUE
   ========================================================= */

$login_value = "";


/* =========================================================
   LOGIN
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* =====================================================
       GET FORM VALUES SAFELY
       ===================================================== */

    $login_value = trim($_POST["login"] ?? "");

    $password = $_POST["password"] ?? "";


    /* =====================================================
       VALIDATION
       ===================================================== */

    if ($login_value === "" || $password === "") {

        $error = "Please enter your email or username and password.";

    }


    /* =====================================================
       CHECK CUSTOMER LOGIN
       ===================================================== */

    else {

        /*
         * First check if the credentials belong
         * to a customer.
         */

        if (filter_var($login_value, FILTER_VALIDATE_EMAIL)) {

            $loggedInCustomer = $customer->login(
                $login_value,
                $password
            );

        } else {

            $loggedInCustomer = false;

        }


        /* =================================================
           CUSTOMER LOGIN SUCCESS
           ================================================= */

        if ($loggedInCustomer) {


            /* =============================================
               REGENERATE SESSION ID
               ============================================= */

            session_regenerate_id(true);


            /* =============================================
               REMOVE OLD ADMIN SESSION
               ============================================= */

            unset($_SESSION["admin_id"]);
            unset($_SESSION["admin_username"]);


            /* =============================================
               CREATE CUSTOMER SESSION
               ============================================= */

            $_SESSION["customer_id"] =
                $loggedInCustomer["customer_id"];


            $_SESSION["customer_name"] =
                $loggedInCustomer["first_name"] . " " .
                $loggedInCustomer["last_name"];


            $_SESSION["customer_email"] =
                $loggedInCustomer["email"];


            /* =============================================
               GO TO CUSTOMER PAGE
               ============================================= */

            header("Location: my-bookings.php");

            exit();

        }


        /* =================================================
           CHECK ADMIN LOGIN
           ================================================= */

        else {

            $query = "SELECT *
                      FROM admins
                      WHERE username = :username
                      LIMIT 1";

            $stmt = $db->prepare($query);

            $stmt->bindParam(
                ":username",
                $login_value
            );

            $stmt->execute();

            $admin = $stmt->fetch(PDO::FETCH_ASSOC);


            /* =============================================
               ADMIN LOGIN SUCCESS
               ============================================= */

            if (
                $admin &&
                password_verify(
                    $password,
                    $admin["password"]
                )
            ) {


                /* =========================================
                   REGENERATE SESSION ID
                   ========================================= */

                session_regenerate_id(true);


                /* =========================================
                   REMOVE OLD CUSTOMER SESSION
                   ========================================= */

                unset($_SESSION["customer_id"]);
                unset($_SESSION["customer_name"]);
                unset($_SESSION["customer_email"]);


                /* =========================================
                   CREATE ADMIN SESSION
                   ========================================= */

                $_SESSION["admin_id"] =
                    $admin["admin_id"];


                $_SESSION["admin_username"] =
                    $admin["username"];


                /* =========================================
                   GO TO ADMIN DASHBOARD
                   ========================================= */

                header("Location: admin/dashboard.php");

                exit();

            }


            /* =============================================
               INVALID LOGIN
               ============================================= */

            else {

                $error =
                    "Invalid email/username or password.";

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

    <title>Login - Bill's Resort</title>


    <!-- =====================================================
         CSS
         ===================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/style.css?v=5"
    >

</head>


<body>


<!-- =========================================================
     LOGIN BOX
     ========================================================= -->

<div class="register-container">


    <h1>
        LOGIN
    </h1>


    <p>
        Sign in to continue to Bill's Resort.
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
         LOGIN FORM
         ===================================================== -->

    <form
        method="POST"
        action=""
    >


        <!-- =================================================
             EMAIL / USERNAME
             ================================================= -->

        <label for="login">
            Email / Username
        </label>


        <input
            type="text"
            id="login"
            name="login"
            value="<?php
                echo htmlspecialchars($login_value);
            ?>"
            placeholder="Enter your email or username"
            required
        >



        <!-- =================================================
             PASSWORD
             ================================================= -->

        <label for="password">
            Password
        </label>


        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >



        <!-- =================================================
             LOGIN BUTTON
             ================================================= -->

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