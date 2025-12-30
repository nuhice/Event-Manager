<?php
require_once __DIR__ . '/../config/config.php';

class BaseDao {
   protected $table;
   public $connection;
   protected $primaryKey;


   public function __construct($table, $primaryKey = 'id') {
       $this->table = $table;
       $this->connection = Database::connect();
       $this->primaryKey = $primaryKey;
   }


   public function getAll() {
       $stmt = $this->connection->prepare("SELECT * FROM " . $this->table);
       $stmt->execute();
       return $stmt->fetchAll();
   }


   public function getById($id) {
       $stmt = $this->connection->prepare("SELECT * FROM " . $this->table . " WHERE " . $this->primaryKey . " = :id");
       $stmt->bindParam(':id', $id);
       $stmt->execute();
       return $stmt->fetch();
   }


   public function insert($data) {
       $columns = implode(", ", array_keys($data));
       $placeholders = ":" . implode(", :", array_keys($data));
       $sql = "INSERT INTO " . $this->table . " ($columns) VALUES ($placeholders)";
       $stmt = $this->connection->prepare($sql);
       $success = $stmt->execute($data);
       if ($success) {
           try {
               return (int)$this->connection->lastInsertId();
           } catch (Exception $e) {
               return true;
           }
       }
       return false;
   }


   public function update($id, $data) {
       $fields = "";
       foreach ($data as $key => $value) {
           $fields .= "$key = :$key, ";
       }
       $fields = rtrim($fields, ", ");
       $sql = "UPDATE " . $this->table . " SET $fields WHERE " . $this->primaryKey . " = :id";
       $stmt = $this->connection->prepare($sql);
       $data['id'] = $id;
       return $stmt->execute($data);
   }


   public function delete($id) {
       $stmt = $this->connection->prepare("DELETE FROM " . $this->table . " WHERE " . $this->primaryKey . " = :id");
       $stmt->bindParam(':id', $id);
       return $stmt->execute();
   }
}
?>