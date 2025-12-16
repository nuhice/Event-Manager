<?php
require_once __DIR__ . '/BaseDao.php';

class ContactDAO extends BaseDao {
    public function __construct() {
        parent::__construct('contacts', 'contact_id');
    }

    public function getByEmail($email) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE email = :email"
        );
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
