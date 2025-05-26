<?php
session_start();

use Config\Database;

require_once("Configuration.php");
require_once("configuration/Database.php"); //

try {
    $pdo = Database::connect(); //
    $viewer = new MustachePresenter("view"); //
    $configuration = new Configuration($pdo, $viewer);
    $router = $configuration->getRouter(); //

    // Asignar un controlador y método por defecto si no están en la URL
    $controller = $_GET["controller"] ?? 'home';
    $method = $_GET["method"] ?? 'show';

    $router->go($controller, $method); //

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage(); //
    exit;
}