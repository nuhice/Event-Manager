<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/rest/middleware/ErrorMiddleware.php';
require_once __DIR__ . '/rest/middleware/ValidationMiddleware.php';

    // Global preflight handler
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        http_response_code(200);
        exit();
    }
    
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    
    ErrorMiddleware::logRequest();

require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/EventRoutes.php';
require_once __DIR__ . '/rest/routes/LocationRoutes.php';
require_once __DIR__ . '/rest/routes/BookingRoutes.php';
require_once __DIR__ . '/rest/routes/ContactRoutes.php';

$userRoutes = new UserRoutes();
$userRoutes->registerRoutes();

$eventRoutes = new EventRoutes();
$eventRoutes->registerRoutes();

$locationRoutes = new LocationRoutes();
$locationRoutes->registerRoutes();

$bookingRoutes = new BookingRoutes();
$bookingRoutes->registerRoutes();

$contactRoutes = new ContactRoutes();
$contactRoutes->registerRoutes();



Flight::route('GET /', function() {
    Flight::json([
        'success' => true,
        'message' => 'Event Manager API',
        'version' => '1.0.0',
        'endpoints' => [
            'users' => '/users',
            'events' => '/events',
            'locations' => '/locations',
            'bookings' => '/bookings',
            'contacts' => '/contacts'
        ]
    ], 200);
});

Flight::map('notFound', function() {
    Flight::json([
        'success' => false,
        'error' => 'Route not found'
    ], 404);
});

Flight::map('error', function($e) {
    ErrorMiddleware::handleError($e);
});

Flight::start();
?>