<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    
    public function validate($role = null) {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = '';

        if (is_array($headers)) {
            $normalized = [];
            foreach ($headers as $k => $v) {
                $normalized[strtolower($k)] = $v;
            }
            if (isset($normalized['authorization'])) {
                $authHeader = $normalized['authorization'];
            }
        }

        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (!$authHeader && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key(Database::getJwtSecret(), 'HS256'));
            Flight::set('user', $decoded);

            if ($role && $decoded->role != $role && $decoded->role != 'admin') { 
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Forbidden']);
                exit;
            }

        } catch (Exception $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized', 'message' => $e->getMessage()]);
            exit;
        }
    }
}
