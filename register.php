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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
$first_name = trim($_POST["first_name"] ?? "");
$last_name = trim($_POST["last_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";


    // Check required fields
    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($email) ||
        empty($password)
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } else {

        // Register customer
        if (
            $customer->register(
                $first_name,
                $last_name,
                $email,
                $password,
                $phone
            )
        ) {

            // Log the customer in automatically
            $customerData = $customer->login($email, $password);

            if ($customerData) {

                // Create customer session
                $_SESSION["customer_id"] =
                    $customerData["customer_id"];

                $_SESSION["customer_name"] =
                    $customerData["first_name"] . " " .
                    $customerData["last_name"];

                $_SESSION["customer_email"] =
                    $customerData["email"];


                // Go directly to booking page
                header("Location: book.php");
                exit();

            } else {

                $error = "Registration successful, but automatic login failed.";

            }

        } else {

            $error = "Email already exists. Please use another email.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Bill's Resort</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>


<body>


<header class="register-header">

    <a href="index.php">

        <img
            src="assets/images/mainlogo.jpg"
            alt="Bill's Resort Logo"
        >

    </a>

</header>


<div class="register-container">

    <h1>Create an Account</h1>

    <p>
        Register to book your stay at Bill's Resort.
    </p>


    <?php if (!empty($error)): ?>

        <p style="color: red;">

            <?php echo htmlspecialchars($error); ?>

        </p>

    <?php endif; ?>


    <?php if (!empty($success)): ?>

        <p style="color: green;">

            <?php echo htmlspecialchars($success); ?>

        </p>

    <?php endif; ?>


    <form method="POST" action="">


        <label>First Name</label>

        <input
            type="text"
            name="first_name"
            required
        >


        <label>Last Name</label>

        <input
            type="text"
            name="last_name"
            required
        >


        <label>Email</label>

        <input
            type="email"
            name="email"
            required
        >


        <label>Phone</label>

        <input
            type="text"
            name="phone"
        >


        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >


        <label>Confirm Password</label>

        <input
            type="password"
            name="confirm_password"
            required
        >


        <button type="submit">

            REGISTER

        </button>


    </form>


    <p>

        Already have an account?

        <a href="login.php">
            Login here
        </a>

    </p>


    <p>

        <a href="index.php">
            Back to Home
        </a>

    </p>


</div>


</body>

</html>