<?php

use Service\SugerenciaPreguntaService;

class HomeController
{
    private $view;
    private SugerenciaPreguntaService  $sugerenciaPreguntaService;
    public function __construct(SugerenciaPreguntaService $sugerenciaPreguntaService, MustachePresenter $view)
    {
        $this->view = $view;
        $this->sugerenciaPreguntaService = $sugerenciaPreguntaService;
    }

    public function show()
    {
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("home", $viewData);
    }

    public function showAdmin()
    {
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("admin", $viewData);
    }

    public function showEditor()
    {
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("editor", $viewData);
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
       $idCategoria =  $_POST['categoria'] ?? null;
       $enunciado = $_POST['enunciado'] ?? null;
       $posicionArrrayRespuestaCorrecta = $_POST['repuesta_correcta'] ?? null;
       $respuestas = $_POST['respuestas'] ?? [];

        //SE RECIBEN BIEN
        var_dump($idCategoria);
        var_dump($enunciado);
        var_dump($posicionArrrayRespuestaCorrecta);
        var_dump($respuestas);

       // CREAR PREGUNTA EN EL SERVICIO
       $this->sugerenciaPreguntaService->crearPregunta($idCategoria,$enunciado,$respuestas,$posicionArrrayRespuestaCorrecta);

       // RETORNAR UNA VISTA DE 'SE CREO SUGERENCIA DE PREGUNTA SATISFACTORIAMENTE'


    }
}