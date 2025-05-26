<?php


use Entity\Ciudad;
use Repository\CiudadRepository;
use Repository\PaisRepository;

class UsuarioService
{
private PaisRepository $paisRepository;
private CiudadRepository $ciudadRepository;

public function __construct(PaisRepository $paisRepository, CiudadRepository $ciudadRepository){
    $this->paisRepository = $paisRepository;
    $this->ciudadRepository = $ciudadRepository;
}

public function processUbication($nombrePais,$nombreCiudad):Ciudad{
    $pais = $this->paisRepository->findOrCreate($nombrePais);
    return $this->ciudadRepository->findOrCreate($pais->getId(),$nombreCiudad);
}

}

?>