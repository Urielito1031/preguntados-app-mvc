<?php

namespace Registry;

use Entity\Nivel;
use PDO;

require_once __DIR__ . '/../Entity/Nivel.php';

class NivelRegistry
{
   private static array $niveles = [];

   //bloquea una posible instancia
   private function __construct() {}


   public static function init(PDO $conn):void
   {
      $stmt = $conn->query("SELECT * FROM nivel");
      foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $data) {
         self::$niveles[$data['id_nivel']] = new Nivel($data);
      }



   }
   public static function get(int $id): ?Nivel{
      if (isset(self::$niveles[$id])) {
         return self::$niveles[$id];
      }
      return null;
   }
   public static function getAll(): array
   {
      return self::$niveles;
   }


}