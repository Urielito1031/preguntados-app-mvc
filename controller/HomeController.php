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

        switch ($viewData['id_rol']) {
            case 2:
                $this->view->render("homeEditor", $viewData);
                break;
            case 1:
                $this->view->render("homeAdmin", $viewData);
                break;
            default:
                $this->view->render("home", $viewData);
                break;
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


