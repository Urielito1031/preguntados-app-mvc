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


    public function getPreguntasSugeridas(): array{
        return $this->sugerenciaPreguntaRepository->getPreguntasSugeridas();
    }
    public function getRespuestasSugeridas($idPregunta): array{
        return $this->sugerenciaPreguntaRepository->getRespuestasSugeridas($idPregunta);
    }

    public function findByIdParaEditor(int $idPregunta): DataResponse{
        $pregunta = $this->sugerenciaPreguntaRepository->findByIdParaEditor($idPregunta);
        if ($pregunta === null) {
            return new DataResponse(false, "Pregunta no encontrada por id");
        }
        return new DataResponse(true, "Pregunta por id encontrada", $pregunta);
    }

    public function eliminarPregunta(int $idPregunta){
        $resultado = $this->sugerenciaPreguntaRepository->eliminarPregunta($idPregunta);
        return $resultado;
    }

}