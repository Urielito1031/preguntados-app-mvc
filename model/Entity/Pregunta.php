<?php

namespace Entity;

class Pregunta
{
   private int $id;
   private string $respuestaCorrecta;
   private Categoria $categoria;
   private string $enunciado;
   private int $cantidadJugada;
   private int $cantidadAciertos;
   private int $cantidadReportes;
   private array $respuestasIncorrectas;




   public function __construct(array $data, Categoria $categoria, array $respuestasIncorrectas = [])
   {
      $this->id = $data['id'] ?? 0;
      $this->respuestaCorrecta = $data['respuesta_correcta'] ?? '';
      $this->enunciado = $data['enunciado'] ?? '';
      $this->categoria = $categoria;
      $this->respuestasIncorrectas = $respuestasIncorrectas;
      $this->cantidadJugada = $data['cantidad_jugada'] ?? 0;
      $this->cantidadAciertos = $data['cantidad_aciertos'] ?? 0;
      $this->cantidadReportes = $data['cantidad_reportes'] ?? 0;

   }
   public function getCantidadJugada(): int
   {
      return $this->cantidadJugada;
   }
   public function getCantidadAciertos(): int
   {
      return $this->cantidadAciertos;
   }
   public function getCantidadReportes(): int
   {
      return $this->cantidadReportes;
   }

   public function setCantidadJugada(int $cantidadJugada): void
   {
      $this->cantidadJugada = $cantidadJugada;
   }
   public function setCantidadAciertos(int $cantidadAciertos): void
   {
      $this->cantidadAciertos = $cantidadAciertos;
   }
   public function setCantidadReportes(int $cantidadReportes): void
   {
      $this->cantidadReportes = $cantidadReportes;
   }


   public function getRespuestasIncorrectas(): array
   {
      return $this->respuestasIncorrectas;
   }
   public function getCategoria(): Categoria
   {
      return $this->categoria;
   }

   public function getId(): int
   {
      return $this->id;
   }
   public function setId(int $id): void
   {
      $this->id = $id;
   }
   public function getRespuestaCorrecta(): string
   {
      return $this->respuestaCorrecta;
   }


   public function getEnunciado(): string
   {
      return $this->enunciado;
   }
   public function setEnunciado(string $enunciado): void
   {
      $this->enunciado = $enunciado;
   }

}