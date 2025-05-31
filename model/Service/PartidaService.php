<?php

use Repository\PreguntaRepository;
use Repository\UsuarioRepository;

class PartidaService
{
    private PreguntaRepository $preguntaRepository;
    private UsuarioRepository $usuarioRepository;

    public function __construct(PreguntaRepository $preguntaRepository, UsuarioRepository $usuarioRepository){
        $this->preguntaRepository = $preguntaRepository;
        $this->usuarioRepository = $usuarioRepository;
    }



}