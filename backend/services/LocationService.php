<?php
require_once __DIR__ . '/../dao/LocationDao.php';

class LocationService {
    private $locationDao;

    public function __construct() {
        $this->locationDao = new LocationDAO();
    }

    public function getAllLocations() {
        return $this->locationDao->getAll();
    }

    public function getLocationById($id) {
        $this->validateId($id);
        $location = $this->locationDao->getById($id);
        if (!$location) {
            throw new Exception("Location with ID $id not found");
        }
        return $location;
    }

    public function createLocation($data) {
        $this->validateRequired($data, ['name']);
        
        if ($this->isDuplicateLocation($data['name'], $data['address'] ?? null)) {
            throw new Exception("Location with name '{$data['name']}' and same address already exists");
        }

        if (isset($data['capacity']) && (!is_numeric($data['capacity']) || $data['capacity'] <= 0)) {
            throw new Exception("Capacity must be a positive number");
        }

        $insertId = $this->locationDao->insert($data);
        if (!$insertId) {
            throw new Exception("Failed to create location");
        }
        return $insertId;
    }

    public function updateLocation($id, $data) {
        $this->validateId($id);
        
        $existingLocation = $this->locationDao->getById($id);
        if (!$existingLocation) {
            throw new Exception("Location with ID $id not found");
        }

        $name = $data['name'] ?? $existingLocation['name'];
        $address = $data['address'] ?? $existingLocation['address'];
        if ($this->isDuplicateLocation($name, $address, $id)) {
            throw new Exception("Location with name '$name' and same address already exists");
        }

        if (isset($data['capacity']) && (!is_numeric($data['capacity']) || $data['capacity'] <= 0)) {
            throw new Exception("Capacity must be a positive number");
        }

        if (!$this->locationDao->update($id, $data)) {
            throw new Exception("Failed to update location");
        }
        return true;
    }

    public function deleteLocation($id) {
        $this->validateId($id);
        
        if (!$this->locationDao->getById($id)) {
            throw new Exception("Location with ID $id not found");
        }

        if (!$this->locationDao->delete($id)) {
            throw new Exception("Failed to delete location");
        }
        return true;
    }

    public function getLocationsByCity($city) {
        if (empty(trim($city))) {
            throw new Exception("City cannot be empty");
        }
        return $this->locationDao->getByCity($city);
    }

    private function validateRequired($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Field '$field' is required");
            }
        }
    }

    private function isDuplicateLocation($name, $address, $excludeId = null) {
        $sql = "SELECT * FROM locations WHERE name = :name";
        if ($address !== null) {
            $sql .= " AND address = :address";
        }
        if ($excludeId) {
            $sql .= " AND location_id != :id";
        }

        $stmt = $this->locationDao->connection->prepare($sql);
        $stmt->bindParam(':name', $name);
        if ($address !== null) {
            $stmt->bindParam(':address', $address);
        }
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId);
        }
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    private function validateId($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID: $id");
        }
    }
}
?>
