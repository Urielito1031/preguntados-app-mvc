<?php

namespace Service;

use Response\DataResponse;

use Repository\CategoriaRepository;

class CategoriaService{

    private CategoriaRepository $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository){
        $this->categoriaRepository = $categoriaRepository;
    }

    public function getCategorias(){

        $categorias = $this->categoriaRepository->getAll();
        return $categorias;
    }

}