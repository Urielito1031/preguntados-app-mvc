<?php

use Config\Database;

require_once("Configuration.php");
require_once("configuration/Database.php");

try {
   $pdo = Database::connect();
   $viewer = new MustachePresenter("view");
   $configuration = new Configuration($pdo, $viewer);
   $router = $configuration->getRouter();

   $router->go(
      $_GET["controller"],
      $_GET["method"]
   );
} catch (PDOException $e) {
   echo "Error de conexión: " . $e->getMessage();
   exit;
}