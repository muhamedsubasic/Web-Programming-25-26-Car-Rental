<?php
require_once 'config.php';

class TestDatabaseSetup {
    private $connection;
    
    public function __construct() {
        $this->connection = Database::connect();
    }
    
    public function clearTestData() {
        $tables = ['review', 'booking', 'car', 'category', 'users'];
        
        foreach ($tables as $table) {
            try {
                $this->connection->exec("DELETE FROM $table");
                echo "Cleared $table table\n";
            } catch (PDOException $e) {
                echo "Error clearing $table: " . $e->getMessage() . "\n";
            }
        }
    }
    
    public function insertTestData() {
        // Insert test users
        $users = [
            ['John', 'Doe', 'john@test.com', 'password123', '123456789', 'customer'],
            ['Jane', 'Smith', 'jane@test.com', 'password123', '987654321', 'customer'],
            ['Admin', 'User', 'admin@test.com', 'admin123', '555555555', 'admin']
        ];
        
        foreach ($users as $user) {
            $stmt = $this->connection->prepare(
                "INSERT INTO users (name, surname, email, password, phone, role, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute($user);
        }
        echo "Inserted test users\n";
        
        $categories = [
            ['Economy', 'Budget-friendly cars'],
            ['SUV', 'Sport Utility Vehicles'],
            ['Luxury', 'Premium vehicles']
        ];
        
        foreach ($categories as $category) {
            $stmt = $this->connection->prepare(
                "INSERT INTO category (name, description, created_at) VALUES (?, ?, NOW())"
            );
            $stmt->execute($category);
        }
        echo "Inserted test categories\n";
        
        $cars = [
            [1, 1, 'Civic', 'Honda', 1, 45.00],
            [2, 1, 'CR-V', 'Honda', 1, 75.00],
            [3, 2, 'Model S', 'Tesla', 1, 120.00]
        ];
        
        foreach ($cars as $car) {
            $stmt = $this->connection->prepare(
                "INSERT INTO car (category_id, user_id, model, brand, availability, daily_rate, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute($car);
        }
        echo "Inserted test cars\n";
    }
}

$setup = new TestDatabaseSetup();
$setup->clearTestData();
$setup->insertTestData();
echo "Test database setup complete!\n";
?>