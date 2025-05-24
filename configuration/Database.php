<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
   public static function connect():PDO{
      //ruta relativa al .ini
      $config = parse_ini_file(__DIR__ . '/config.ini',true);
      $host = $config['database']['host'];
      $db = $config['database']['dbname'];
      $user = $config['database']['user'];
      $pass = $config['database']['password'];
      try{
         $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
         $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
         ];
         return new PDO($dsn, $user, $pass, $options);
      }catch(PDOException $e){
         echo "Error de conexión: " . $e->getMessage();
         exit;
      }


   }

}