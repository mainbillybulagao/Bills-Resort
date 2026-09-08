<?php

class Database
{
    private $host = "localhost";
    private $db_name = "bills_resort";
    private $username = "root";
    private $password = "";

    public $conn;


    /* =========================================
       DATABASE CONNECTION
    ========================================= */

    public function getConnection()
    {
        $this->conn = null;

        try {

            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            /* Enable PDO exceptions */
            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            /* Return database results as associative arrays */
            $this->conn->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

        } catch (PDOException $exception) {

            /* Save the technical error in the server log */
            error_log($exception->getMessage());

            /* Show a safe message to the user */
            die(
                "Sorry, we are unable to connect to the database right now."
            );

        }

        return $this->conn;
    }
}

?>