<?php

class ErrorMiddleware {
    
    public static function handleError($e) {
        error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . "\n", 3, __DIR__ . '/../../logs/error.log');
        
        $statusCode = 500;
        $message = $e->getMessage();
        
        if (strpos($message, 'not found') !== false) {
            $statusCode = 404;
        } elseif (strpos($message, 'already exists') !== false) {
            $statusCode = 409;
        } elseif (strpos($message, 'Unauthorized') !== false) {
            $statusCode = 401;
        } elseif (strpos($message, 'Forbidden') !== false) {
            $statusCode = 403;
        } elseif (strpos($message, 'required') !== false || strpos($message, 'Invalid') !== false) {
            $statusCode = 400;
        }
        
        Flight::json([
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ], $statusCode);
    }
    
    public static function logRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $timestamp = date('[Y-m-d H:i:s]');
        
        $logMessage = "$timestamp $method $uri from $ip\n";
        error_log($logMessage, 3, __DIR__ . '/../../logs/access.log');
    }
}
?>
