<?php
require_once __DIR__ . '/../dao/UserDao.php';

class UserService {
    private $userDao;

    public function __construct() {
        $this->userDao = new UserDAO();
    }

    public function getAllUsers() {
        return $this->userDao->getAll();
    }

    public function getUserById($id) {
        $this->validateId($id);
        $user = $this->userDao->getById($id);
        if (!$user) {
            throw new Exception("User with ID $id not found");
        }
        return $user;
    }

    public function createUser($data) {
        $this->validateRequired($data, ['name', 'email', 'password']);
        $this->validateEmail($data['email']);
        
        if ($this->userDao->getByEmail($data['email'])) {
            throw new Exception("User with email {$data['email']} already exists");
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if (!$this->userDao->insert($data)) {
            throw new Exception("Failed to create user");
        }
        return true;
    }

    public function updateUser($id, $data) {
        $this->validateId($id);
        
        $existingUser = $this->userDao->getById($id);
        if (!$existingUser) {
            throw new Exception("User with ID $id not found");
        }

        if (isset($data['email'])) {
            $this->validateEmail($data['email']);
            
            $userWithEmail = $this->userDao->getByEmail($data['email']);
            if ($userWithEmail && $userWithEmail['user_id'] != $id) {
                throw new Exception("Email {$data['email']} is already in use");
            }
        }

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (!$this->userDao->update($id, $data)) {
            throw new Exception("Failed to update user");
        }
        return true;
    }

    public function deleteUser($id) {
        $this->validateId($id);
        
        if (!$this->userDao->getById($id)) {
            throw new Exception("User with ID $id not found");
        }

        if (!$this->userDao->delete($id)) {
            throw new Exception("Failed to delete user");
        }
        return true;
    }

    public function getUserByEmail($email) {
        $this->validateEmail($email);
        return $this->userDao->getByEmail($email);
    }
    private function validateRequired($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Field '$field' is required");
            }
        }
    }

    private function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format: $email");
        }
    }

    private function validateId($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID: $id");
        }
    }
}
?>
