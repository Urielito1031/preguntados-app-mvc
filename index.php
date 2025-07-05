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

   $controller = $_GET["controller"] ?? null;
   $method = $_GET["method"] ?? null;

   $user = $_SESSION["user_name"] ?? null;
   $rol = $_SESSION["id_rol"] ?? null;

    //Si no esta logueado o no se esta registrando se tiene que loguear
    if(!$user && $controller !== 'usuario'){
        $controller = "usuario";
        $method = "showLoginForm";
    }

   // Asignar ruta por defecto según el rol
   if (!$controller && !$method) {
      switch ($rol) {
         case 1:
            $controller = "adminDashboard";
            $method     = "show";
            break;
         case 2:
            $controller = "home";
            $method     = "showEditor";
            break;
         case 3:
            $controller = "home";
            $method     = "show";
            break;
      }
   }
   if ($controller === 'home') {


      if ($method === 'showEditor' && $rol !== 2) {
         $method = 'show';
      }

      if ($method === 'show' && $rol !== 3) {
         $method = 'showEditor';
      }
   }

    /* Fin de Control de rol */

    $router->go($controller, $method);

} catch (PDOException $e) {
   echo "Error de conexión: " . $e->getMessage();
   exit;
}