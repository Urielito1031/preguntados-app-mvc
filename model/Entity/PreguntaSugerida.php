<?php

namespace Entity;

class PreguntaSugerida
{
    private int $id;
    private int $idCategoria;
    private int $posicionArrayDeRespuestaCorrecta;
    private string $enunciado;
    private array $respuestas;



    public function __construct(int $idCategoria,string $enunciado, array $respuestas,  int $posicionArrayDeRespuestaCorrecta)
    {
        $this->id = $id ?? 0;
        $this->idCategoria = $idCategoria ?? '';
        $this->enunciado = $enunciado ?? '';
        $this->respuestas = $respuestas?? [];
        $this->posicionArrayDeRespuestaCorrecta = $posicionArrayDeRespuestaCorrecta ?? '';

    }
      public function getId(): int
      {
         return $this->id;
      }
      public function setId(int $id):void {
         $this->id = $id;
      }

    public function getIdCategoria(): int
    {
        return $this->idCategoria;
    }

    public function getPosicionArrayDeRespuestaCorrecta(): int
    {
        return $this->posicionArrayDeRespuestaCorrecta;
    }

    public function getEnunciado(): string
    {
        return $this->enunciado;
    }

    public function getRespuestas(): array
    {
        return $this->respuestas;
    }




}