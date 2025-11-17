<?php
require_once __DIR__ . '/rest/config.php';

class TestDatabaseSetup {
    private $connection;
    
    public function __construct() {
        try {
            $this->connection = Database::connect();
            if (!($this->connection instanceof PDO)) {
                throw new Exception('Database::connect() did not return a PDO instance');
            }
            echo "Connected to database: " . \Config::DB_NAME() . "\n";
        } catch (Exception $e) {
            echo "Database connection error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function clearTestData() {
    $tables = ['review', 'booking', 'car', 'category', 'users'];

    try {
        $this->connection->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            $q = "TRUNCATE TABLE `" . str_replace('`','', $table) . "`";
            $this->connection->exec($q);
            echo "Truncated $table table\n";
        }
        $this->connection->exec('SET FOREIGN_KEY_CHECKS=1');
    } catch (PDOException $e) {
        echo "Error truncating tables: " . $e->getMessage() . "\n";
        try { $this->connection->exec('SET FOREIGN_KEY_CHECKS=1'); } catch (Exception $_) {}
        exit(1);
    }
    }
    
    public function insertTestData() {
        $users = [
            ['John', 'Doe', 'john@test.com', 'password123', '123456789', 'customer'],
            ['Jane', 'Smith', 'jane@test.com', 'password123', '987654321', 'customer'],
            ['Admin', 'User', 'admin@test.com', 'admin123', '555555555', 'admin']
        ];
        
        try {
            $this->connection->beginTransaction();
            foreach ($users as $user) {
                $stmt = $this->connection->prepare(
                    "INSERT INTO `users` (name, surname, email, password, phone, role, created_at) 
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
                    "INSERT INTO `category` (name, description, created_at) VALUES (?, ?, NOW())"
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
                    "INSERT INTO `car` (category_id, user_id, model, brand, availability, daily_rate, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, NOW())"
                );
                $stmt->execute($car);
            }
            $this->connection->commit();
            echo "Inserted test cars\n";
        } catch (PDOException $e) {
            $this->connection->rollBack();
            echo "Error inserting test data: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

$setup = new TestDatabaseSetup();
$setup->clearTestData();
$setup->insertTestData();
echo "Test database setup complete!\n";
?>