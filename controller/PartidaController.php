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


   private UsuarioRepository $usuarioRepository;
   private PreguntaRepository $preguntaRepository;

   private PreguntaService $preguntaService;
   private UsuarioService $usuarioService;
   private UsuarioPreguntaService $usuarioPreguntaService;

   public function __construct(PartidaService $service,
                               UsuarioRepository $usuarioRepository,
                               PreguntaRepository $preguntaRepository,
                               UsuarioService $usuarioService,
                               PreguntaService $preguntaService,
                               UsuarioPreguntaService $usuarioPreguntaService,
                               MustachePresenter $view)
   {
      $this->view = $view;
      $this->service = $service;
      $this->usuarioRepository = $usuarioRepository;
      $this->preguntaRepository = $preguntaRepository;
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
         if (!isset($_SESSION['partida'])) {
            throw new \Exception("No hay partida activa");
         }



         $serializedPartida = $_SESSION['partida'];
         //otro debuug
         if (!is_string($serializedPartida)) {
            throw new \Exception("Datos de partida en sesión no son una cadena válida");
         }


         $partida = unserialize($serializedPartida);
         //debuuuugg
         if ($partida === false) {
            throw new \Exception("No se pudo deserializar la partida");
         };
         $partida->setEstado($this->calcularEstado($_SESSION['preguntas_correctas'] ?? []));
         $partida->setPuntaje(count($_SESSION['preguntas_correctas'] ?? []));

         $response = $this->service->finalizarPartida($partida);

         $viewData = array_merge($this->getUserSessionData(), [
            'puntaje' => $partida->getPuntaje(),
            'respuesta_usuario' => $_SESSION['respuesta_usuario'] ?? ''
         ]);

         if (!$response->success) {
            $viewData['error'] = $response->message;
         } else {
            $viewData['success'] = "Partida finalizada correctamente";
         }

         // Limpiar la sesión
         unset(
            $_SESSION['partida'],
            $_SESSION['preguntas_correctas'],
            $_SESSION['preguntas_realizadas'],
            $_SESSION['pregunta'],
            $_SESSION['respuesta_usuario']
         );

         $this->view->render("finpartida", $viewData);
      } catch (\Exception $e) {
         $viewData = [
            'error' => "Error al finalizar la partida: " . $e->getMessage(),
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'],
            'puntaje' => count($_SESSION['preguntas_correctas'] ?? [])
         ];
         $this->view->render("error", $viewData);
      }
   }
    public function pregunta(): void {
        try {

          // verificamos si hay una pregunta activa, para evitar que el usuario recargue la pagina y haga trampa
          // ok no anda jaja
               if(!isset($_SESSION['pregunta']) && !empty($_SESSION['pregunta']['id'])){
                  $this->endGame();
                  return;

               }

           //en session guardamos la partida si no está creada
            if(!isset($_SESSION['partida'])){
               $response = $this->service->iniciarPartida($_SESSION['user_id']?? 0);
               if(!$response->success){
                  $viewData = [
                      'error' => $response->message,
                      'usuario' => $_SESSION['user_name'] ?? '',
                      'foto_perfil' => $_SESSION['foto_perfil']
                  ];
                  $this->view->render("error", $viewData);
                  return;
               }



               //el serialize permite pasar el objeto partida en un formato string
               // para que la session lo pueda guardar
               $_SESSION['partida'] = serialize($response->data);
               $_SESSION['preguntas_correctas'] = [];
               $_SESSION['preguntas_realizadas'] = [];
            }

            //deberiamos aplicar logica para que no se repitan las preguntas
           //llamar a usuarioPreguntaService para registrar la pregunta en la base de datos
           // registrarUsuarioPregunta(int $idUsuario, int $idPregunta): DataResponse
           //el metodo valida que la pregunta no haya sido respondida por el usuario
            $idCategoria = rand(1,6);


            $responsePregunta = $this->preguntaService->getPregunta($idCategoria, $_SESSION['preguntas_realizadas']);
            if(!$responsePregunta->success){
                  $viewData = [
                     'error' => $responsePregunta->message,
                     'usuario' => $_SESSION['user_name'] ?? '',
                     'foto_perfil' => $_SESSION['foto_perfil']
                  ];
                  $this->view->render("error", $viewData);
                  return;
            }
            $pregunta = $responsePregunta->data;
            //debemos calcular si el nivel de la pregunta es adecuada para el usuario
           //preguntaService-> calcularNivelPregunta($pregunta):DataResponse;
            if (!$pregunta) {$this->endGame();return;}

           //una vez que se muestre la pregunta
           //usar servicePregunta->acumularPreguntaJuagada($pregunta);
            $respuestas = $pregunta->getRespuestasIncorrectas();
            $respuestas[] = $pregunta->getRespuestaCorrecta();
            //si responde correctamente usar acumularAciertoPregunta(Pregunta $pregunta)
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
   public function responder(): void
   {
      try {
         if (empty($_POST['respuesta'])) {
            throw new \Exception("No se proporcionó una respuesta");
         }

         $_SESSION['respuesta_usuario'] = $_POST['respuesta'];

         if ($_POST['respuesta'] == $_SESSION['pregunta']['respuesta_correcta']) {
            $_SESSION['preguntas_correctas'][] = $_SESSION['pregunta']['id'];
             $this->showPreguntaCorrecta();

         } else {
            $this->endGame();
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
    }
   private function calcularEstado(array $preguntasCorrectas): string
   {
      return count($preguntasCorrectas) > 0 ? 'GANADA' : 'PERDIDA';
   }
}