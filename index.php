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

    /* Control de rol*/

    //Roles: 1 Admin, 2 Editor , 3 Jugador
    $rol = (isset($_SESSION["id_rol"])) ? $_SESSION["id_rol"] : null;

    //Admin, editor y jugar tienen sus propias vistas show
    if($controller == 'home' && $method == 'showAdmin' && $rol != 1){
        $method = $rol == 2 ? 'showEditor' : 'show';
    }

    if($controller == 'home' && $method == 'showEditor' && $rol != 2){
        $method = $rol == 1 ? 'showAdmin' : 'show';
    }

    if($controller == 'home' && $method == 'show' && $rol != 3){
        $method = $rol == 1 ? 'showAdmin' : 'showEditor';
    }

    /* Fin de Control de rol */

    $router->go($controller, $method);

} catch (PDOException $e) {
   echo "Error de conexión: " . $e->getMessage();
   exit;
}