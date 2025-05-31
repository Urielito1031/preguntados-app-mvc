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

    public function pregunta(){
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

        $viewData = [
            // Datos para el menu desplegable
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'],

            //datos para la pregunta
            'categoria' => $pregunta->getCategoria()->getDescripcion() ?? '',
            'enunciado' => $pregunta->getEnunciado() ?? '',
            'respuestas' => $respuestas,
            'categoria_color' => $pregunta->getCategoria()->getColor() ?? '',
        ];
        $this->view->render("pregunta", $viewData);
    }
}