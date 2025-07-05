<?php
session_start();


use Config\Database;

require_once("Configuration.php");
require_once("configuration/Database.php");

$permisos = parse_ini_file("configuration/permisos.ini", true);
try {
   $pdo = Database::connect();
   $viewer = new MustachePresenter("view");
   $configuration = new Configuration($pdo, $viewer);
   $router = $configuration->getRouter();

   $controller = $_GET["controller"] ?? null;
   $method     = $_GET["method"] ?? null;
   $user       = $_SESSION["user_name"] ?? null;
   $rol        = $_SESSION["id_rol"] ?? null;



   $rolNombre = match ($rol)
   {
      1 => "admin",
      2 => "editor",
      3 => "jugador",
      default => 'no_logueado'
   };
   $rolPermisos = $permisos[$rolNombre] ?? [];

   // Si no hay controlador ni método, usar los por defecto del rol
   if (!$controller || !$method) {
      $controller = $rolPermisos['defaultController'];
      $method     = $rolPermisos['defaultMethod'];
   }

   //valido si el controlador está permitido para el rol
   $controllersPermitidos = $rolPermisos['controllers'] ?? [];
   if (!in_array($controller, $controllersPermitidos)) {
      // Si el controlador no está permitido, redirigir al controlador por defecto
      $controller = $rolPermisos['defaultController'];
      $method = $rolPermisos['defaultMethod'];
   }

    $router->go($controller, $method);

} catch (PDOException $e) {
   echo "Error de conexión: " . $e->getMessage();
   exit;
}