<?php
class TestConfig {
    public static function setup() {
        
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_NAME'] = 'car_rental_db';
        $_ENV['DB_USER'] = 'root';
        $_ENV['DB_PASS'] = '';
    }
}
?>