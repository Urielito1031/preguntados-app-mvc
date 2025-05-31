<?php

namespace Entity;

class Pregunta
{
   private int $id;
   private string $respuestaCorrecta;
   private Categoria $categoria;
   private Nivel $nivel;
   private string $enunciado;
   private array $respuestasIncorrectas;



   public function __construct(array $data, Categoria $categoria, Nivel $nivel, array $respuestasIncorrectas = [])
   {
      $this->id = $data['id'] ?? 0;
      $this->respuestaCorrecta = $data['respuesta_correcta'] ?? '';
      $this->enunciado = $data['enunciado'] ?? '';
      $this->categoria = $categoria;
      $this->nivel = $nivel;
      $this->respuestasIncorrectas = $respuestasIncorrectas;


   }
   public function getRespuestasIncorrectas(): array
   {
      return $this->respuestasIncorrectas;
   }
   public function getCategoria(): Categoria
   {
      return $this->categoria;
   }
   public function getNivel(): Nivel
   {
      return $this->nivel;
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