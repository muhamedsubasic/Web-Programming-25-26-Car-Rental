<?php
require_once 'TestConfig.php';
require_once 'config.php';
require_once 'BaseDao.php';
require_once 'UsersDao.php';
require_once 'CategoryDao.php';
require_once 'CarDao.php';
require_once 'BookingDao.php';
require_once 'ReviewDao.php';

TestConfig::setup();

class TestResetter {
    private $connection;
    private $testIdentifiers = [];
    
    public function __construct() {
        $this->connection = Database::connect();
        // Store unique identifiers for this test run
        $this->testIdentifiers = [
            'user_email' => 'test_' . uniqid() . '@test.com',
            'category_name' => 'TestCategory_' . uniqid(),
            'car_model' => 'TestModel_' . uniqid(),
            'review_comment' => 'TestReview_' . uniqid()
        ];
    }
    
    public function getTestIdentifiers() {
        return $this->testIdentifiers;
    }
    
    public function clearThisTestData() {
        echo "=== CLEARING THIS TEST RUN DATA ===\n";
        
        $this->connection->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        try {
            // Delete ONLY the specific test data created in this run
            $ids = $this->testIdentifiers;
            
            // Delete test users (only the exact email from this test run)
            $stmt = $this->connection->prepare("DELETE FROM users WHERE email = ?");
            $stmt->execute([$ids['user_email']]);
            echo "✓ Cleared test user: " . $ids['user_email'] . "\n";
            
            // Delete test categories (only the exact name from this test run)
            $stmt = $this->connection->prepare("DELETE FROM category WHERE name = ?");
            $stmt->execute([$ids['category_name']]);
            echo "✓ Cleared test category: " . $ids['category_name'] . "\n";
            
            // Delete test cars (only the exact model from this test run)
            $stmt = $this->connection->prepare("DELETE FROM car WHERE model = ?");
            $stmt->execute([$ids['car_model']]);
            echo "✓ Cleared test car: " . $ids['car_model'] . "\n";
            
            // Delete test reviews (only the exact comment from this test run)
            $stmt = $this->connection->prepare("DELETE FROM review WHERE comment = ?");
            $stmt->execute([$ids['review_comment']]);
            echo "✓ Cleared test review: " . $ids['review_comment'] . "\n";
            
            // Delete test bookings (only those with specific test prices from this run)
            $stmt = $this->connection->prepare("DELETE FROM booking WHERE price = ?");
            $stmt->execute([299.97]);
            echo "✓ Cleared test bookings with price 299.97\n";
            
        } catch (Exception $e) {
            echo "✗ Error clearing test data: " . $e->getMessage() . "\n";
        }
        
        $this->connection->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "✓ This test run data cleared!\n\n";
    }
}

class SaferDaoTester {
    private $usersDao;
    private $categoryDao;
    private $carDao;
    private $bookingDao;
    private $reviewDao;
    private $testIdentifiers;
    
    public function __construct($testIdentifiers) {
        $this->usersDao = new UsersDao();
        $this->categoryDao = new CategoryDao();
        $this->carDao = new CarDao();
        $this->bookingDao = new BookingDao();
        $this->reviewDao = new ReviewDao();
        $this->testIdentifiers = $testIdentifiers;
    }
    
    public function runAllTests() {
        echo "=== SAFER DAO TESTER (CLEARS ONLY THIS TEST'S DATA) ===\n\n";
        
        $this->testUsersDao();
        $this->testCategoryDao();
        $this->testCarDao();
        $this->testBookingDao();
        $this->testReviewDao();
        
        echo "\n=== ALL TESTS COMPLETED ===\n";
    }
    
    private function testUsersDao() {
        echo "--- USERS DAO ---\n";
        
        // Test createAccount with unique test data
        $userData = [
            'name' => 'TestUser', 
            'surname' => 'Tester', 
            'email' => $this->testIdentifiers['user_email'],
            'password' => 'testpass123', 
            'phone' => '111222333', 
            'city' => 'TestCity', 
            'role' => 'customer'
        ];
        
        try {
            $result = $this->usersDao->createAccount($userData);
            $this->printResult("createAccount", $result);
            
            if ($result) {
                $user = $this->usersDao->getByEmail($userData['email']);
                if ($user) {
                    $this->printResult("getByEmail", !empty($user));
                }
            }
        } catch (Exception $e) {
            $this->printResult("createAccount", false, $e->getMessage());
        }
        
        // Test getAll
        $allUsers = $this->usersDao->getAll();
        $this->printResult("getAll", count($allUsers) > 0, count($allUsers) . " total users");
        
        echo "\n";
    }
    
    private function testCategoryDao() {
        echo "--- CATEGORY DAO ---\n";
        
        // Test insert with unique test data
        $insertResult = $this->categoryDao->insert([
            'name' => $this->testIdentifiers['category_name'], 
            'description' => 'Test Description'
        ]);
        $this->printResult("insert", $insertResult);
        
        // Test getAll
        $allCategories = $this->categoryDao->getAll();
        $this->printResult("getAll", count($allCategories) > 0, count($allCategories) . " total categories");
        
        echo "\n";
    }
    
