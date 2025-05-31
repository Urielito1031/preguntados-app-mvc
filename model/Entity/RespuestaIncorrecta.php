<?php

namespace Entity;

class RespuestaIncorrecta
{
   private int $id;
   private string $respuesta;
   private int $idPregunta;

   public function __construct(array $data = []){
      $this->id = $data['id'] ?? 0;
      $this->respuesta = $data['respuesta'] ?? '';
      $this->idPregunta = $data['idPregunta'] ?? 0;
   }
   public function getId(): int
   {
      return $this->id;
   }
   public function setId(int $id): void
   {
      $this->id = $id;
   }
   public function getRespuesta(): string
   {
      return $this->respuesta;
   }

   public function getIdPregunta(): int
   {
      return $this->idPregunta;
   }


}