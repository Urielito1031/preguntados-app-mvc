<?php

namespace Service;

use Entity\Partida;
use Entity\Usuario;
use Repository\PartidaRepository;
use Repository\UsuarioRepository;
use Response\DataResponse;

require_once( __DIR__ . "/../Entity/Partida.php");

class PartidaService{

    private PartidaRepository $partidaRepository;
    private UsuarioRepository $usuarioRepository;

    public function __construct(PartidaRepository $partidaRepository){
        $this->partidaRepository = $partidaRepository;
         $this->usuarioRepository = new UsuarioRepository();
    }

    public function save(Partida $partidaAGuardar): DataResponse
    {
       if(!$partidaAGuardar->getUsuario()){
          return new DataResponse(false, "La partida debe tener un usuario asociado");
       }
       if($partidaAGuardar->getEstado() != "GANADA" && $partidaAGuardar->getEstado() != "PERDIDA"){
         return new DataResponse(false, "El partida debe haber finalizado GANADA o PERDIDA");
       }
         if($partidaAGuardar->getCantidadPreguntasCorrectas() < 0) {
            return new DataResponse(false, "La cantidad de preguntas correctas no puede ser negativa");
         }
         if($partidaAGuardar->getPuntaje() < 0) {
            return new DataResponse(false, "El puntaje no puede ser negativo");
         }
         try {
            $this->partidaRepository->saveGame($partidaAGuardar);

            return new DataResponse(true, "Partida registrada correctamente", $partidaAGuardar);
         }catch (\Exception $e){
            return new DataResponse(false, "Error al guardar la partida: " . $e->getMessage());
         }
    }
   public function finalizarPartida(int $userId, array $preguntasCorrectas, array $preguntasRealizadas): DataResponse
   {
      try {
         // Calcular puntaje y estado
         $puntaje = $this->calcularPuntaje($preguntasCorrectas);
         $estado = $this->calcularEstado($preguntasCorrectas);
         $preguntasCorrectasCount = count($preguntasCorrectas);

         $usuario = $this->usuarioRepository->findById($userId);
         if (!$usuario) {
            return new DataResponse(false, "Usuario no encontrado");
         }
         // Crear objeto Partida
         $partida = new Partida(
            [
               'id' => 0,
               'puntaje' => $puntaje,
               'estado' => $estado,
               'preguntas_correctas' => $preguntasCorrectasCount,
            ],
            $usuario
         );

         // Guardar la partida
         return $this->save($partida);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al finalizar la partida: " . $e->getMessage());
      }
   }

   private function calcularPuntaje(array $preguntasCorrectas): int
   {
      return count($preguntasCorrectas);
   }

   private function calcularEstado(array $preguntasCorrectas): string
   {
      return count($preguntasCorrectas) > 0 ? 'GANADA' : 'PERDIDA';
   }
}