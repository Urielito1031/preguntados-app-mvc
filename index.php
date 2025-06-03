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

    $controller = $_GET["controller"];
    $method = $_GET["method"];

    if($controller === "home" && $method === "playGame" && !isset($_SESSION['user_name'])){
        header('Location: /usuario/showLoginForm');
        exit;
    }

    $router->go($controller, $method);

} catch (PDOException $e) {
   echo "Error de conexión: " . $e->getMessage();
   exit;
}