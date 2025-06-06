<?php

namespace Service;

use Entity\Pregunta;
use Repository\PreguntaRepository;
use Response\DataResponse;

class PreguntaService
{
    private  PreguntaRepository $repository;
    private  const  CANTIDAD_MINIMA_PARA_CALCULAR = 10;


    public function __construct(PreguntaRepository $preguntaRepository){
        $this->repository = $preguntaRepository;
    }



   public function calcularNivelPregunta(Pregunta $pregunta):DataResponse
    {
       if(self::CANTIDAD_MINIMA_PARA_CALCULAR > $pregunta->getCantidadJugada()){
            return new DataResponse(false, "No se puede calcular el nivel de la pregunta, porque no se ha jugado lo suficiente");

       }

       $ratio = $pregunta->getRatioPregunta();
       $dificultad = match (true) {
          $ratio > 0 && $ratio < 0.3 => 'DIFICIL',
          $ratio >= 0.3 && $ratio < 0.7 => 'MEDIO',
          $ratio >= 0.7 && $ratio <= 1 => 'FACIL',
          default => 'INDETERMINADO'
       };

       if ($dificultad === 'INDETERMINADO') {
          return new DataResponse(false, "Nivel no determinado");
       }

       return new DataResponse(true, $dificultad, $ratio);
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
   public function obtenerPreguntasPorDificultad(string $nivel): DataResponse
   {
      try {
         $nivel = strtoupper($nivel);
         if (!in_array($nivel, ['DIFICIL', 'MEDIO', 'FACIL'])) {
            return new DataResponse(false, "Nivel de dificultad no válido: $nivel");
         }

         $preguntas = $this->repository->getPreguntasPorDificultad($nivel);
         if (empty($preguntas)) {
            return new DataResponse(false, "No se encontraron preguntas de nivel $nivel");
         }

         return new DataResponse(true, "Preguntas obtenidas correctamente", $preguntas);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al obtener preguntas por dificultad: " . $e->getMessage());
      }
   }

   public function findById(int $idPregunta): DataResponse{
         $pregunta = $this->repository->find($idPregunta);
         if ($pregunta === null) {
            return new DataResponse(false, "Pregunta no encontrada por id");
         }
         return new DataResponse(true, "Pregunta por id encontrada", $pregunta);
   }




    public function getPregunta($idCategoria, $array_id_preguntas_realizadas): DataResponse
    {
       try{

        $preguntaObtenida = $this->repository->getPreguntaByCategoria($idCategoria,$array_id_preguntas_realizadas);
        if($preguntaObtenida === null){
           return new DataResponse(false, "Pregunta no encontrada");

        }
        return new DataResponse(true, "Pregunta obtenida correctamente", $preguntaObtenida);
       }catch (\Exception $e){
            throw new \Exception("Error al obtener la pregunta: " . $e->getMessage());
       }
    }

   public function acumularPreguntaJugada(Pregunta $pregunta): DataResponse
   {
      try {
         $this->repository->acumularPreguntaJugada($pregunta);
         return new DataResponse(true, "Pregunta acumulada correctamente", $pregunta);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al acumular pregunta jugada: " . $e->getMessage());
      }
   }

   public function acumularAciertoPregunta(Pregunta $pregunta): DataResponse
   {
      try {
         $this->repository->acumularCantidadAciertos($pregunta);
         return new DataResponse(true, "Pregunta respondida correctamente", $pregunta);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al acumular acierto: " . $e->getMessage());
      }
   }



}