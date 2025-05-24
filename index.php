<?php
require_once("Configuration.php");
require_once("configuration/Database.php");

try {
   $pdo = Config\Database::connect();
   $configuration = new Configuration($pdo);
   $router = $configuration->getRouter();

   $router->go(
      $_GET["controller"],
      $_GET["method"]
   );
} catch (PDOException $e) {
   echo "Error de conexión: " . $e->getMessage();
   exit;
}