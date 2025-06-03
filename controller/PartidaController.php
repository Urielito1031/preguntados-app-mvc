<?php

use Service\PartidaService;
use Service\PreguntaService;

class PartidaController
{
   private $view;
   private $preguntaService;
   private $service;

   public function __construct(PartidaService $service, PreguntaService $preguntaService, MustachePresenter $view)
   {
      $this->view = $view;
      $this->service = $service;
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

        if (!$pregunta) {
            $this->endGame();
            return;
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

    public function responder()
    {
        try {

            $respuesta = $_POST['respuesta'] ?? '';
            if (empty($respuesta)) {
                throw new \Exception("No se proporcionó una respuesta");
            }

            $_SESSION['respuesta_usuario'] = $respuesta;


            if(!isset($_SESSION['pregunta_actual']) || !isset($_SESSION['respuesta_correcta'])) {
                $this->endGame();
                return;
            }
            $continuaLaPartida = $respuesta === $_SESSION['respuesta_correcta'];

            if ($continuaLaPartida) {
                $_SESSION['preguntas_correctas'][] = $_SESSION['pregunta_actual'];
                $this->showPreguntaCorrecta();
            } else {
                $this->endGame();
            }
        } catch (\Exception $e) {
            $viewData = [
                'error' => "Error al procesar la respuesta: " . $e->getMessage(),
                'usuario' => $_SESSION['user_name'] ?? '',
                'foto_perfil' => $_SESSION['foto_perfil']
            ];
            $this->view->render("error", $viewData);
        }
    }
}