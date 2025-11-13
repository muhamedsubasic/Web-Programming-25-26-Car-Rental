<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class CategoryService extends BaseService {
    public function __construct() {
        parent::__construct(new CategoryDao());
    }

    public function createCategory(array $data) {
        if (empty($data['name'])) {
            throw new Exception('name is required for category');
        }

        $payload = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ];

        return $this->dao->insert($payload);
    }

    public function getAllCategories() {
        return $this->dao->getAll();
    }

    public function getCategoryById($id) {
        return $this->dao->getById($id);
    }

    public function updateCategory($id, array $data) {
        return $this->dao->updateById($id, $data);
    }
}

?>
