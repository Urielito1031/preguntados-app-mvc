<?php

namespace Service;

use Repository\PreguntaRepository;

class PreguntaService
{
    private $preguntaRepository;

    public function __construct(PreguntaRepository $preguntaRepository){
        $this->preguntaRepository = $preguntaRepository;
    }

    public function getPregunta($idCategoria,$array_id_preguntas_realizadas): ?\Entity\Pregunta
    {
        return $this->preguntaRepository->getPreguntaByCategoria($idCategoria,$array_id_preguntas_realizadas);
    }
}