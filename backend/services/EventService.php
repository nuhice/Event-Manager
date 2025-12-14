<?php
require_once __DIR__ . '/../dao/EventDao.php';

class EventService {
    private $eventDao;

    public function __construct() {
        $this->eventDao = new EventDAO();
    }

    public function getAllEvents() {
        return $this->eventDao->getAll();
    }

    public function getEventById($id) {
        $this->validateId($id);
        $event = $this->eventDao->getById($id);
        if (!$event) {
            throw new Exception("Event with ID $id not found");
        }
        return $event;
    }

    public function createEvent($data) {
        $this->validateRequired($data, ['title', 'start_date']);
        
        $this->validateDate($data['start_date'], 'start_date');
        
        if (isset($data['end_date'])) {
            $this->validateDate($data['end_date'], 'end_date');
            $this->validateDateRange($data['start_date'], $data['end_date']);
        }

        if ($this->isDuplicateEvent($data['title'], $data['start_date'])) {
            throw new Exception("Event with title '{$data['title']}' on {$data['start_date']} already exists");
        }

        if (isset($data['capacity']) && (!is_numeric($data['capacity']) || $data['capacity'] <= 0)) {
            throw new Exception("Capacity must be a positive number");
        }

        if (!$this->eventDao->insert($data)) {
            throw new Exception("Failed to create event");
        }
        return true;
    }

    public function updateEvent($id, $data) {
        $this->validateId($id);
        
        $existingEvent = $this->eventDao->getById($id);
        if (!$existingEvent) {
            throw new Exception("Event with ID $id not found");
        }

        if (isset($data['start_date'])) {
            $this->validateDate($data['start_date'], 'start_date');
        }
        if (isset($data['end_date'])) {
            $this->validateDate($data['end_date'], 'end_date');
        }

        $startDate = $data['start_date'] ?? $existingEvent['start_date'];
        $endDate = $data['end_date'] ?? $existingEvent['end_date'];
        if ($startDate && $endDate) {
            $this->validateDateRange($startDate, $endDate);
        }

        $title = $data['title'] ?? $existingEvent['title'];
        $checkStartDate = $data['start_date'] ?? $existingEvent['start_date'];
        if ($this->isDuplicateEvent($title, $checkStartDate, $id)) {
            throw new Exception("Event with title '$title' on $checkStartDate already exists");
        }

        if (isset($data['capacity']) && (!is_numeric($data['capacity']) || $data['capacity'] <= 0)) {
            throw new Exception("Capacity must be a positive number");
        }

        if (!$this->eventDao->update($id, $data)) {
            throw new Exception("Failed to update event");
        }
        return true;
    }

    public function deleteEvent($id) {
        $this->validateId($id);
        
        if (!$this->eventDao->getById($id)) {
            throw new Exception("Event with ID $id not found");
        }

        if (!$this->eventDao->delete($id)) {
            throw new Exception("Failed to delete event");
        }
        return true;
    }

    public function getEventByName($name) {
        if (empty(trim($name))) {
            throw new Exception("Event name cannot be empty");
        }
        return $this->eventDao->getByName($name);
    }

    private function validateRequired($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Field '$field' is required");
            }
        }
    }

    private function validateDate($date, $fieldName) {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new Exception("Invalid date format for $fieldName: $date");
        }
    }

    private function validateDateRange($startDate, $endDate) {
        if (strtotime($endDate) < strtotime($startDate)) {
            throw new Exception("End date must be after start date");
        }
    }

    private function isDuplicateEvent($title, $startDate, $excludeId = null) {
        $stmt = $this->eventDao->connection->prepare(
            "SELECT * FROM events WHERE title = :title AND start_date = :start_date" . 
            ($excludeId ? " AND event_id != :id" : "")
        );
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':start_date', $startDate);
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
