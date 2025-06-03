<?php

use Service\PreguntaService;
use Entity\Pregunta;

class PartidaController
{
    private $view;
    private $preguntaService;

    public function __construct(PreguntaService $preguntaService, MustachePresenter $view) {
        $this->view = $view;
        $this->preguntaService = $preguntaService;
    }


    private function getUserSessionData() : array {
        return [
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
    }

    public function pregunta(): void {
        if(!isset($_SESSION['preguntas_realizadas'])){
            $_SESSION['preguntas_realizadas'] = array();
        }

        // Miedo me da tocar esto xD
        $idCategoria = rand(1,6);
        $pregunta = $this->preguntaService->getPregunta($idCategoria, $_SESSION['preguntas_realizadas']);
        $respuestas = $pregunta->getRespuestasIncorrectas();
        $respuestas[] = $pregunta->getRespuestaCorrecta();
        shuffle($respuestas);
        $_SESSION['respuestas'] = $respuestas;

        $_SESSION['pregunta'] = [
            'id' => $pregunta->getId(),
            'enunciado' => $pregunta->getEnunciado(),
            'respuesta_correcta' => $pregunta->getRespuestaCorrecta(),
            'respuestas' => $respuestas,
            'categoria' => [
                'id' => $idCategoria,
                'descripcion' => $pregunta->getCategoria()->getDescripcion(),
                'color' => $pregunta->getCategoria()->getColor()
            ]
        ];

        array_push($_SESSION['preguntas_realizadas'], $pregunta->getId());

        $viewData = array_merge($this->getUserSessionData(),[
            'preguntasRealizadas' => $_SESSION['preguntas_realizadas'],
            'pregunta' => $_SESSION['pregunta'],
        ]);

        $this->view->render("pregunta", $viewData);
    }

    public function showPreguntaCorrecta(): void {
        $viewData = array_merge($this->getUserSessionData(),
        [
            'puntaje' => count($_SESSION['preguntas_realizadas']),
            'pregunta' => $_SESSION['pregunta'],
            'respuesta_usuario' => $_POST['respuesta_usuario'],
        ]);
        $this->view->render("respuestacorrecta", $viewData);
    }

    public function endGame(): void {
        $viewData = array_merge($this->getUserSessionData(),[
            'puntaje' => count($_SESSION['preguntas_realizadas']) - 1,
            'pregunta' => $_SESSION['pregunta'],
            'respuesta_usuario' => $_POST['respuesta_usuario']
        ]);

        unset($_SESSION['preguntas_realizadas']);
        $this->view->render("finpartida", $viewData);
    }

    public function responder(): void{
        if($_SESSION['pregunta']['respuesta_correcta'] === $_POST['respuesta_usuario']){
            $this->showPreguntaCorrecta();
        }else{
            $this->endGame();
        }
    }
}