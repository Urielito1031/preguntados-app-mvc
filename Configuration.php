<?php

use Config\Database;

require_once("core/Database.php");
require_once("core/FilePresenter.php");
require_once("core/MustachePresenter.php");
require_once("core/Router.php");

require_once("controller/HomeController.php");
require_once("controller/GroupController.php");
require_once("controller/SongController.php");
require_once("controller/TourController.php");

require_once("model/GroupModel.php");
require_once("model/SongModel.php");
require_once("model/TourModel.php");

include_once('vendor/mustache/src/Mustache/Autoloader.php');

class Configuration
{

   private $database ;

   public function __construct(PDO $database){
      $this->database = $database;
   }

   public function getDatabase(): PDO
   {
      return $this->database;
   }

   public function getLoginController()
   {
      return new UsuarioController($this->getViewer());
   }


//    public function getSongController()
//    {
//        return new SongController(
//            new SongModel($this->getDatabase()),
//            $this->getViewer()
//        );
//    }
//
//    public function getTourController()
//    {
//        return new TourController(
//            new TourModel($this->getDatabase()),
//            $this->getViewer()
//        );
//    }
//
//    public function getHomeController()
//    {
//        return new HomeController($this->getViewer());
//    }
//
//
//    public function getGroupController()
//    {
//        return new GroupController(new GroupModel($this->getDatabase()), $this->getViewer());
//    }

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