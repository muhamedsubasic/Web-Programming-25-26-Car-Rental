<?php
require_once __DIR__ . '/BaseDao.php';

class CarDao extends BaseDao {
    public function __construct() {
        parent::__construct("car");
    }
    
    public function getAvailable() {
        $stmt = $this->connection->prepare("SELECT * FROM car WHERE availability = TRUE");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($category_id) {
        $stmt = $this->connection->prepare("SELECT * FROM car WHERE category_id = :category_id");
        $stmt->execute(['category_id' => $category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM car WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
