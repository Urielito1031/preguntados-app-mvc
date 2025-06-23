<?php

namespace Service;


use Entity\PreguntaSugerida;
use Repository\SugerenciaPreguntaRepository;
use Response\DataResponse;
class SugerenciaPreguntaService
{

    private  SugerenciaPreguntaRepository $sugerenciaPreguntaRepository;

    public function __construct(SugerenciaPreguntaRepository $sugerenciaPreguntaRepository)
    {
        $this->sugerenciaPreguntaRepository = $sugerenciaPreguntaRepository;
    }

    public function crearPregunta (int $idCategoria,string $enunciado, array $respuestas,  int $posicionArrayDeRespuestaCorrecta)
    {
        $nuevaPreguntaSugerida = new PreguntaSugerida($idCategoria, $enunciado, $respuestas, $posicionArrayDeRespuestaCorrecta);
        // GUARDAR PREGUNTA EN EL REPOSITORIO
        $this->guardarPregunta($nuevaPreguntaSugerida);

    }

    private function guardarPregunta (PreguntaSugerida $pregunta) {
        $this->sugerenciaPreguntaRepository->save($pregunta);
        $this->view->render("home");
    }

}