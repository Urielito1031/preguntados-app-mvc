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

            if (!$response->success) {
                $viewData['error'] = $response->message;
            } else {
                $viewData['success'] = "Partida guardada correctamente";
            }


            $viewData = [
                'puntaje' => $response->success ? $response->data->getPuntaje() : 0,
                'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? ''
            ];



            //  Limpiar la sesión
//            unset(
//                $_SESSION['preguntas_correctas'],
//                $_SESSION['preguntas_realizadas'],
//
//                $_SESSION['pregunta']['respuesta_correcta'],
//                $_SESSION['pregunta']['enunciado'],
//                $_SESSION['pregunta']['respuestas'],
//                $_SESSION['pregunta']['pregunta_actual'],
//
//                $_SESSION['respuesta_usuario']
//            );

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
            $idCategoria = rand(1,6);

            $_SESSION['preguntas_realizadas'] ??= [];

            $pregunta = $this->preguntaService->getPregunta($idCategoria, $_SESSION['preguntas_realizadas']);
            if (!$pregunta) {$this->endGame();return;}

            $respuestas = $pregunta->getRespuestasIncorrectas();
            $respuestas[] = $pregunta->getRespuestaCorrecta();
            shuffle($respuestas);

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

            $_SESSION['preguntas_realizadas'][] = $pregunta->getId();

            $viewData = array_merge($this->getUserSessionData(), [
                'pregunta' => $_SESSION['pregunta'],
                'preguntasRealizadas' => $_SESSION['preguntas_realizadas']
            ]);

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

            if(!isset($_SESSION['pregunta']['id']) || !isset($_SESSION['pregunta']['respuesta_correcta'])) {
                $this->endGame();
                return;
            }

            if ($_POST['respuesta'] === $_SESSION['pregunta']['respuesta_correcta']) {
                
                array_push($_SESSION['preguntas_correctas'], $_SESSION['pregunta']['id']);

                $this->showPreguntaCorrecta();
            } else {
                $this->endGame();
            }



        } catch (\Exception $e) {
            $viewData = [
                'error' => "Error al procesar la respuesta: " . $e->getMessage(),
                'usuario' => $_SESSION['user_name'] ?? '',
                'foto_perfil' => $_SESSION['foto_perfil'],
                'respuesta_usuario' => $respuesta
            ];
            $this->view->render("error", $viewData);
        }
    }

    public function showPreguntaCorrecta()
    {
        $viewData = array_merge($this->getUserSessionData(),[
            'pregunta' => $_SESSION['pregunta'],
            'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? '',
            'puntaje' => count($_SESSION['preguntas_correctas']),
        ]);
        $this->view->render("respuestacorrecta", $viewData);
    }

















