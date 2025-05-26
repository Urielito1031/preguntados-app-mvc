<?php

namespace Repository;

require_once __DIR__ . "/../Entity/Pais.php";
use Config\Database;
use Entity\Pais;
use PDO;
use PDOException;

class PaisRepository
{
   private PDO $conn;

   public function __construct()
   {
      $this->conn = Database::connect();
   }

   public function findOrCreate(string $nombrePais): Pais
   {
      $pais = $this->findByNombre($nombrePais);

      if ($pais !== null) {
         return $pais;
      }

      return $this->create($nombrePais);
   }

   private function findByNombre(string $nombre): ?Pais
   {
      $sql = "SELECT * FROM pais WHERE nombre = :nombre";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
      $stmt->execute();

      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      return $data ? new Pais($data) : null;
   }

   private function create(string $nombre): Pais
   {
      $sql = "INSERT INTO pais (nombre) VALUES (:nombre)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
      $stmt->execute();

      $id = $this->conn->lastInsertId();
      $data = $this->findById($id);
      $data->setId($this->conn->lastInsertId());

      return $this->findById($id);
   }

   private function findById(int $id): ?Pais
   {
      $sql = "SELECT * FROM pais WHERE id = :id";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      return $data ? new Pais($data) : null;
   }
}