<?php

class ValidationMiddleware {
    
    public static function validateRequest($requiredFields = []) {
        $data = Flight::request()->data->getData();
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[] = "Field '$field' is required";
            }
        }
        
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors[] = "Password must be at least 6 characters long";
            }
        }
        
        if (!empty($errors)) {
            Flight::json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $errors
            ], 400);
            die;
        }
        
        return $data;
    }
}
?>