    private function testCarDao() {
        echo "--- CAR DAO ---\n";
        
        // Get any existing user and category (not necessarily test data)
        $users = $this->usersDao->getAll();
        $categories = $this->categoryDao->getAll();
        
        if (empty($users) || empty($categories)) {
            echo "✗ Skipping - need at least one user and category in database\n\n";
            return;
        }
        
        $userId = $users[0]['id'];
        $categoryId = $categories[0]['id'];
        
        // Use insert with unique test data
        $insertResult = $this->carDao->insert([
            'category_id' => $categoryId,
            'user_id' => $userId,
            'model' => $this->testIdentifiers['car_model'],
            'brand' => 'TestBrand',
            'availability' => true,
            'daily_rate' => 99.99
        ]);
        $this->printResult("insert", $insertResult);
        
        // Test inherited methods
        $allCars = $this->carDao->getAll();
        $this->printResult("getAll", count($allCars) > 0, count($allCars) . " total cars");
        
        // Test custom methods
        $available = $this->carDao->getAvailable();
        $this->printResult("getAvailable", count($available) > 0, count($available) . " available");
        
        $byCategory = $this->carDao->getByCategory($categoryId);
        $this->printResult("getByCategory", true, count($byCategory) . " in category");
        
        $byUser = $this->carDao->getByUser($userId);
        $this->printResult("getByUser", true, count($byUser) . " by user");
        
        echo "\n";
    }
    
    private function testBookingDao() {
        echo "--- BOOKING DAO ---\n";
        
        // Get any existing user and car
        $users = $this->usersDao->getAll();
        $cars = $this->carDao->getAll();
        
        if (empty($users) || empty($cars)) {
            echo "✗ Skipping - need at least one user and car in database\n\n";
            return;
        }
        
        $userId = $users[0]['id'];
        $carId = $cars[0]['id'];
        
        // Use insert with test data
        $insertResult = $this->bookingDao->insert([
            'user_id' => $userId,
            'car_id' => $carId,
            'rented_at' => date('Y-m-d H:i:s'),
            'return_time' => date('Y-m-d H:i:s', strtotime('+3 days')),
            'price' => 299.97, // Specific test price
            'status' => 'in process'
        ]);
        $this->printResult("insert", $insertResult);
        
        // Test inherited methods
        $allBookings = $this->bookingDao->getAll();
        $this->printResult("getAll", count($allBookings) > 0, count($allBookings) . " total bookings");
        
        // Test custom methods
        $userBookings = $this->bookingDao->getByUserId($userId);
        $this->printResult("getByUserId", count($userBookings) > 0, count($userBookings) . " user bookings");
        
        $carBookings = $this->bookingDao->getByCarId($carId);
        $this->printResult("getByCarId", true, count($carBookings) . " car bookings");
        
        $active = $this->bookingDao->getActiveBookings();
        $this->printResult("getActiveBookings", true, count($active) . " active");
        
        echo "\n";
    }
    
    private function testReviewDao() {
        echo "--- REVIEW DAO ---\n";
        
        // Get any existing user and car
        $users = $this->usersDao->getAll();
        $cars = $this->carDao->getAll();
        
        if (empty($users) || empty($cars)) {
            echo "✗ Skipping - need at least one user and car in database\n\n";
            return;
        }
        
        $userId = $users[0]['id'];
        $carId = $cars[0]['id'];
        
        // Test create method with unique comment
        $result = $this->reviewDao->create($userId, $carId, 5, $this->testIdentifiers['review_comment']);
        $this->printResult("create", $result);
        
        // Test inherited methods
        $allReviews = $this->reviewDao->getAll();
        $this->printResult("getAll", count($allReviews) > 0, count($allReviews) . " total reviews");
        
        // Test custom methods
        $carReviews = $this->reviewDao->getByCarId($carId);
        $this->printResult("getByCarId", count($carReviews) > 0, count($carReviews) . " car reviews");
        
        $userReviews = $this->reviewDao->getByUserId($userId);
        $this->printResult("getByUserId", count($userReviews) > 0, count($userReviews) . " user reviews");
        
        $avgRating = $this->reviewDao->getAverageRating($carId);
        $this->printResult("getAverageRating", is_numeric($avgRating), "Avg: " . number_format($avgRating, 1));
        
        echo "\n";
    }
    
    private function printResult($method, $success, $message = '') {
        $status = $success ? '✓ PASS' : '✗ FAIL';
        $output = "$status $method";
        if ($message) {
            $output .= " ($message)";
        }
        echo $output . "\n";
    }
}

// Run the tests
try {
    // Create resetter first to generate unique identifiers
    $resetter = new TestResetter();
    $testIdentifiers = $resetter->getTestIdentifiers();
    
    echo "Test run identifiers:\n";
    foreach ($testIdentifiers as $key => $value) {
        echo "  $key: $value\n";
    }
    echo "\n";
    
    // Run tests
    $tester = new SaferDaoTester($testIdentifiers);
    $tester->runAllTests();
    
    // Ask user if they want to clean up
    echo "\nClean up this test's data? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if(trim($line) == 'y'){
        $resetter->clearThisTestData();
    } else {
        echo "Test data preserved in database.\n";
    }
    fclose($handle);
    
} catch (Exception $e) {
    echo "Test Error: " . $e->getMessage() . "\n";
}