<?php
require_once __DIR__ . '/BaseDao.php';

class BookingDAO extends BaseDao {
    public function __construct() {
        parent::__construct('bookings');
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = :user_id"
        );
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
