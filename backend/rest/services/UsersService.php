<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UsersDao.php';

class UsersService extends BaseService {
    public function __construct() {
        parent::__construct(new UsersDao());
    }

    public function register(array $data) {
        $required = ['name','surname','email','password','city','phone'];
        foreach ($required as $f) {
            if (empty($data[$f])) {
                throw new Exception("Field '$f' is required");
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        return $this->dao->createAccount($data);
    }

    public function login(string $email, string $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email');
        }

        $user = $this->dao->getByEmail($email);
        if (!$user) {
            return false;
        }
        
        if (!isset($user['password'])) {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }

        return false;
    }
    
    public function updateProfile($id, array $data) {
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email');
        }

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->dao->updateById($id, $data);
    }
}

?>
