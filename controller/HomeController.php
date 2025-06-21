<?php

class HomeController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

   //lo usamos en todos los metodos
   private function getBaseViewData(): array
   {
      return [
         'usuario' => $_SESSION['user_name'] ?? '',
         'logo_url' => '/public/img/LogoQuizCode.png',
         'foto_perfil' => $_SESSION['foto_perfil'] ?? '',
      ];
   }

   public function show(): void
   {
      $viewData = $this->getBaseViewData();
      $this->view->render("home", $viewData);
      var_dump($_SESSION["id_rol"]);
   }

   public function showAdmin(): void
   {
      $viewData = $this->getBaseViewData();
      $this->view->render("admin", $viewData);
      var_dump($_SESSION["id_rol"]);
   }

   public function showEditor(): void
   {
      $viewData = $this->getBaseViewData();
      $this->view->render("editor", $viewData);
      var_dump($_SESSION["id_rol"]);
   }

   public function playGame(): void
   {
      $viewData = $this->getBaseViewData();
      $this->view->render("partida", $viewData);
   }
}