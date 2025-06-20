<?php

namespace Repository;

use Config\Database;
use PDO;
use PDOException;

class CategoriaRepository{

    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function getAll(){
        $query = "SELECT * FROM categoria";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
}