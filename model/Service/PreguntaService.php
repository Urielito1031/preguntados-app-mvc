<?php

namespace Service;

use Entity\Pregunta;
use Repository\PreguntaRepository;
use Response\DataResponse;

class PreguntaService
{
    private  PreguntaRepository $repository;
    private  const  CANTIDAD_MINIMA_PARA_CALCULAR = 10;
    private array $niveles = [
        "DIFICIL" => [],
        "MEDIO" =>[],
        "FACIL" =>[]
    ];

    public function __construct(PreguntaRepository $preguntaRepository){
        $this->repository = $preguntaRepository;
    }



   public function calcularNivelPregunta(Pregunta $pregunta):DataResponse
    {
       if(self::CANTIDAD_MINIMA_PARA_CALCULAR > $pregunta->getCantidadJugada()){
            return new DataResponse(false, "No se puede calcular el nivel de la pregunta, porque no se ha jugado lo suficiente");

       }

       $ratio = $pregunta->getRatioPregunta();
         if($ratio > 0 && $ratio < 0.3){
            $this->niveles["DIFICIL"][] = $pregunta->getId();
            return new DataResponse(true, "DIFÍCIL", $ratio);
         }
         if($ratio >= 0.3 && $ratio < 0.7){
            $this->niveles["MEDIO"][] = $pregunta->getId();
            return new DataResponse(true, "MEDIO", $ratio);
         }
         if($ratio >= 0.7 && $ratio <= 1){
            $this->niveles["FACIL"][] = $pregunta->getId();
            return new DataResponse(true, "FÁCIL", $ratio);
         }


       return new DataResponse(false, "Nivel no determinado");


    }


    //
   public function obtenerRatioPregunta(int $idPregunta): float
   {
      $pregunta = $this->repository->find($idPregunta);
      if ($pregunta === null) {
         return 0.0;
      }
      return $pregunta->getRatioPregunta();
   }
   public function obtenerPreguntasPorDificultad(string $nivel, ?string $idCategoria = null): DataResponse
   {
      try {
         $nivel = strtoupper($nivel);
         if (!in_array($nivel, ['DIFICIL', 'MEDIO', 'FACIL'])) {
            return new DataResponse(false, "Nivel de dificultad no válido: $nivel");
         }

         $preguntas = $this->repository->getPreguntasPorDificultad($nivel);
         if (empty($preguntas)) {
            return new DataResponse(false, "No se encontraron preguntas de nivel $nivel" . ($idCategoria ? " en la categoría $idCategoria" : ""));
         }

         return new DataResponse(true, "Preguntas obtenidas correctamente", $preguntas);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al obtener preguntas por dificultad: " . $e->getMessage());
      }
   }





    public function getPregunta($idCategoria, $array_id_preguntas_realizadas): Pregunta
    {
        return $this->repository->getPreguntaByCategoria($idCategoria,$array_id_preguntas_realizadas);
    }

    public function acumularPreguntaJugada(Pregunta $pregunta):DataResponse{
       $this->repository->acumularPreguntaJugada($pregunta);
       return new DataResponse(true, "Pregunta acumulada correctamente", $pregunta);
    }

   public function acumularAciertoPregunta(Pregunta $pregunta):DataResponse{
      $this->repository->acumularCantidadAciertos($pregunta);
      return new DataResponse(true, "Pregunta respondida correctamente", $pregunta);
   }



}