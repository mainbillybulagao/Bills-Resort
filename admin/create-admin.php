<?php

require_once "../config/Database.php";

$database = new Database();
$db = $database->getConnection();

$username = "admin";
$password = "admin123";

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO admins (username, password)
          VALUES (:username, :password)";

$stmt = $db->prepare($query);

$stmt->bindParam(":username", $username);
$stmt->bindParam(":password", $hashed_password);

if ($stmt->execute()) {

    echo "Admin account created successfully.";

} else {

    echo "Failed to create admin account.";

}

?>