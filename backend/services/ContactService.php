<?php
require_once __DIR__ . '/../dao/ContactDao.php';

class ContactService {
    private $contactDao;

    public function __construct() {
        $this->contactDao = new ContactDAO();
    }

    public function getAllContacts() {
        return $this->contactDao->getAll();
    }

    public function getContactById($id) {
        $this->validateId($id);
        $contact = $this->contactDao->getById($id);
        if (!$contact) {
            throw new Exception("Contact with ID $id not found");
        }
        return $contact;
    }

    public function createContact($data) {
        $this->validateRequired($data, ['name', 'email', 'message']);
        
        $this->validateEmail($data['email']);

        if (strlen(trim($data['message'])) < 10) {
            throw new Exception("Message must be at least 10 characters long");
        }

        if (!$this->contactDao->insert($data)) {
            throw new Exception("Failed to create contact");
        }
        return true;
    }

    public function updateContact($id, $data) {
        $this->validateId($id);
        
        if (!$this->contactDao->getById($id)) {
            throw new Exception("Contact with ID $id not found");
        }

        if (isset($data['email'])) {
            $this->validateEmail($data['email']);
        }

        if (isset($data['message']) && strlen(trim($data['message'])) < 10) {
            throw new Exception("Message must be at least 10 characters long");
        }

        if (!$this->contactDao->update($id, $data)) {
            throw new Exception("Failed to update contact");
        }
        return true;
    }

    public function deleteContact($id) {
        $this->validateId($id);
        
        if (!$this->contactDao->getById($id)) {
            throw new Exception("Contact with ID $id not found");
        }

        if (!$this->contactDao->delete($id)) {
            throw new Exception("Failed to delete contact");
        }
        return true;
    }

    public function getContactsByEmail($email) {
        $this->validateEmail($email);
        return $this->contactDao->getByEmail($email);
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
