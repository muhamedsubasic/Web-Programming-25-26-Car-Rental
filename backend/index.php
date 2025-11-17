<?php
require 'vendor/autoload.php';
 
require_once __DIR__ . '/rest/services/UsersService.php';
require_once __DIR__ . '/rest/services/CarService.php';
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/BookingService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';

Flight::register('usersService', 'UsersService');
Flight::register('carService', 'CarService');
Flight::register('categoryService', 'CategoryService');
Flight::register('bookingService', 'BookingService');
Flight::register('reviewService', 'ReviewService');

require_once __DIR__ . '/rest/routes/UsersRoutes.php';
require_once __DIR__ . '/rest/routes/CarsRoutes.php';
require_once __DIR__ . '/rest/routes/CategoryRoutes.php';
require_once __DIR__ . '/rest/routes/BookingRoutes.php';
require_once __DIR__ . '/rest/routes/ReviewRoutes.php';

Flight::route('/', function(){
   echo 'If you see this, RentAcar API is working. :)';
});

Flight::start();
?>
