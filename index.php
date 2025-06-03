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


try {
   $pdo = Database::connect();
   $viewer = new MustachePresenter("view");
   $configuration = new Configuration($pdo, $viewer);
   $router = $configuration->getRouter();

   $controller = (isset($_GET["controller"])) ? $_GET["controller"] : null;
   $method = (isset($_GET["method"])) ? $_GET["method"] : null;

   $router->go($controller, $method);

} catch (PDOException $e) {
   echo "Error de conexión: " . $e->getMessage();
   exit;
}