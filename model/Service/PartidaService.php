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
    public function iniciarPartida(int $idUsuario):DataResponse
    {
       $usuario = $this->usuarioRepository->findById($idUsuario);
         if (!$usuario) {
               return new DataResponse(false, "Usuario no encontrado");
         }

         $partida = new Partida($usuario);

         $this->partidaRepository->saveGame($partida);
         return new DataResponse(true, "Partida iniciada correctamente", $partida);

    }


    public function sumarPuntaje(Partida $partida, int $puntaje):DataResponse
    {
       $this->partidaRepository->sumarPuntaje($partida->getPuntaje(), $partida);

       return new DataResponse(true, "Acumulaste ". $puntaje." punto/s", $partida);
    }

    public function finalizarPartida(Partida $partida):DataResponse
    {
       $this->partidaRepository->updatePartida($partida);

       return new DataResponse(true, "La partida finalizó", $partida);
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