<?php

namespace Service;

use Entity\Pregunta;
use Repository\PreguntaRepository;
use Response\DataResponse;
use Repository\UsuarioRepository;

class PreguntaService
{
    private  PreguntaRepository $repository;
    private UsuarioRepository $usuarioRepository;
    private  const  CANTIDAD_MINIMA_PARA_CALCULAR = 10;


    public function __construct(PreguntaRepository $preguntaRepository, UsuarioRepository $usuarioRepository){
        $this->repository = $preguntaRepository;
        $this->usuarioRepository = $usuarioRepository;
    }

    public function findById(int $idPregunta): DataResponse{
         $pregunta = $this->repository->find($idPregunta);
         if ($pregunta === null) {
            return new DataResponse(false, "Pregunta no encontrada por id");
         }
         return new DataResponse(true, "Pregunta por id encontrada", $pregunta);
   }

    // SE MODIFICO PARA BUSCAR PREGUNTA TAMBIEN POR DIFICULTAD
    public function getPregunta($idCategoria, $array_id_preguntas_realizadas, $userId): DataResponse
    {
        try {
            // SI EL ARRAY ES 6 O MÁS, SIGNIFICA QUE 5 YA FUERON ENTREGADAS Y RESPONDIDAS CORRECTAMENTE
            // ENTRAMOS EN INSTANCIA DE CALCULAR NIVEL PARA ENTREGAR PREGUNTA
            if (count($array_id_preguntas_realizadas) >= 6) {
                // Calculo nivel de usuario
                var_dump($this->usuarioRepository->calcularNivel($userId));
                $nivelDelUsuario = $this->determinarDificultad($this->usuarioRepository->calcularNivel($userId));

                do {
                    $preguntaObtenida = $this->repository->getPreguntaByCategoria($idCategoria, $array_id_preguntas_realizadas);

                    if ($preguntaObtenida === null) {
                        return new DataResponse(false, "No se encontró una pregunta válida");
                    }
                    $nivelDePregunta = $this->determinarDificultad($this->repository->calcularNivelDePregunta($preguntaObtenida));
                    var_dump($this->repository->calcularNivelDePregunta($preguntaObtenida));

                } while ($nivelDePregunta !== $nivelDelUsuario); // UN DO WHILE PARA REPETIR HASTA QUE COINCIDAN LOS NIVELES


                return new DataResponse(true, "Pregunta obtenida correctamente", $preguntaObtenida);
            }

            // OBTENER PREGUNTA SI EL ARRAY ES MENOR A 6
            $preguntaObtenida = $this->repository->getPreguntaByCategoria($idCategoria, $array_id_preguntas_realizadas);
            if ($preguntaObtenida === null) {
                return new DataResponse(false, "Pregunta no encontrada");
            }

            return new DataResponse(true, "Pregunta obtenida correctamente", $preguntaObtenida);

        } catch (\Exception $e) {
            throw new \Exception("Error al obtener la pregunta: " . $e->getMessage());
        }
    }

    // METODO PRIVADO PARA DETERMINAR LA DIFICULTAD DE USUARIO Y PREGUNTA
    private function determinarDificultad(float $valor): string
    {
        switch (true) {
            case ($valor >= 0 && $valor < 0.3):
                return 'DIFICIL';
            case ($valor >= 0.3 && $valor < 0.7):
                return 'MEDIO';
            case ($valor >= 0.7 && $valor <= 1):
                return 'FACIL';
            default:
                throw new \InvalidArgumentException("Valor fuera de rango: $valor");
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

   // NO SE ESTAN USANDO
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

    public function obtenerRatioPregunta(int $idPregunta): float
    {
        $pregunta = $this->repository->find($idPregunta);
        if ($pregunta === null) {
            return 0.0;
        }
        return $pregunta->getRatioPregunta();
    }
    // LO USA "UsuarioPreguntaService" pero desde ese archivo en adelante, no se usa para nada | DE PRONTO ES INUTIL
    public function obtenerPreguntasPorDificultad(string $nivel, int $idCategoria): DataResponse
    {
        try {
            $nivel = strtoupper($nivel);
            if (!in_array($nivel, ['DIFICIL', 'MEDIO', 'FACIL'])) {
                return new DataResponse(false, "Nivel de dificultad no válido: $nivel");
            }

            $preguntas = $this->repository->getPreguntasPorDificultad($nivel,$idCategoria);
            if (empty($preguntas)) {
                return new DataResponse(false, "No se encontraron preguntas de nivel $nivel");
            }

            return new DataResponse(true, "Preguntas obtenidas correctamente", $preguntas);
        } catch (\Exception $e) {
            return new DataResponse(false, "Error al obtener preguntas por dificultad: " . $e->getMessage());
        }
    }




}