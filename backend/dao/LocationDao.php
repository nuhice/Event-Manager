<?php
require_once __DIR__ . '/BaseDao.php';

class LocationDAO extends BaseDao {
    public function __construct() {
        parent::__construct('locations');
    }

    public function getByCity($city) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE city = :city"
        );
        $stmt->bindParam(':city', $city);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
