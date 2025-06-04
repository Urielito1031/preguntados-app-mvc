<?php

namespace Service;

use Entity\UsuarioPregunta;
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
   public function validarPreguntaNoRepetidaEnUsuario($idUsuario,$idPregunta):bool{
     $this->usuarioService->findById  ($idUsuario);
   }





   public function eliminarRespuesta(int $idUsuario, int $idPregunta): void
   {
      $usuarioPregunta = new UsuarioPregunta($idUsuario, $idPregunta);
      $this->repository->delete($usuarioPregunta);
   }
}