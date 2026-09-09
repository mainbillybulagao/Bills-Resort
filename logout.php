<?php

session_start();

/* Remove customer session */
unset($_SESSION["customer_id"]);
unset($_SESSION["customer_name"]);
unset($_SESSION["customer_email"]);

/* Remove admin session too */
unset($_SESSION["admin_id"]);
unset($_SESSION["admin_username"]);

/* Destroy current session */
session_destroy();

/* Go to the single login page */
header("Location: login.php");
exit();

?><?php

session_start();

/* Remove customer session */
unset($_SESSION["customer_id"]);
unset($_SESSION["customer_name"]);
unset($_SESSION["customer_email"]);

/* Remove admin session too */
unset($_SESSION["admin_id"]);
unset($_SESSION["admin_username"]);

/* Destroy current session */
session_destroy();

/* Go to the single login page */
header("Location: login.php");
exit();

?>