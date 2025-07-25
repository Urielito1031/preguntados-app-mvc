<?php

use Repository\PreguntaRepository;
use Repository\UsuarioRepository;
use Service\PartidaService;
use Service\PreguntaService;
use Service\UsuarioPreguntaService;
use Service\UsuarioService;

class PartidaController
{
   private $view;
   private PartidaService $service;



   private PreguntaService $preguntaService;
   private UsuarioService $usuarioService;
   private UsuarioPreguntaService $usuarioPreguntaService;

   public function __construct(PartidaService $service,
                               UsuarioService $usuarioService,
                               PreguntaService $preguntaService,
                               UsuarioPreguntaService $usuarioPreguntaService,
                               MustachePresenter $view)
   {
      $this->view = $view;
      $this->service = $service;

      $this->preguntaService = $preguntaService;
      $this->usuarioService = $usuarioService;
      $this->usuarioPreguntaService = $usuarioPreguntaService;


   }

    private function getUserSessionData() : array {
        return [
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
    }
   public function endGame(): void
   {
      try {
         if (!isset($_SESSION['partida']) || !is_string($_SESSION['partida'])) {
            throw new \Exception("No hay partida activa o los datos son inválidos");
         }

         $partida = unserialize($_SESSION['partida']);
         if ($partida === false) {
            throw new \Exception("No se pudo deserializar la partida");
         }

         $preguntasCorrectas = $_SESSION['preguntas_correctas'] ?? [];
         $partida->setEstado($this->calcularEstado($preguntasCorrectas));
         $partida->setPuntaje(count($preguntasCorrectas));

         $response = $this->service->finalizarPartida($partida);

         $viewData = array_merge($this->getUserSessionData(), [
            'puntaje' => $partida->getPuntaje(),
            'pregunta' => $_SESSION['pregunta'],
            'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? ''
         ]);

         if (isset($_SESSION['error_tiempo'])) {
            $viewData['error'] = $_SESSION['error_tiempo'];
            unset($_SESSION['error_tiempo']);
         }

         $viewData[$response->success ? 'success' : 'error'] = $response->success
            ? "Partida finalizada correctamente"
            : $response->message;

         $this->clearSession();
         unset($_SESSION['pregunta_reportada']);

         $this->view->render("finpartida", $viewData);

      } catch (\Exception $e) {
         $this->view->render("error", [
            'error' => "Error al finalizar la partida: " . $e->getMessage(),
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'] ?? '',
            'puntaje' => count($_SESSION['preguntas_correctas'] ?? [])
         ]);
      }
   }
   public function pregunta(): void
   {
      try {
         unset($_SESSION['pregunta_reportada']);

         // Evitar recarga con pregunta activa
         if (!empty($_SESSION['pregunta']['id'])) {
            session_destroy();
            header('Location: ../');
            return;
         }

         // Iniciar partida si no existe
         if (!isset($_SESSION['partida'])) {
            $response = $this->service->iniciarPartida($_SESSION['user_id'] ?? 0);

            if (!$response->success) {
               $this->view->render("error", [
                  'error' => $response->message,
                  'usuario' => $_SESSION['user_name'] ?? '',
                  'foto_perfil' => $_SESSION['foto_perfil'] ?? ''
               ]);
               return;
            }

            $_SESSION['partida'] = serialize($response->data);
            $_SESSION['preguntas_correctas'] = [];
            $_SESSION['preguntas_realizadas'] = [];
         }

         // Obtener pregunta aleatoria
         $idCategoria = rand(1, 6);
         $responsePregunta = $this->preguntaService->getPregunta(
            $idCategoria,
            $_SESSION['preguntas_realizadas'],
            $_SESSION['user_id']
         );

         if (!$responsePregunta->success || !$responsePregunta->data) {
            $this->view->render("error", [
               'error' => $responsePregunta->message,
               'usuario' => $_SESSION['user_name'] ?? '',
               'foto_perfil' => $_SESSION['foto_perfil'] ?? ''
            ]);
            return;
         }

         $pregunta = $responsePregunta->data;
         $_SESSION['tiempo_de_entrega'] = time();

         $respuestas = array_merge(
            $pregunta->getRespuestasIncorrectas(),
            [$pregunta->getRespuestaCorrecta()]
         );
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

         $this->usuarioPreguntaService->registrarUsuarioPregunta($_SESSION['user_id'], $pregunta->getId());
         $this->preguntaService->acumularPreguntaJugada($pregunta);
         $_SESSION['preguntas_realizadas'][] = $pregunta->getId();

         $this->view->render("pregunta", array_merge($this->getUserSessionData(), [
            'pregunta' => $_SESSION['pregunta'],
            'preguntasRealizadas' => $_SESSION['preguntas_realizadas']
         ]));

      } catch (\Exception $e) {
         $this->view->render("error", [
            'error' => "Error al cargar la pregunta: " . $e->getMessage(),
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'] ?? ''
         ]);
      }
   }
   public function responder(): void
   {
      try {
         if (empty($_POST['respuesta'])) {
            throw new \Exception("No se proporcionó una respuesta");
         }
         $tiempo_respuesta = time();

         $_SESSION['respuesta_usuario'] = $_POST['respuesta'];
         $tiempo_entrega = $tiempo_respuesta - $_SESSION['tiempo_de_entrega'];
        if($tiempo_entrega <= 10){

         if ($_POST['respuesta'] == $_SESSION['pregunta']['respuesta_correcta']) {
            $_SESSION['preguntas_correctas'][] = $_SESSION['pregunta']['id'];
            $preguntaId = $_SESSION['pregunta']['id'];
            $preguntaEntity = $this->preguntaService->findById($preguntaId)->data;

            $this->preguntaService->acumularAciertoPregunta($preguntaEntity);

            $usuario = $this->usuarioService->findById($_SESSION['user_id']);
            $this->usuarioService->sumarRespuestaCorrecta($usuario->data);
            $this->usuarioService->sumarPreguntaEntregada($usuario->data);
            $this->showPreguntaCorrecta();

         } else {
             $usuario = $this->usuarioService->findById($_SESSION['user_id']);
             $this->usuarioService->sumarPreguntaEntregada($usuario->data);
            $this->endGame();
         }
        } else {
           $_SESSION['respuesta_usuario'] = $_POST['respuesta'];
           $_SESSION['error_tiempo'] = "El tiempo de respuesta fue mayor a 10 segundos.";
           $this->endGame();
           return;
        }
      } catch (\Exception $e) {
         $viewData = array_merge($this->getUserSessionData(), [
            'error' => "Error al procesar la respuesta: " . $e->getMessage(),
            'respuesta_usuario' => $_POST['respuesta']
         ]);
         $this->view->render("error", $viewData);
      }
   }
    public function showPreguntaCorrecta(): void {
        $viewData = array_merge($this->getUserSessionData(),[
            'pregunta' => $_SESSION['pregunta'],
            'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? '',
            'puntaje' => count($_SESSION['preguntas_correctas']),
        ]);
        $this->view->render("respuestacorrecta", $viewData);
         // Limpiar la pregunta y respuesta del usuario para evitar recargas
       unset($_SESSION['pregunta'], $_SESSION['respuesta_usuario']);
    }

    // SE USA PARA setEstado() y luego le pasa la partida seteada al servicio, para finalizar actualizandola
    // desde el repositorio, pero el estado de la partida jamas se guarda, parece que no hace falta realizar
    // una operacion con este atributo si no necesitamos guardarlo
    private function calcularEstado(array $preguntasCorrectas): string
   {
      return count($preguntasCorrectas) > 0 ? 'GANADA' : 'PERDIDA';
   }

    public function reportarPregunta(){
        if(isset($_SESSION['pregunta_reportada'])) {
            echo json_encode(["available" => true,"resultado" => "Ya ha sido reportada"]);
        }else {
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "Falta el parámetro id"]);
            exit;
        }
        $idPregunta =$_GET['id'];
        $resultado = $this->preguntaService->reportarPregunta($idPregunta);
        $_SESSION['pregunta_reportada']= true;

        echo json_encode(["available" => true,"recibido" => $_GET['id'] ,"resultado" => $resultado]);
        }
    }

   public function clearSession(): void
   {
      unset(
         $_SESSION['partida'],
         $_SESSION['preguntas_correctas'],
         $_SESSION['preguntas_realizadas'],
         $_SESSION['pregunta'],
         $_SESSION['respuesta_usuario'],
         $_SESSION['pregunta_reportada']
      );
   }
}