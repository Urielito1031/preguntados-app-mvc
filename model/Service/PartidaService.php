<?php

namespace Service;

use Entity\Partida;
use Repository\PartidaRepository;
use Response\DataResponse;

class PartidaService{

    private PartidaRepository $partidaRepository;

    public function __construct(PartidaRepository $partidaRepository){
        $this->partidaRepository = $partidaRepository;
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
}