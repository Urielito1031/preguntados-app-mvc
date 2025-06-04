<?php

namespace Service;

use Entity\UsuarioPregunta;
use PDOException;
use Repository\UsuarioPreguntaRepository;
use Response\DataResponse;

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

      $validado = $this->validarPreguntaNoRepetidaEnUsuario($idUsuario,$idPregunta);
      if(!$validado){
         return new DataResponse(false,"La pregunta ya fue respondida por el usuario en otra ocasión");
      }
      $usuarioPregunta = new UsuarioPregunta($idUsuario, $idPregunta);
      $this->repository->save($usuarioPregunta);
      return new DataResponse(true, "Pregunta registrada correctamente para el usuario", $usuarioPregunta);
   }

   public function getPreguntasPorUsuario(int $idUsuario): array
   {
      return $this->repository->getPreguntasIdByUsuario($idUsuario);
   }

   //unicamente se encarga de validar
   public function validarPreguntaNoRepetidaEnUsuario($idUsuario,$idPregunta):bool{

      try{
         $this->usuarioService->findById($idUsuario);
         $preguntas = $this->getPreguntasPorUsuario($idUsuario);

         if(in_array($idPregunta, $preguntas)){
            return false;
         }

         return true;
      }catch (PDOException $e){
         throw new PDOException("Error al validar la pregunta en el usuario:  " . $e);
      }
   }

   public function seleccionarPreguntaParaUsuario(int $idUsuario, ?int $idCategoria = null, array $preguntasRealizadas = []): DataResponse
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

         $preguntasResponse = $this->preguntaService->obtenerPreguntasPorDificultad($dificultadEsperada, $idCategoria);
         if (!$preguntasResponse->success) {
            return new DataResponse(false, "No se encontraron preguntas de dificultad $dificultadEsperada" . ($idCategoria ? " en la categoría $idCategoria" : ""));
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

         return new DataResponse(true, "Pregunta seleccionada correctamente (Nivel usuario: $nivelUsuario, Dificultad pregunta: $dificultadEsperada)", $preguntaSeleccionada);
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