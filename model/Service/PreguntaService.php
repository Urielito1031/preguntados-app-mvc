<?php

namespace Service;

use Entity\Pregunta;
use Repository\PreguntaRepository;
use Response\DataResponse;

class PreguntaService
{
    private $preguntaRepository;
    private const int CANTIDAD_MINIMA_PARA_CALCULAR = 10;
    private array $niveles = [
        "DIFICIL" => [],
        "MEDIO" =>[],
        "FACIL" =>[]
    ];

    public function __construct(PreguntaRepository $preguntaRepository){
        $this->preguntaRepository = $preguntaRepository;
    }



   public function calcularNivelPregunta(Pregunta $pregunta):DataResponse
    {
       if(self::CANTIDAD_MINIMA_PARA_CALCULAR > $pregunta->getCantidadJugada()){
            return new DataResponse(false, "No se puede calcular el nivel de la pregunta, porque no se ha jugado lo suficiente");

       }
       $ratio = $this->preguntaRepository->obtenerRatioPregunta($pregunta->getId());
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
   public function obtenerPreguntasPorDificultad(string $nivel, ?string $idCategoria = null): DataResponse
   {
      try {
         $nivel = strtoupper($nivel);
         if (!in_array($nivel, ['DIFICIL', 'MEDIO', 'FACIL'])) {
            return new DataResponse(false, "Nivel de dificultad no válido: $nivel");
         }

         $preguntas = $this->preguntaRepository->getPreguntasPorDificultad($nivel, $idCategoria);
         if (empty($preguntas)) {
            return new DataResponse(false, "No se encontraron preguntas de nivel $nivel" . ($idCategoria ? " en la categoría $idCategoria" : ""));
         }

         return new DataResponse(true, "Preguntas obtenidas correctamente", $preguntas);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al obtener preguntas por dificultad: " . $e->getMessage());
      }
   }



   public function calcularRatioDificultad(): DataResponse
   {
      try {
         // Reiniciar los niveles para este cálculo
         $this->niveles = [
            "DIFICIL" => [],
            "MEDIO" => [],
            "FACIL" => []
         ];

         // Obtener preguntas por nivel de dificultad
         $preguntasDificiles = $this->preguntaRepository->getPreguntasPorDificultad("DIFICIL");
         $preguntasMedias = $this->preguntaRepository->getPreguntasPorDificultad("MEDIO");
         $preguntasFaciles = $this->preguntaRepository->getPreguntasPorDificultad("FACIL");

         // Llenar los niveles con los IDs
         foreach ($preguntasDificiles as $pregunta) {
            $this->niveles["DIFICIL"][] = $pregunta->getId();
         }
         foreach ($preguntasMedias as $pregunta) {
            $this->niveles["MEDIO"][] = $pregunta->getId();
         }
         foreach ($preguntasFaciles as $pregunta) {
            $this->niveles["FACIL"][] = $pregunta->getId();
         }

         $totalPreguntas = count($preguntasDificiles) + count($preguntasMedias) + count($preguntasFaciles);
         if ($totalPreguntas === 0) {
            return new DataResponse(false, "No hay preguntas con suficientes jugadas para calcular el ratio de dificultad");
         }

         $ratios = [
            "DIFICIL" => (count($preguntasDificiles) / $totalPreguntas) * 100,
            "MEDIO" => (count($preguntasMedias) / $totalPreguntas) * 100,
            "FACIL" => (count($preguntasFaciles) / $totalPreguntas) * 100
         ];

         return new DataResponse(true, "Ratios de dificultad calculados", $ratios);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al calcular el ratio de dificultad: " . $e->getMessage());
      }
   }

    public function getPregunta($idCategoria, $array_id_preguntas_realizadas): Pregunta
    {
        return $this->preguntaRepository->getPreguntaByCategoria($idCategoria,$array_id_preguntas_realizadas);
    }

    public function acumularPreguntaJugada(Pregunta $pregunta):DataResponse{
       $this->preguntaRepository->acumularPreguntaJugada($pregunta);
       return new DataResponse(true, "Pregunta acumulada correctamente", $pregunta);
    }

   public function acumularAciertoPregunta(Pregunta $pregunta):DataResponse{
      $this->preguntaRepository->acumularCantidadAciertos($pregunta);
      return new DataResponse(true, "Pregunta respondida correctamente", $pregunta);
   }



}