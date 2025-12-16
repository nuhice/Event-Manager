<?php
require_once __DIR__ . '/BaseDao.php';

class UserDAO extends BaseDao {
    public function __construct() {
        parent::__construct('users', 'user_id');
    }

    public function getByEmail($email) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE email = :email"
        );
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
