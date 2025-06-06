<?php
session_start();

use Config\Database;

require_once("Configuration.php");
require_once("configuration/Database.php");

try {
   $pdo = Database::connect();
   $viewer = new MustachePresenter("view");
   $configuration = new Configuration($pdo, $viewer);
   $router = $configuration->getRouter();

   $controller = (isset($_GET["controller"])) ? $_GET["controller"] : null;
   $method = (isset($_GET["method"])) ? $_GET["method"] : null;

    //Si no esta logueado o no se esta registrando se tiene que loguear
    if(!isset($_SESSION["user_name"]) && $_GET["controller"] !== 'usuario'){
        $controller = "usuario";
        $method = "showLoginForm";
    }

    $router->go($controller, $method);

} catch (PDOException $e) {
   echo "Error de conexión: " . $e->getMessage();
   exit;
}