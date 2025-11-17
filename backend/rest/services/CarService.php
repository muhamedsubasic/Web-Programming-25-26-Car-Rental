<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CarDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class CarService extends BaseService {
    protected $categoryDao;

    public function __construct() {
        parent::__construct(new CarDao());
        $this->categoryDao = new CategoryDao();
    }

    public function getAvailable() {
        return $this->dao->getAvailable();
    }

    public function getByCategory($category_id) {
        return $this->dao->getByCategory($category_id);
    }

    public function createCar(array $data) {
        $required = ['model','brand','daily_rate','category_id'];
        foreach ($required as $f) {
            if (!isset($data[$f]) || $data[$f] === '') {
                throw new Exception("Field '$f' is required for creating a car");
            }
        }

        if (!is_numeric($data['daily_rate']) || $data['daily_rate'] < 0) {
            throw new Exception('daily_rate must be a positive number');
        }

        // verify category exists
        $cat = $this->categoryDao->getById($data['category_id']);
        if (!$cat) {
            throw new Exception('category_id does not reference an existing category');
        }

        $payload = [
            'category_id' => $data['category_id'],
            'user_id' => $data['user_id'] ?? null,
            'model' => $data['model'],
            'brand' => $data['brand'],
            'availability' => isset($data['availability']) ? (bool)$data['availability'] : true,
            'daily_rate' => $data['daily_rate']
        ];

        return $this->dao->insert($payload);
    }

    public function updateCar($id, array $data) {
        if (isset($data['daily_rate']) && (!is_numeric($data['daily_rate']) || $data['daily_rate'] < 0)) {
            throw new Exception('daily_rate must be a positive number');
        }

        if (isset($data['category_id'])) {
            $cat = $this->categoryDao->getById($data['category_id']);
            if (!$cat) {
                throw new Exception('category_id does not reference an existing category');
            }
        }

        return $this->dao->updateById($id, $data);
    }

    public function setAvailability($id, bool $available) {
        return $this->dao->updateById($id, ['availability' => $available]);
    }
}

?>
