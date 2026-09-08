<?php

class Customer
{
    private $conn;
    private $table = "customers";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // REGISTER CUSTOMER
    public function register($first_name, $last_name, $email, $password, $phone)
    {
        // Check if email already exists
        $query = "SELECT customer_id 
                  FROM " . $this->table . " 
                  WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false;
        }

        // Encrypt password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert customer
        $query = "INSERT INTO " . $this->table . "
                  (first_name, last_name, email, password, phone)
                  VALUES
                  (:first_name, :last_name, :email, :password, :phone)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":phone", $phone);

        return $stmt->execute();
    }

    // LOGIN CUSTOMER
    public function login($email, $password)
    {
        $query = "SELECT * 
                  FROM " . $this->table . "
                  WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($password, $customer["password"])) {
            return $customer;
        }

        return false;
    }
}
?>