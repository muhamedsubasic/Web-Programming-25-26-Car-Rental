<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ReviewDao.php';
require_once __DIR__ . '/../dao/UsersDao.php';
require_once __DIR__ . '/../dao/CarDao.php';

class ReviewService extends BaseService {
    protected $usersDao;
    protected $carDao;

    public function __construct() {
        parent::__construct(new ReviewDao());
        $this->usersDao = new UsersDao();
        $this->carDao = new CarDao();
    }

    public function createReview($user_id, $car_id, $rating, $comment = null) {
        if (!is_int($rating) && !ctype_digit((string)$rating)) {
            throw new Exception('rating must be an integer');
        }

        $rating = (int)$rating;
        if ($rating < 1 || $rating > 5) {
            throw new Exception('rating must be between 1 and 5');
        }

        // check user and car exist
        if (!$this->usersDao->getById($user_id)) {
            throw new Exception('user does not exist');
        }

        if (!$this->carDao->getById($car_id)) {
            throw new Exception('car does not exist');
        }

        return $this->dao->create($user_id, $car_id, $rating, $comment);
    }

    public function getByCar($car_id) {
        return $this->dao->getByCarId($car_id);
    }

    public function getAverageRating($car_id) {
        return $this->dao->getAverageRating($car_id);
    }
}

?>
