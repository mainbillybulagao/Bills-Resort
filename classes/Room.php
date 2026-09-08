<?php

class Room
{
    private $conn;
    private $table = "rooms";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // GET ALL ROOMS
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . "
                  ORDER BY room_id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET ONE ROOM
    public function getById($room_id)
    {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE room_id = :room_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":room_id", $room_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>