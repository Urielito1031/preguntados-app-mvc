<?php

namespace Repository;
require_once __DIR__ . "/../Entity/Ciudad.php";

use Config\Database;
use Entity\Ciudad;
use PDO;
use PDOException;

class CiudadRepository
{
   private PDO $conn;

   public function __construct()
   {
      $this->conn = Database::connect();
   }

   public function findOrCreate(int $idPais , string $nombreCiudad): Ciudad
   {
      $ciudad = $this->findByNombreYPais($nombreCiudad, $idPais);

      if ($ciudad !== null) {
         return $ciudad;
      }

      return $this->create($nombreCiudad, $idPais);
   }

   private function findByNombreYPais(string $nombre, int $idPais): ?Ciudad
   {
      $sql = "SELECT * FROM ciudad 
               WHERE nombre = :nombre 
               AND id_pais = :id_pais";

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
      $stmt->bindValue(':id_pais', $idPais, PDO::PARAM_INT);
      $stmt->execute();

      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      return $data ? new Ciudad($data) : null;
   }

   private function create(string $nombre, int $idPais): Ciudad
   {
      $sql = "INSERT INTO ciudad (nombre, id_pais) 
               VALUES (:nombre, :id_pais)";

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
      $stmt->bindValue(':id_pais', $idPais, PDO::PARAM_INT);
      $stmt->execute();

      return new Ciudad([
         'id' => $this->conn->lastInsertId(),
         'nombre' => $nombre,
         'id_pais' => $idPais
      ]);
   }

   public function obtenerIdPais(int $idCiudad) {
       $sql = "SELECT id_pais FROM ciudad 
               WHERE id= :id_ciudad";

       $stmt = $this->conn->prepare($sql);
       $stmt->bindValue(':id_ciudad', $idCiudad, PDO::PARAM_INT);
       $stmt->execute();

       $idPais = (int) $stmt->fetchColumn();

       return $idPais;
   }
   public function buscarCiudadPorIDPais(int $idPais): ?Ciudad
    {
        $sql = "SELECT * FROM ciudad 
               WHERE id_pais = :id_pais";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_pais', $idPais, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Ciudad($data) : null;
    }

    public function findById($getIdCiudad)
    {
        $sql = "SELECT * FROM ciudad WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $getIdCiudad, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Ciudad($data) : null;
    }


}