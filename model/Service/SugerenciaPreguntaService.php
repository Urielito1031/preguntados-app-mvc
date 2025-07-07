<?php

namespace Service;


use Entity\PreguntaSugerida;
use Repository\SugerenciaPreguntaRepository;
use Response\DataResponse;

class SugerenciaPreguntaService
{

    private SugerenciaPreguntaRepository $sugerenciaPreguntaRepository;

    public function __construct(SugerenciaPreguntaRepository $sugerenciaPreguntaRepository)
    {
        $this->sugerenciaPreguntaRepository = $sugerenciaPreguntaRepository;
    }

    public function crearPregunta(int $idCategoria, string $enunciado, array $respuestas, int $posicionArrayDeRespuestaCorrecta)
    {
        $nuevaPreguntaSugerida = new PreguntaSugerida($idCategoria, $enunciado, $respuestas, $posicionArrayDeRespuestaCorrecta);
        // GUARDAR PREGUNTA EN EL REPOSITORIO
        $this->guardarPregunta($nuevaPreguntaSugerida);

    }

    private function guardarPregunta(PreguntaSugerida $pregunta) : DataResponse
    {


        try {
            if (empty(trim($pregunta->getEnunciado()))) {
                return new DataResponse(false, "El enunciado es obligatorio.");
            }

            if (empty($pregunta->getRespuestas())) {
                return new DataResponse(false, "Las respuestas son obligatorias.");
            }

            if (empty($pregunta->getIdCategoria())) {
                return new DataResponse(false, "La categoria es obligatoria.");
            }

            if (empty($pregunta->getPosicionArrayDeRespuestaCorrecta())) {
                return new DataResponse(false, "Debe seleccionar una respuesta correcta.");
            }

            $this->sugerenciaPreguntaRepository->save($pregunta);
            return new DataResponse(true, "Pregunta guardada con exito", $pregunta);
        } catch (\Exception $e) {
            return new DataResponse(false, "Error al guardar pregunta sugerida: " . $e->getMessage());
        }

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