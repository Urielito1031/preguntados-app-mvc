<?php

use Controller\AdminController;
use Graph\JPGraphGenerador;
use Repository\PartidaRepository;
use Repository\PreguntaRepository;
use Repository\SugerenciaPreguntaRepository;
use Repository\UsuarioPreguntaRepository;
use Repository\UsuarioRepository;
use Repository\CategoriaRepository;

use Service\DashboardService;
use Service\ExportarPdfService;
use Service\PartidaService;
use Service\PreguntaService;
use Service\SugerenciaPreguntaService;
use Service\UsuarioPreguntaService;
use Service\UsuarioService;
use Service\CategoriaService;

require_once("model/service/preguntaService.php");
require_once("model/Service/ImageService.php");
require_once("model/Service/UbicacionService.php");
require_once("model/Response/DataResponse.php");

require_once("controller/UsuarioController.php");
require_once("controller/HomeController.php");
require_once("controller/PartidaController.php");
require_once("controller/RankingController.php");
require_once("controller/EditorController.php");
require_once("controller/AdminController.php");
require_once("model/Service/ExportarPdfService.php");
require_once("model/Entity/Usuario.php");


require_once("Model/Service/UsuarioService.php");
require_once("Model/Service/PartidaService.php");
require_once("Model/Repository/UsuarioRepository.php");
require_once("model/Repository/UsuarioPreguntaRepository.php");
require_once("model/Service/UsuarioPreguntaService.php");
require_once("model/Repository/PaisRepository.php");
require_once("model/Repository/CiudadRepository.php");
require_once("model/Repository/CategoriaRepository.php");
require_once("Model/Service/CategoriaService.php");

require_once("model/repository/PreguntaRepository.php");
require_once("core/Router.php");
require_once("core/MustachePresenter.php");
require_once("model/repository/PartidaRepository.php");
require_once("model/Service/DashboardService.php");
require_once("model/Graph/JPGraphGenerador.php");


require_once("model/repository/SugerenciaPreguntaRepository.php");
require_once("model/Service/SugerenciaPreguntaService.php");
require_once("model/Entity/PreguntaSugerida.php");
require_once ("model/Entity/Ubicacion.php");


include_once('vendor/mustache/src/Mustache/Autoloader.php');
include_once('vendor/phpmailer/Exception.php');
include_once('vendor/phpmailer/PHPMailer.php');
include_once('vendor/phpmailer/SMTP.php');

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

         $sugerenciaPreguntaRepository = new SugerenciaPreguntaRepository();
         $sugerenciaPreguntaService = new SugerenciaPreguntaService($sugerenciaPreguntaRepository);
        return new HomeController($sugerenciaPreguntaService,$this->getViewer());

    }

   public function getPartidaController() {
      // Repositorios
      $preguntaRepository = new PreguntaRepository();
      $partidaRepository = new PartidaRepository();
      $usuarioPreguntaRepository = new UsuarioPreguntaRepository();
      $usuarioRepository = new UsuarioRepository();

      // Servicios
      $usuarioService = new UsuarioService($usuarioRepository);
      $preguntaService = new PreguntaService($preguntaRepository,$usuarioRepository);
      $partidaService = new PartidaService($partidaRepository);
      $usuarioPreguntaService = new UsuarioPreguntaService(
         $usuarioPreguntaRepository,
         $usuarioService,
         $preguntaService
      );

      return new PartidaController(
         $partidaService,
         $usuarioService,
         $preguntaService,
         $usuarioPreguntaService,
         $this->getViewer()
      );
   }


   public function getRankingController(){
       $repository = new UsuarioRepository();
       $service = new UsuarioService($repository);
       return new RankingController($service, $this->getViewer());
    }

    public function getEditorController(){


        $preguntaRepository = new PreguntaRepository();
        $usuarioRepository = new UsuarioRepository();
        $preguntaService = new PreguntaService($preguntaRepository,$usuarioRepository);
        $categoriaRepository = new CategoriaRepository();
        $categoriaService = new CategoriaService($categoriaRepository);
        $sugerenciaPreguntaRepository = new SugerenciaPreguntaRepository();
        $sugerenciaPreguntaService = new SugerenciaPreguntaService($sugerenciaPreguntaRepository);
        return new EditorController($this->getViewer(),$preguntaService,$categoriaService,$sugerenciaPreguntaService);
    }



   public function getDashboardService(): DashboardService
   {
      return new DashboardService(
         new PartidaRepository(),
         new PreguntaRepository(),
         new UsuarioRepository(),
         $this->getGraphGenerator()
      );
   }

   public function getExportarPdfService(): ExportarPdfService
   {
      return new ExportarPdfService();
   }

   public function getGraphGenerator(): JPGraphGenerador
   {
      return new JPGraphGenerador();
   }
   public function getAdminController()
   {
      $dashboardService = $this->getDashboardService();
      $exportarPdfService = $this->getExportarPdfService();

      return new AdminController($this->getViewer(), $dashboardService, $exportarPdfService);
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
