<?php

session_start();


/* =========================================
   REMOVE CUSTOMER SESSION
========================================= */

unset($_SESSION["customer_id"]);
unset($_SESSION["customer_name"]);
unset($_SESSION["customer_email"]);


/* =========================================
   REMOVE ADMIN SESSION
========================================= */

unset($_SESSION["admin_id"]);
unset($_SESSION["admin_username"]);


/* =========================================
   DESTROY SESSION
========================================= */

session_destroy();


/* =========================================
   GO TO SINGLE LOGIN PAGE
========================================= */

header("Location: ../login.php");
exit();

?>