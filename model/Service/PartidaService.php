<?php

namespace Service;

use Repository\PartidaRepository;
class PartidaService{

    private $partidaRepository;

    public function __construct(PartidaRepository $partidaRepository){
        $this->partidaRepository = $partidaRepository;
    }

    public function guardarPartida(): ?\Entity\Partida
    {
        return false;
    }
}