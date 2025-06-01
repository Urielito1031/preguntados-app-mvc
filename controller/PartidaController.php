<?php

use Entity\Partida;
use Entity\Usuario;
use Service\PartidaService;
use Service\PreguntaService;

class PartidaController
{
    private $view;
    private $preguntaService;
    private PartidaService $service;

    public function __construct(PartidaService $service,PreguntaService $preguntaService, MustachePresenter $view) {
        $this->view = $view;
         $this->service = $service;

        $this->preguntaService = $preguntaService;
    }

    public function playGame(){
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
         // Calcular puntaje y estado
         $puntaje =  count($_SESSION['preguntas_correctas'] ?? []) * 10;
         $estado = count($_SESSION['preguntas_correctas'] ?? []) > 0 ? 'GANADA' : 'PERDIDA';
         $preguntasCorrectas = count($_SESSION['preguntas_correctas'] ?? []);

         // Crear objeto Partida
         $partida = new Partida(
            [
               'id' => 0, // se asigna en el repositorio con lastInsertId
               'puntaje' => $puntaje,
               'estado' => $estado,
               'preguntas_correctas' => $preguntasCorrectas,
            ],
            new Usuario(['id_usuario' => $_SESSION['user_id'] ?? 0])
         );

         // Guardar la partida en la base de datos
         $response = $this->service->save($partida);
         if (!$response->success) {
            $viewData['error'] = $response->message;
         } else {
            $viewData['success'] = "Partida guardada correctamente";
         }

         $viewData = array_merge($viewData ?? [], [
            'puntaje' => $puntaje,
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'],
            'enunciado' => $_SESSION['enunciado_actual'] ?? '',
            'respuesta_correcta' => $_SESSION['respuesta_correcta'] ?? '',
            'respuestas' => $_SESSION['respuestas'] ?? [],
            'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? ''
         ]);

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

        $pregunta = $this->preguntaService->getPregunta($idCategoria, $_SESSION['preguntas_realizadas']);
        $respuestas = $pregunta->getRespuestasIncorrectas();
        $respuestas[] = $pregunta->getRespuestaCorrecta();
        shuffle($respuestas);

        // Agrego esta linea para usarla en endGame()
        $_SESSION['respuestas'] = $respuestas;

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
            'preguntasRealizadas' => $_SESSION['preguntas_realizadas'],
            'categoria_color' => $pregunta->getCategoria()->getColor() ?? '',
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
            'enunciado' => $_SESSION['enunciado_actual'],
            'respuestas' => $_SESSION['respuestas'],
            'respuesta_usuario' => $_SESSION['respuesta_usuario']
        ];
        $this->view->render("respuestacorrecta", $viewData);
    }
    public function responder(){
        $continuaLaPartida = false;
        $respuesta = $_POST['respuesta'];
        $_SESSION['respuesta_usuario'] = $respuesta;

        if($respuesta == $_SESSION['respuesta_correcta']){
            //deberia mostrar una vista que permita reportar pregunta
            $continuaLaPartida = true;
        }

        if($continuaLaPartida){
            $this->showPreguntaCorrecta();
        }else{
            $this->endGame();
        }
    }
}