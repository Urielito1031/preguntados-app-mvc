<?php

namespace Controller;

use Entity\Usuario;
use MustachePresenter;
use Service\PartidaService;
use Service\PreguntaService;
use Service\UsuarioService;

class PartidaController
{
   private $view;
   private $preguntaService;
   private PartidaService $service;

   public function __construct(PartidaService $service, PreguntaService $preguntaService, MustachePresenter $view)
   {
      $this->view = $view;
      $this->service = $service;
      $this->preguntaService = $preguntaService;

   }

   public function playGame()
   {
      $_SESSION['preguntas_realizadas'] = [];
      $_SESSION['preguntas_correctas'] = [];
      $viewData = [
         'usuario' => $_SESSION['user_name'] ?? '',
         'logo_url' => '/public/img/LogoQuizCode.png',
         'foto_perfil' => $_SESSION['foto_perfil']
      ];
      $this->view->render("partida", $viewData);
   }

   public function endGame()
   {
      try {

         $response = $this->service->finalizarPartida(
            $_SESSION['user_id']?? 0,
            $_SESSION['preguntas_correctas'] ?? [],
            $_SESSION['preguntas_realizadas'] ?? []
         );

         // Preparar datos para la vista
         $viewData = [
            'puntaje' => $response->success ? $response->data->getPuntaje() : 0,
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'],
            'enunciado' => $_SESSION['enunciado_actual'] ?? '',
            'respuesta_correcta' => $_SESSION['respuesta_correcta'] ?? '',
            'respuestas' => $_SESSION['respuestas'] ?? [],
            'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? ''
         ];

         if (!$response->success) {
            $viewData['error'] = $response->message;
         } else {
            $viewData['success'] = "Partida guardada correctamente";
         }

         // Limpiar la sesión
         unset($_SESSION['preguntas_realizadas'], $_SESSION['preguntas_correctas'], $_SESSION['respuesta_correcta'],
            $_SESSION['enunciado_actual'], $_SESSION['respuestas'], $_SESSION['respuesta_usuario'], $_SESSION['pregunta_actual']);

         $this->view->render("finpartida", $viewData);
      } catch (\Exception $e) {
         $viewData = [
            'error' => "Error al finalizar la partida: " . $e->getMessage(),
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil']
         ];
         $this->view->render("error", $viewData);
      }
   }

   public function pregunta()
   {
      try {
         if (!isset($_GET['categoria']) || !in_array($_GET['categoria'], ['historia', 'deporte', 'arte', 'ciencia', 'geografia', 'Entrenamiento', 'Aleatorio'])) {
            throw new \Exception("Categoría inválida");
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

         $_SESSION['preguntas_realizadas'] ??= [];

         $pregunta = $this->preguntaService->getPregunta($idCategoria, $_SESSION['preguntas_realizadas']);
         if (!$pregunta) {
            $this->endGame();
            return;
         }

         $respuestas = $pregunta->getRespuestasIncorrectas();
         $respuestas[] = $pregunta->getRespuestaCorrecta();
         shuffle($respuestas);

         $_SESSION['respuestas'] = $respuestas;
         $_SESSION['respuesta_correcta'] = $pregunta->getRespuestaCorrecta();
         $_SESSION['pregunta_actual'] = $pregunta->getId();
         $_SESSION['enunciado_actual'] = $pregunta->getEnunciado();
         $_SESSION['preguntas_realizadas'][] = $pregunta->getId();

         $viewData = [
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'],
            'categoria' => $pregunta->getCategoria()->getDescripcion() ?? '',
            'enunciado' => $pregunta->getEnunciado() ?? '',
            'respuestas' => $respuestas,
            'respuestaCorrecta' => $pregunta->getRespuestaCorrecta(),
            'preguntasRealizadas' => $_SESSION['preguntas_realizadas'],
            'categoria_color' => $pregunta->getCategoria()->getColor() ?? '',
         ];

         $this->view->render("pregunta", $viewData);
      } catch (\Exception $e) {
         $viewData = [
            'error' => "Error al cargar la pregunta: " . $e->getMessage(),
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil']
         ];
         $this->view->render("error", $viewData);
      }
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

   public function showPreguntaCorrecta()
   {
      $viewData = [
         'usuario' => $_SESSION['user_name'] ?? '',
         'foto_perfil' => $_SESSION['foto_perfil'],
         'enunciado_actual' => $_SESSION['enunciado_actual'] ?? '',
         'enunciado' => $_SESSION['enunciado_actual'] ?? '',
         'respuestas' => $_SESSION['respuestas'] ?? [],
         'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? ''
      ];
      $this->view->render("respuestacorrecta", $viewData);
   }
}