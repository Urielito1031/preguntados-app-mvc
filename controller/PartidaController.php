<?php

use Service\PreguntaService;

class PartidaController
{
    private $view;
    private $preguntaService;

    public function __construct(PreguntaService $preguntaService, MustachePresenter $view) {
        $this->view = $view;
        $this->preguntaService = $preguntaService;
    }

    public function playGame(){
        $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'logo_url' => '/public/img/LogoQuizCode.png',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
        $this->view->render("partida", $viewData);
    }

    public function endGame(){
        //aca recopila info y hace las llamadas para guardar la partida, recupera los datos para mostrarlo en resumen de partida

        //$this->partidaService->finalizarPartida();
        $puntaje = count($_SESSION['preguntas_realizadas']) - 1;
        $viewData = [
            'puntaje' => $puntaje
        ];

        unset($_SESSION['preguntas_realizadas']);
        $this->view->render("finpartida",$viewData);
    }
    public function pregunta(){
        if(!isset($_SESSION['preguntas_realizadas'])){
            $_SESSION['preguntas_realizadas'] = array();
        }

        $idCategoria = match ($_GET['categoria']) {
            "historia" => 1,
            "deporte" => 2,
            "arte" => 3,
            "ciencia" => 4,
            "geografia" => 5,
            "Entrenamiento" => 6,
            "Aleatorio" => 7,
        };

        $pregunta = $this->preguntaService->getPregunta($idCategoria);
        $respuestas = $pregunta->getRespuestasIncorrectas();
        $respuestas[] = $pregunta->getRespuestaCorrecta();
        shuffle($respuestas);

        array_push($_SESSION['preguntas_realizadas'], $pregunta->getId());

        $_SESSION['respuesta_correcta'] = $pregunta->getRespuestaCorrecta();

        $_SESSION['pregunta_actual'] = $pregunta->getId();
        $_SESSION['enunciado_actual'] = $pregunta->getEnunciado();

        $respuestaCorrecta = $pregunta->getRespuestaCorrecta();

        $viewData = [
            // Datos para el menu desplegable
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'],

            //datos para la pregunta
            'categoria' => $pregunta->getCategoria()->getDescripcion() ?? '',
            'enunciado' => $pregunta->getEnunciado() ?? '',
            'respuestas' => $respuestas,
            'respuestaCorrecta' => $respuestaCorrecta,
            'preguntasRealizadas' => $_SESSION['preguntas_realizadas']


        ];
        $this->view->render("pregunta", $viewData);
    }

    public function showPreguntaCorrecta(){
        $viewData = [
            // Datos para el menu desplegable
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'],

            //datos para la mostrar
            'enunciado_actual' => $_SESSION['enunciado_actual'],
            'respuesta_correcta' => $_SESSION['respuesta_correcta']
        ];
        $this->view->render("respuestacorrecta", $viewData);
    }
    public function responder(){

        $continuaLaPartida = false;

        if($_POST['respuesta'] == $_SESSION['respuesta_correcta']){
            //deberia mostrar una vista que permita reportar pregunta
            $continuaLaPartida = true;
        }else{
            $continuaLaPartida = false;
        }

        if($continuaLaPartida){
            $this->showPreguntaCorrecta();
        }else{
            $this->endGame();
        }
    }
}