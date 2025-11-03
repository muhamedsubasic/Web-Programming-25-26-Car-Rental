<?php
require_once 'BaseDao.php';

class BookingDao extends BaseDao {
    public function __construct() {
        parent::__construct("booking");
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM booking WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCarId($car_id) {
        $stmt = $this->connection->prepare("SELECT * FROM booking WHERE car_id = :car_id");
        $stmt->execute(['car_id' => $car_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveBookings() {
        $stmt = $this->connection->prepare("SELECT * FROM booking WHERE status IN ('in process', 'confirmed')");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>