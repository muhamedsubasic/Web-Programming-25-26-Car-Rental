<?php
require_once __DIR__ . '/BaseDao.php';


class AuthDao extends BaseDao {
   protected $table_name;


   public function __construct() {
       $this->table_name = "users";
       parent::__construct($this->table_name);
   }


   public function get_user_by_email($email) {
       $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
             $stmt = $this->connection->prepare("SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1");
             $stmt->bindParam(':email', $email);
             $stmt->execute();
             return $stmt->fetch(PDO::FETCH_ASSOC);
   }
}
