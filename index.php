<?php
session_start();

use Config\Database;

require_once("Configuration.php");
require_once("configuration/Database.php");

require_once("model/Service/ImageService.php");
require_once("model/Service/UbicacionService.php");
require_once("model/Service/UsuarioService.php");

require_once("model/Repository/PaisRepository.php");
require_once("model/Repository/CiudadRepository.php");
require_once("model/Repository/UsuarioRepository.php");

require_once("model/Response/DataResponse.php");

require_once("controller/UsuarioController.php");

require_once("controller/PartidaController.php");

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