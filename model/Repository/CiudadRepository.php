<?php

namespace Repository;

use Config\Database;
use Entity\Ciudad;
use Entity\Pais;
use PDO;

class CiudadRepository
{
    private PDO $conn;

    public function __construct(){
        $this->conn=Database::connect();
    }

    public function findOrCreate(int $id_pais, string $nombreCiudad):Ciudad {
        $sql = "SELECT * FROM ciudad WHERE id_pais=:id_pais AND nombre=:nombreCiudad";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row != null){
            return new Pais($row);
        }

        $sql = "INSERT INTO ciudad (nombre,id_pais) VALUES (:nombreCiudad,:id_pais)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':nombreCiudad', $nombreCiudad);
        $stmt->bindValue(':id_pais', $id_pais);
        $stmt->execute();
        $id = $this->conn->lastInsertId();
        return new Ciudad(["id"=>$id,"nombre"=>$nombreCiudad,"id_pais"=>$id_pais]);
//     $row = $stmt->fetch(PDO::FETCH_ASSOC);


    }

}