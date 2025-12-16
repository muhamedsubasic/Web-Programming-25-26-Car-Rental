<?php
require 'vendor/autoload.php';
require 'rest/services/AuthService.php';
require 'middleware/AuthMiddleware.php';
require_once __DIR__ . '/rest/services/UsersService.php';
require_once __DIR__ . '/rest/services/CarService.php';
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/BookingService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Flight::register('usersService', 'UsersService');
Flight::register('carService', 'CarService');
Flight::register('categoryService', 'CategoryService');
Flight::register('bookingService', 'BookingService');
Flight::register('reviewService', 'ReviewService');
Flight::register('authService', 'AuthService');
Flight::register('auth_middleware', 'AuthMiddleware');

Flight::route('/*', function() {
   if(
       strpos(Flight::request()->url, '/auth/login') === 0 ||
       strpos(Flight::request()->url, '/auth/register') === 0
   ) {
       return TRUE;
   } else {
       try {
           $token = Flight::request()->getHeader("Authentication");
           if(!$token)
               Flight::halt(401, "Missing authentication header");


           $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));


           Flight::set('user', $decoded_token->user);
           Flight::set('jwt_token', $token);
           return TRUE;
       } catch (\Exception $e) {
           Flight::halt(401, $e->getMessage());
       }
   }
});

require_once __DIR__ . '/rest/routes/AuthRoutes.php';
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
