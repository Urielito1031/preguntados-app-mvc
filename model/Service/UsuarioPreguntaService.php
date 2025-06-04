<?php

namespace Service;

use Entity\UsuarioPregunta;
use Repository\UsuarioPreguntaRepository;

class UsuarioPreguntaService
{
   private UsuarioPreguntaRepository $repository;

   public function __construct(UsuarioPreguntaRepository $repository)
   {
      $this->repository = $repository;
   }

   public function registrarRespuesta(int $idUsuario, int $idPregunta): void
   {
      $usuarioPregunta = new UsuarioPregunta($idUsuario, $idPregunta);
      $this->repository->save($usuarioPregunta);
   }

   public function getPreguntasPorUsuario(int $idUsuario): array
   {
      return $this->repository->getPreguntasIdByUsuario($idUsuario);
   }

   public function eliminarRespuesta(int $idUsuario, int $idPregunta): void
   {
      $usuarioPregunta = new UsuarioPregunta($idUsuario, $idPregunta);
      $this->repository->delete($usuarioPregunta);
   }
}