<?php

class HomeController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function show()
    {
        if (!isset($_SESSION['user_name'])) {
            header('Location: /usuario/showLoginForm');
            exit;
        }

        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil'],
            'id_rol' => $_SESSION['id_rol']
        ];

        if($viewData['id_rol'] === 2) {
            $this->view->render("homeEditor", $viewData);
        } else if ($viewData['id_rol'] === 1) {
            $this->view->render("homeAdmin", $viewData);
        } else {
            $this->view->render("home", $viewData);
            var_dump($_SESSION['id_rol']);
        }
    }


    public function playGame(){

        if (!isset($_SESSION['user_name'])) {
            header('Location: /usuario/showLoginForm');
            exit;
        }

        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("partida", $viewData); //
    }



}


