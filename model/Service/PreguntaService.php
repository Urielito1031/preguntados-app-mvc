<?php

namespace Service;

use Repository\PreguntaRepository;

class PreguntaService
{
    private $preguntaRepository;

    public function __construct(PreguntaRepository $preguntaRepository){
        $this->preguntaRepository = $preguntaRepository;
    }

    public function getPregunta($idCategoria): ?\Entity\Pregunta
    {
        return $this->preguntaRepository->getPreguntaByCategoria($idCategoria);
    }
}