//    public function pregunta(): void {
//        if(!isset($_SESSION['preguntas_realizadas'])){
//            $_SESSION['preguntas_realizadas'] = array();
//        }
//
//
//
//        // Miedo me da tocar esto xD
//        $idCategoria = rand(1,6);
//        $pregunta = $this->preguntaService->getPregunta($idCategoria, $_SESSION['preguntas_realizadas']);
//        $respuestas = $pregunta->getRespuestasIncorrectas();
//        $respuestas[] = $pregunta->getRespuestaCorrecta();
//        shuffle($respuestas);
//        $_SESSION['respuestas'] = $respuestas;
//
//        if (!$pregunta) {
//            $this->endGame();
//            return;
//        }
//
//        $_SESSION['pregunta'] = [
//            'id' => $pregunta->getId(),
//            'enunciado' => $pregunta->getEnunciado(),
//            'respuesta_correcta' => $pregunta->getRespuestaCorrecta(),
//            'respuestas' => $respuestas,
//            'categoria' => [
//                'id' => $idCategoria,
//                'descripcion' => $pregunta->getCategoria()->getDescripcion(),
//                'color' => $pregunta->getCategoria()->getColor()
//            ]
//        ];
//
//        array_push($_SESSION['preguntas_realizadas'], $pregunta->getId());
//
//        $viewData = array_merge($this->getUserSessionData(),[
//            'preguntasRealizadas' => $_SESSION['preguntas_realizadas'],
//            'pregunta' => $_SESSION['pregunta'],
//        ]);
//
//        $this->view->render("pregunta", $viewData);
//    }
//    public function showPreguntaCorrecta(): void {
//        $viewData = array_merge($this->getUserSessionData(),
//        [
//            'puntaje' => count($_SESSION['preguntas_realizadas']),
//            'pregunta' => $_SESSION['pregunta'],
//            'respuesta_usuario' => $_POST['respuesta_usuario'],
//        ]);
//        $this->view->render("respuestacorrecta", $viewData);
//    }
//    public function endGame(): void {
//        $viewData = array_merge($this->getUserSessionData(),[
//            'puntaje' => count($_SESSION['preguntas_realizadas']) - 1,
//            'pregunta' => $_SESSION['pregunta'],
//            'respuesta_usuario' => $_POST['respuesta_usuario']
//        ]);
//
//        unset($_SESSION['preguntas_realizadas']);
//        $this->view->render("finpartida", $viewData);
//    }
//    public function endGame()
//    {
//        try {
//
//            $response = $this->service->finalizarPartida(
//                $_SESSION['user_id']?? 0,
//                $_SESSION['preguntas_correctas'] ?? [],
//                $_SESSION['preguntas_realizadas'] ?? []
//            );
//
//            // Preparar datos para la vista
//            $viewData = [
//                'puntaje' => $response->success ? $response->data->getPuntaje() : 0,
//                'usuario' => $_SESSION['user_name'] ?? '',
//                'foto_perfil' => $_SESSION['foto_perfil'],
//                'enunciado' => $_SESSION['enunciado_actual'] ?? '',
//                'respuesta_correcta' => $_SESSION['respuesta_correcta'] ?? '',
//                'respuestas' => $_SESSION['respuestas'] ?? [],
//                'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? ''
//            ];
//
//            if (!$response->success) {
//                $viewData['error'] = $response->message;
//            } else {
//                $viewData['success'] = "Partida guardada correctamente";
//            }
//
//            // Limpiar la sesión
//            unset($_SESSION['preguntas_realizadas'], $_SESSION['preguntas_correctas'], $_SESSION['respuesta_correcta'],
//                $_SESSION['enunciado_actual'], $_SESSION['respuestas'], $_SESSION['respuesta_usuario'], $_SESSION['pregunta_actual']);
//
//            $this->view->render("finpartida", $viewData);
//        } catch (\Exception $e) {
//            $viewData = [
//                'error' => "Error al finalizar la partida: " . $e->getMessage(),
//                'usuario' => $_SESSION['user_name'] ?? '',
//                'foto_perfil' => $_SESSION['foto_perfil']
//            ];
//            $this->view->render("error", $viewData);
//        }
//    }
//    public function responder()
//    {
//        try {
//
//            $respuesta = $_POST['respuesta'] ?? '';
//            if (empty($respuesta)) {
//                throw new \Exception("No se proporcionó una respuesta");
//            }
//
//            $_SESSION['respuesta_usuario'] = $respuesta;
//
//
//            if(!isset($_SESSION['pregunta_actual']) || !isset($_SESSION['respuesta_correcta'])) {
//                $this->endGame();
//                return;
//            }
//            $continuaLaPartida = $respuesta === $_SESSION['respuesta_correcta'];
//
//            if ($continuaLaPartida) {
//                $_SESSION['preguntas_correctas'][] = $_SESSION['pregunta_actual'];
//                $this->showPreguntaCorrecta();
//            } else {
//                $this->endGame();
//            }
//        } catch (\Exception $e) {
//            $viewData = [
//                'error' => "Error al procesar la respuesta: " . $e->getMessage(),
//                'usuario' => $_SESSION['user_name'] ?? '',
//                'foto_perfil' => $_SESSION['foto_perfil']
//            ];
//            $this->view->render("error", $viewData);
//        }
//    }
}