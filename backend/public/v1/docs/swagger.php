<?php

ini_set('display_errors', 0);
error_reporting(0);
    

require __DIR__ . '/../../../vendor/autoload.php';


$serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
if ($serverName === 'localhost' || $serverName === '127.0.0.1') {
    define('BASE_URL', 'http://localhost/eventmanager/backend');
} else {
    define('BASE_URL', 'https://lobster-app-czvm2.ondigitalocean.app/backend/');
}

$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',
    __DIR__ . '/../../../rest/routes'
]);
header('Content-Type: application/json');
echo $openapi->toJson();
?>


