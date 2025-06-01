<?php

namespace Entity;

class Partida
{
   private int $id;
   private Usuario $usuario;
   private int $puntaje;
   private string $estado;
   private int $cantidadPreguntasCorrectas;
   private \DateTimeImmutable $fechaCreacion;


   public function __construct(array $data, Usuario $usuario) {
      $this->id = $data["id"];
      $this->puntaje = $data["puntaje"];
      $this->estado = $data["estado"];
      $this->cantidadPreguntasCorrectas = $data["preguntas_correctas"];
      $this->usuario = $usuario;
      $this->fechaCreacion = new \DateTimeImmutable("now");

   }
   public function getId(): int {
      return $this->id;
   }
   public function getUsuario(): Usuario {
      return $this->usuario;
   }

   public function getPuntaje(): int {
      return $this->puntaje;
   }
   public function getEstado(): string {
      return $this->estado;
   }
   public function getCantidadPreguntasCorrectas(): int {
      return $this->cantidadPreguntasCorrectas;
   }
   public function getFechaCreacion(): \DateTimeImmutable {
      return $this->fechaCreacion;
   }
   public function setId(int $id): void {
      $this->id = $id;
   }

   public function setPuntaje(int $puntaje): void {
      $this->puntaje = $puntaje;
   }
   public function setEstado(string $estado): void {
      $this->estado = $estado;
   }
   public function setCantidadPreguntasCorrectas(int $cantidadPreguntasCorrectas): void {
      $this->cantidadPreguntasCorrectas = $cantidadPreguntasCorrectas;
   }




}