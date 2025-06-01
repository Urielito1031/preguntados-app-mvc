<?php

namespace Service;

use Repository\PartidaRepository;
class PartidaService{

    private $partidaRepository;

    public function __construct(PartidaRepository $partidaRepository){
        $this->partidaRepository = $partidaRepository;
    }

    public function finalizarPartida($id_usuario, $puntaje,$estado, $preguntas_correctas){

        $this->partidaRepository->saveGame($id_usuario, $puntaje,$estado, $preguntas_correctas);
    }
}