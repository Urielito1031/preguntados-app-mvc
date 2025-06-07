<?php

namespace Service;

use Entity\UsuarioPregunta;
use mysql_xdevapi\Exception;
use PDOException;
use Repository\UsuarioPreguntaRepository;
use Response\DataResponse;
require_once( __DIR__ . '/../Entity/UsuarioPregunta.php');

class UsuarioPreguntaService
{
   private UsuarioPreguntaRepository $repository;

   private UsuarioService $usuarioService;
   private PreguntaService $preguntaService;

   public function __construct(UsuarioPreguntaRepository $repository, $usuarioService, $preguntaService)
   {
      $this->repository = $repository;
      $this->usuarioService = $usuarioService;
      $this->preguntaService = $preguntaService;
   }

   public function registrarUsuarioPregunta(int $idUsuario, int $idPregunta): DataResponse
   {
      if (!$this->validarPreguntaNoRepetidaEnUsuario($idUsuario, $idPregunta)) {
         $idPregunta = $this->obtenerNuevaPregunta($idUsuario);

         if ($idPregunta === null) {
            $this->repository->resetearPreguntasParaUsuario($idUsuario);
            return new DataResponse(false, "No hay más preguntas disponibles para este usuario");
         }
      }

      $usuarioPregunta = new UsuarioPregunta($idUsuario, $idPregunta);
      $this->repository->save($usuarioPregunta);
      return new DataResponse(true, "Pregunta registrada correctamente para el usuario", $usuarioPregunta);
   }


   public function obtenerNuevaPregunta(int $idUsuario): ?int
   {
      try {
         do {
            $preguntaId = $this->repository->getPreguntaIdRandomNoRespondida($idUsuario);
            if ($preguntaId === null) {
               return null; // No hay más preguntas disponibles
            }
         } while (!$this->validarPreguntaNoRepetidaEnUsuario($idUsuario, $preguntaId));

         return $preguntaId;
      } catch (PDOException $e) {
         return null;
      }
   }

   public function getPreguntasPorUsuario(int $idUsuario): DataResponse
   {
      try{
      $preguntasIds = $this->repository->getPreguntasIdByUsuario($idUsuario);
         return new DataResponse(true, "Preguntas obtenidas correctamente", $preguntasIds);

      }catch(PDOException $e){
         return new DataResponse(false, "Error al obtener las preguntas del usuario: " . $e->getMessage());
      }
   }

   //unicamente se encarga de validar
   public function validarPreguntaNoRepetidaEnUsuario($idUsuario,$idPregunta):bool{

      try{
         $usuarioResponse = $this->usuarioService->findById($idUsuario);
         if(!$usuarioResponse->success) {
            throw new \Exception("Usuario no encontrado");
         }


         $response = $this->getPreguntasPorUsuario($idUsuario);
         if (!$response->success) {
            throw new \Exception($response->message);
         }
         $preguntasDeUsuario = $response->data;


         return !in_array($idPregunta, $preguntasDeUsuario, true);

      }catch (PDOException $e){
         throw new PDOException("Error al validar la pregunta en el usuario:  " . $e);
      }
   }

   /**
    * Selecciona una pregunta para un usuario basado en su nivel y la dificultad esperada.
    *
    * @param int $idUsuario ID del usuario para el cual se seleccionará la pregunta.
    * @param array $preguntasRealizadas Array de IDs de preguntas que el usuario ya ha respondido.
    * @return DataResponse Contiene la pregunta seleccionada o un mensaje de error.
    */
   public function seleccionarPreguntaParaUsuario(int $idUsuario, array $preguntasRealizadas = []): DataResponse
   {
      try {

         $usuarioResponse = $this->usuarioService->findById($idUsuario);
         if (!$usuarioResponse->success) {
            return new DataResponse(false, "Usuario no encontrado");
         }
         $usuario = $usuarioResponse->data;

         $nivelUsuario = $usuario->getNivel();

         $dificultadEsperada = match (true) {
            $nivelUsuario >= 0.7 => 'DIFICIL',
            $nivelUsuario >= 0.3 && $nivelUsuario < 0.7 => 'MEDIO',
            $nivelUsuario < 0.3 => 'FACIL',
            default => throw new \Exception("Nivel de usuario inválido")
         };

         $preguntasResponse = $this->preguntaService->obtenerPreguntasPorDificultad($dificultadEsperada);
         if (!$preguntasResponse->success) {
            return new DataResponse(false, "No se encontraron preguntas de dificultad $dificultadEsperada");
         }

         //aca filtramos las preguntas que el usuario no respondió
         $preguntas = $preguntasResponse->data;
         $preguntasNoRespondidas = array_filter($preguntas, function ($pregunta) use ($preguntasRealizadas) {
            return !in_array($pregunta->getId(), $preguntasRealizadas);
         });

         if (empty($preguntasNoRespondidas)) {
            return new DataResponse(false, "No hay preguntas disponibles de dificultad $dificultadEsperada que el usuario no haya respondido");
         }


         $preguntaSeleccionada = reset($preguntasNoRespondidas);

         return new DataResponse(true, "Pregunta (Nivel usuario: $nivelUsuario, Dificultad pregunta: $dificultadEsperada)", $preguntaSeleccionada);
      } catch (PDOException $e) {
         return new DataResponse(false, "Error al seleccionar la pregunta: " . $e->getMessage());
      } catch (\Exception $e) {
         return new DataResponse(false, "Error inesperado: " . $e->getMessage());
      }
   }


   public function eliminarRespuesta(int $idUsuario, int $idPregunta): void
   {
      $usuarioPregunta = new UsuarioPregunta($idUsuario, $idPregunta);
      $this->repository->delete($usuarioPregunta);
   }
}