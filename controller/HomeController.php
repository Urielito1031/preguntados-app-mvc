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
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("home", $viewData); //
    }

    public function playGame(){
//         CREO QUE NO TIENE SENTIDO PORQUE ANTES DE ENTRAR EN ESTE METODO YA DEBE ESTAR EN LA PAGINA,
//         NO HAY OTRO ACCESO SEGUN ENTIENDO
//        if (!isset($_SESSION['user_name'])) {
//            header('Location: /usuario/showLoginForm');
//            exit;
//        }

        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("partida", $viewData); //
    }

    public function pregunta(){
        $viewData = [
            // Datos para el menu desplegable
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil']

        ];
        $this->view->render("pregunta", $viewData);
    }

}