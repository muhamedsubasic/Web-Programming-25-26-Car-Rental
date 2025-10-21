<?php
require_once 'config.php';

class CategoryDao extends BaseDao{
    public static function create($name, $description) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO category (name, description) VALUES (?, ?)");
        return $stmt->execute([$name, $description]);
    }

    // get single category
    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM category WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // update category
    public static function update($id, $name, $description) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE category SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $id]);
    }

    // delete category
    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // get categories
    public static function getAll() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM category");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>