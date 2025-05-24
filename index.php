<?php
require_once("Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();

$router->go(
   $_GET["controller"],
   $_GET["method"]
);