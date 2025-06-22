<?php

namespace Entity;

class UsuarioPregunta
{
   private int $idUsuario;
   private int $idPregunta;

   public function __construct(int $idUsuario, int $idPregunta)
   {
      $this->idUsuario = $idUsuario;
      $this->idPregunta = $idPregunta;
   }


   public function getIdUsuario(): int { return $this->idUsuario; }
   public function getIdPregunta(): int { return $this->idPregunta; }

   public function setIdUsuario(int $idUsuario): void { $this->idUsuario = $idUsuario; }
   public function setIdPregunta(int $idPregunta): void { $this->idPregunta = $idPregunta; }
}