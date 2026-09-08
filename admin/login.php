<?php

session_start();

require_once "../config/Database.php";


/* =========================================
   DATABASE CONNECTION
========================================= */

$database = new Database();
$db = $database->getConnection();


/* =========================================
   VARIABLES
========================================= */

$error = "";


/* =========================================
   LOGIN
========================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";


    if (empty($username) || empty($password)) {

        $error = "Please enter your username and password.";

    } else {

        $query = "SELECT * FROM admins
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":username", $username);

        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($admin && password_verify($password, $admin["password"])) {

            $_SESSION["admin_id"] = $admin["admin_id"];
            $_SESSION["admin_username"] = $admin["username"];


            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Invalid username or password.";

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Bill's Resort</title>

    <link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<!-- =========================================================
     ADMIN LOGIN
     ========================================================= -->

<div class="admin-login-container">


    <h1>
        ADMIN LOGIN
    </h1>


    <p>
        Bill's Resort Administration
    </p>


    <!-- ERROR MESSAGE -->

    <?php if (!empty($error)): ?>

        <p style="color: red;">

            <?php echo htmlspecialchars($error); ?>

        </p>

    <?php endif; ?>


    <!-- LOGIN FORM -->

    <form method="POST" action="">


        <!-- USERNAME -->

        <label>
            Username
        </label>

        <input
            type="text"
            name="username"
            placeholder="Enter username"
            required
        >


        <!-- PASSWORD -->

        <label>
            Password
        </label>

        <input
            type="password"
            name="password"
            placeholder="Enter password"
            required
        >


        <!-- LOGIN BUTTON -->

        <button type="submit">
            LOGIN
        </button>


    </form>


    <p>

        <a href="../index.php">
            Back to Bill's Resort
        </a>

    </p>


</div>


</body>

</html>