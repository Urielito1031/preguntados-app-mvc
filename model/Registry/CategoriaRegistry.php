<?php

namespace Registry;

use Entity\Categoria;
use PDO;

require_once __DIR__ . '/../Entity/Categoria.php';

class CategoriaRegistry
{
   private static $categorias = [];

   //bloquea una posible instancia
   private function __construct() {}

   public static function init(PDO $conn):void
   {
      $stmt = $conn->query("SELECT * FROM categoria");
      foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $data) {
         self::$categorias[$data['id']] = new Categoria($data);
      }

   }
   public static function get(int $id): ?Categoria
   {
      if (isset(self::$categorias[$id])) {
         return self::$categorias[$id];
      }
      return null;
   }
   public static function getAll(): array
   {
      return self::$categorias;
   }

}