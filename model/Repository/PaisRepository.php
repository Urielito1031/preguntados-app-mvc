<?php

namespace Repository;

use Config\Database;
use Entity\Pais;
use PDO;

class PaisRepository
{
   private PDO $conn;

   public function __construct(){
      $this->conn=Database::connect();
   }

   public function findOrCreate(string $nombre): Pais
   {

   }

}