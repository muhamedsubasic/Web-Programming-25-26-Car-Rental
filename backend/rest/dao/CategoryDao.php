<?php
require_once __DIR__ . '/BaseDao.php';

class CategoryDao extends BaseDao {
    public function __construct() {
        parent::__construct("category");
    }

    public function create($name, $description) {
        return $this->insert([
            'name' => $name,
            'description' => $description
        ]);
    }

    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM category ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM category WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
