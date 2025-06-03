<?php

use Repository\PreguntaRepository;
use Repository\UsuarioRepository;
use Service\PreguntaService;
use Service\UsuarioService;

require_once("Model/Service/UsuarioService.php");
require_once("model/service/preguntaService.php");
require_once("model/Service/ImageService.php");
require_once("model/Service/UbicacionService.php");
require_once("model/Response/DataResponse.php");
require_once("controller/UsuarioController.php");
require_once("controller/HomeController.php");
require_once("controller/PartidaController.php");
require_once("model/Entity/Usuario.php");
require_once("Model/Repository/UsuarioRepository.php");
require_once("model/Repository/PaisRepository.php");
require_once("model/Repository/CiudadRepository.php");
require_once("model/repository/PreguntaRepository.php");
require_once("core/Router.php");
require_once("core/MustachePresenter.php");
include_once('vendor/mustache/src/Mustache/Autoloader.php');

class Configuration
{
   private PDO $database;
   private $viewer;

   public function __construct(PDO $database, MustachePresenter $viewer)
   {
      $this->database = $database;
      $this->viewer = $viewer;
   }
   public function getDatabase(): PDO
   {
      return $this->database;
   }

   public function getUsuarioController()
   {
      $repository = new UsuarioRepository();
      $service = new UsuarioService($repository);
      return new UsuarioController($service, $this->getViewer());
   }

    public function getHomeController()
    {
        return new HomeController($this->getViewer());
    }

    public function getPartidaController(){
       $repository = new PreguntaRepository();
       $service = new PreguntaService($repository);
       return new PartidaController($service, $this->getViewer());
    }

    public function getRouter()
    {
        return new Router("getHomeController", "show", $this);
    }

    public function getViewer()
    {
        //return new FileView();
        return new MustachePresenter("view");
    }
}
