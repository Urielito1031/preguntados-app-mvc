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
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("home", $viewData); //
        var_dump($_SESSION["id_rol"]);
    }

    public function showAdmin()
    {
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("admin", $viewData); //
        var_dump($_SESSION["id_rol"]);
    }

    public function showEditor()
    {
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("editor", $viewData); //
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

    public function questionRequest() {
        //$_SESSION['user_id']
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil'],
            'id_usuario' => $_SESSION['user_id']
        ];
        $this->view->render("solicitudPregunta", $viewData);
    }

    public function requestSubmit() {
       $idCategoriaSeleccionada =  $_POST['categoria'] ?? null;

       $respuestaCorrecta = $_POST['repuesta_correcta'] ?? null;

       $respuestas = $_POST['respuestas'] ?? [];

       //SE RECIBEN BIEN
       var_dump($idCategoriaSeleccionada);
        var_dump($respuestaCorrecta);
        var_dump($respuestas);

    }
}