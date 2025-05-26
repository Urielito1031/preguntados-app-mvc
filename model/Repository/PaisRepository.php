<?php

namespace Repository;

use Config\Database;
use Entity\Pais;
use PDO;
use PDOException;

class PaisRepository
{
   private PDO $conn;

   public function __construct(){
      $this->conn=Database::connect();
   }

   public function findOrCreate(string $nombrePais):Pais {
      $sql = "SELECT * FROM pais WHERE nombre=:nombrePais";
      try{
      $stmt = $this->conn->prepare($sql);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
          echo $e->getMessage();
      }
      if($row != null){
          return new Pais($row);
      }
        try{
       $sql = "INSERT INTO pais (nombre) VALUES (:nombrePais)";
       $stmt = $this->conn->prepare($sql);
       $stmt->bindValue(':nombre', $nombrePais);
       $stmt->execute();}
      catch(PDOException $e){
          echo $e->getMessage();
      }
       $id = $this->conn->lastInsertId();
       return new Pais(['id'=>$id,'nombre'=>$nombrePais]);
//     $row = $stmt->fetch(PDO::FETCH_ASSOC);


   }

}