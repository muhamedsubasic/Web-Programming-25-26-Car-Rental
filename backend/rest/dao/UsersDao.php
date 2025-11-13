<?php
require_once __DIR__ . '/BaseDao.php';

class UsersDao extends BaseDao {
    public function __construct() {
        parent::__construct("users");
    }

    // email
    public function getByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // get by name
    public function getByName($name, $surname) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE name = :name AND surname = :surname");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // TODO: acount creation
    public function createAccount($userData) {
        if (empty($userData['name']) || empty($userData['surname']) || 
            empty($userData['email']) || empty($userData['password']) ||
            empty($userData['city']) || empty($userData['phone'])) {
            throw new Exception("All fields are required");
        }

        if ($this->getByEmail($userData['email'])) {
            throw new Exception("Email already registered");
        }

        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        $userData['role'] = $userData['role'] ?? 'customer';
        $userData['created_at'] = date('Y-m-d H:i:s');

        return $this->insert($userData);
    }

}
?>
