<?php
require_once 'BaseDao.php';

class ReviewDao extends BaseDao {
    public function __construct() {
        parent::__construct("review");
    }

    public function create($user_id, $car_id, $rating, $comment = null) {
        $data = [
            'user_id' => $user_id,
            'car_id' => $car_id,
            'rating' => $rating
        ];

        if ($comment !== null) {
            $data['comment'] = $comment;
        }

        return $this->insert($data);
    }

    public function update($id, $rating, $comment = null) {
        $data = ['rating' => $rating];
        
        if ($comment !== null) {
            $data['comment'] = $comment;
        }

        return parent::update($id, $data);
    }

    public function getByCarId($car_id) {
        $stmt = $this->connection->prepare("SELECT * FROM review WHERE car_id = :car_id");
        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM review WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($car_id) {
        $stmt = $this->connection->prepare("SELECT AVG(rating) as average_rating FROM review WHERE car_id = :car_id");
        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['average_rating'];
    }
}
?>