<?php

namespace Entity;

class Partida
{
   private int $id;
   private int $id_usuario;
   private int $puntaje;
   private string $estado;
   private int $cantidadPreguntasCorrectas;
   private \DateTime $fechaCreacion;


   public function __construct(array $data) {
      $this->id = $data["id"];
      $this->id_usuario = $data["id_usuario"];
      $this->puntaje = $data["puntaje"];
      $this->estado = $data["estado"];
      $this->cantidadPreguntasCorrectas = $data["preguntas_correctas"];
      $this->fechaCreacion = new \DateTime($data["fecha_creacion"]);

   }
   public function getId(): int {
      return $this->id;
   }
   public function getIdUsuario(): int {
      return $this->id_usuario;
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
   public function getFechaCreacion(): \DateTime {
      return $this->fechaCreacion;
   }
   public function setId(int $id): void {
      $this->id = $id;
   }
   public function setIdUsuario(int $id_usuario): void {
      $this->id_usuario = $id_usuario;
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