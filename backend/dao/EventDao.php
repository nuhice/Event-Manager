<?php
require_once __DIR__ . '/BaseDao.php';

class EventDAO extends BaseDao {
    public function __construct() {
        parent::__construct('events', 'event_id');
    }

    public function getByName($name) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE name = :name"
        );
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
