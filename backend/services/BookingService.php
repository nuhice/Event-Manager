<?php
require_once __DIR__ . '/../dao/BookingDao.php';

class BookingService {
    private $bookingDao;

    public function __construct() {
        $this->bookingDao = new BookingDAO();
    }

    public function getAllBookings() {
        return $this->bookingDao->getAll();
    }

    public function getBookingById($id) {
        $this->validateId($id);
        $booking = $this->bookingDao->getById($id);
        if (!$booking) {
            throw new Exception("Booking with ID $id not found");
        }
        return $booking;
    }

    public function createBooking($data) {
        $this->validateRequired($data, ['user_id', 'event_id']);
        
        $this->validateId($data['user_id'], 'user_id');
        $this->validateId($data['event_id'], 'event_id');

        if ($this->isDuplicateBooking($data['user_id'], $data['event_id'])) {
            throw new Exception("User has already booked this event");
        }

        if (isset($data['status'])) {
            $this->validateStatus($data['status']);
        }

        if (!$this->bookingDao->insert($data)) {
            throw new Exception("Failed to create booking");
        }
        return true;
    }

    public function updateBooking($id, $data) {
        $this->validateId($id);
        
        $existingBooking = $this->bookingDao->getById($id);
        if (!$existingBooking) {
            throw new Exception("Booking with ID $id not found");
        }

        if (isset($data['user_id'])) {
            $this->validateId($data['user_id'], 'user_id');
        }
        if (isset($data['event_id'])) {
            $this->validateId($data['event_id'], 'event_id');
        }

        $userId = $data['user_id'] ?? $existingBooking['user_id'];
        $eventId = $data['event_id'] ?? $existingBooking['event_id'];
        if ($this->isDuplicateBooking($userId, $eventId, $id)) {
            throw new Exception("User has already booked this event");
        }

        if (isset($data['status'])) {
            $this->validateStatus($data['status']);
        }

        if (!$this->bookingDao->update($id, $data)) {
            throw new Exception("Failed to update booking");
        }
        return true;
    }

    public function deleteBooking($id) {
        $this->validateId($id);
        
        if (!$this->bookingDao->getById($id)) {
            throw new Exception("Booking with ID $id not found");
        }

        if (!$this->bookingDao->delete($id)) {
            throw new Exception("Failed to delete booking");
        }
        return true;
    }

    public function getBookingsByUserId($userId) {
        $this->validateId($userId, 'user_id');
        return $this->bookingDao->getByUserId($userId);
    }

    private function validateRequired($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new Exception("Field '$field' is required");
            }
        }
    }

    private function validateId($id, $fieldName = 'id') {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid $fieldName: $id");
        }
    }

    private function validateStatus($status) {
        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array(strtolower($status), $validStatuses)) {
            throw new Exception("Invalid status. Must be one of: " . implode(', ', $validStatuses));
        }
    }

    private function isDuplicateBooking($userId, $eventId, $excludeId = null) {
        $sql = "SELECT * FROM bookings WHERE user_id = :user_id AND event_id = :event_id";
        if ($excludeId) {
            $sql .= " AND booking_id != :id";
        }

        $stmt = $this->bookingDao->connection->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':event_id', $eventId);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId);
        }
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
}
?>
