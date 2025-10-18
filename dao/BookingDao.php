<?php
require_once 'BaseDao.php';

class BookingDao extends BaseDao {
    public function __construct() {
        parent::__construct("booking");
    }

    public function create($user_id, $car_id, $rented_at, $return_time, $price, $status = 'in process') {
        return $this->insert([
            'user_id' => $user_id,
            'car_id' => $car_id,
            'rented_at' => $rented_at,
            'return_time' => $return_time,
            'price' => $price,
            'status' => $status
        ]);
    }

    public function update($id, $user_id, $car_id, $rented_at, $return_time, $price, $status) {
        return parent::update($id, [
            'user_id' => $user_id,
            'car_id' => $car_id,
            'rented_at' => $rented_at,
            'return_time' => $return_time,
            'price' => $price,
            'status' => $status
        ]);
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM booking WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCarId($car_id) {
        $stmt = $this->connection->prepare("SELECT * FROM booking WHERE car_id = :car_id");
        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        return parent::update($id, [
            'status' => $status
        ]);
    }

    public function getActiveBookings() {
        $stmt = $this->connection->prepare("SELECT * FROM booking WHERE status IN ('in process', 'confirmed')");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>