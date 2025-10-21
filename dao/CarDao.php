<?php
require_once 'BaseDao.php';

class CarDao extends BaseDao {
    public function __construct() {
        parent::__construct("car");
    }

    public function create($category_id, $user_id, $model, $brand, $availability, $daily_rate) {
        return $this->insert([
            'category_id' => $category_id,
            'user_id' => $user_id,
            'model' => $model,
            'brand' => $brand,
            'availability' => $availability,
            'daily_rate' => $daily_rate
        ]);
    }

    public function update($id, $category_id, $user_id, $model, $brand, $availability, $daily_rate) {
        return parent::update($id, [
            'category_id' => $category_id,
            'user_id' => $user_id,
            'model' => $model,
            'brand' => $brand,
            'availability' => $availability,
            'daily_rate' => $daily_rate
        ]);
    }

    public function getAvailable() {
        $stmt = $this->connection->prepare("SELECT * FROM car WHERE availability = TRUE");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($category_id) {
        $stmt = $this->connection->prepare("SELECT * FROM car WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM car WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>