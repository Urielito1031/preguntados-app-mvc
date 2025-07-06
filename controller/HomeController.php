<?php

use Service\SugerenciaPreguntaService;

class HomeController
{
    private $view;
    public function __construct(MustachePresenter $view)
    {
        $this->view = $view;
    }

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




    public function playGame(){
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("partida", $viewData); //
    }



}