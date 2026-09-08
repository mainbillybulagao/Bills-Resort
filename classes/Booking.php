<?php

class Booking
{
    private $conn;
    private $table = "bookings";

    public function __construct($db)
    {
        $this->conn = $db;
    }


    /* =========================================================
       FIND AVAILABLE ROOM
       ========================================================= */

    public function findAvailableRoom($room_name, $check_in, $check_out)
    {
        $query = "SELECT *
                  FROM rooms
                  WHERE room_name = :room_name
                  AND status = 'Available'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM bookings
                      WHERE bookings.room_id = rooms.room_id
                      AND bookings.status != 'Cancelled'
                      AND bookings.check_in < :check_out
                      AND bookings.check_out > :check_in
                  )
                  ORDER BY room_id ASC
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":room_name", $room_name);
        $stmt->bindParam(":check_in", $check_in);
        $stmt->bindParam(":check_out", $check_out);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /* =========================================================
       CREATE BOOKING
       ========================================================= */

    public function createBooking(
        $customer_id,
        $room_id,
        $check_in,
        $check_out,
        $number_of_days,
        $room_price,
        $total_cost
    ) {
        $query = "INSERT INTO " . $this->table . "
                  (
                      customer_id,
                      room_id,
                      check_in,
                      check_out,
                      number_of_days,
                      room_price,
                      total_cost
                  )
                  VALUES
                  (
                      :customer_id,
                      :room_id,
                      :check_in,
                      :check_out,
                      :number_of_days,
                      :room_price,
                      :total_cost
                  )";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->bindParam(":room_id", $room_id);
        $stmt->bindParam(":check_in", $check_in);
        $stmt->bindParam(":check_out", $check_out);
        $stmt->bindParam(":number_of_days", $number_of_days);
        $stmt->bindParam(":room_price", $room_price);
        $stmt->bindParam(":total_cost", $total_cost);

        return $stmt->execute();
    }


    /* =========================================================
       CHECK ROOM AVAILABILITY
       ========================================================= */

    public function isRoomAvailable($room_id, $check_in, $check_out)
    {
        $query = "SELECT booking_id
                  FROM " . $this->table . "
                  WHERE room_id = :room_id
                  AND status != 'Cancelled'
                  AND check_in < :check_out
                  AND check_out > :check_in";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":room_id", $room_id);
        $stmt->bindParam(":check_in", $check_in);
        $stmt->bindParam(":check_out", $check_out);

        $stmt->execute();

        return $stmt->rowCount() == 0;
    }


    /* =========================================================
       GET CUSTOMER BOOKINGS
       ========================================================= */

    public function getCustomerBookings($customer_id)
    {
        $query = "SELECT bookings.*, 
                         rooms.room_name,
                         rooms.room_number,
                         rooms.image
                  FROM bookings
                  INNER JOIN rooms
                  ON bookings.room_id = rooms.room_id
                  WHERE bookings.customer_id = :customer_id
                  ORDER BY bookings.created_at DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":customer_id", $customer_id);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* =========================================================
       GET BOOKING BY ID
       ========================================================= */

    public function getById($booking_id)
    {
        $query = "SELECT bookings.*,
                         rooms.room_name,
                         rooms.room_number,
                         rooms.image
                  FROM bookings
                  INNER JOIN rooms
                  ON bookings.room_id = rooms.room_id
                  WHERE bookings.booking_id = :booking_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":booking_id", $booking_id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>