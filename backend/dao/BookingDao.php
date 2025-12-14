<?php
require_once __DIR__ . '/BaseDao.php';

class BookingDAO extends BaseDao {
    public function __construct() {
        parent::__construct('bookings', 'booking_id');
    }

    public function getAll() {
        $sql = "SELECT b.*, u.name as user_name, u.email as user_email, e.title as event_title 
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN events e ON b.event_id = e.event_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUserId($user_id) {
        $sql = "SELECT b.*, u.name as user_name, u.email as user_email, e.title as event_title, e.start_date, e.location_id
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN events e ON b.event_id = e.event_id
                WHERE b.user_id = :user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
