<?php


use Entity\Ciudad;
use Repository\CiudadRepository;
use Repository\PaisRepository;

class UbicacionService
{
    private PaisRepository $paisRepository;
    private CiudadRepository $ciudadRepository;

    public function __construct(PaisRepository $paisRepository, CiudadRepository $ciudadRepository)
    {
        $this->paisRepository = $paisRepository;
        $this->ciudadRepository = $ciudadRepository;
    }

    public function processUbication($nombrePais, $nombreCiudad): Ciudad
    {
        $pais = $this->paisRepository->findOrCreate($nombrePais);
        $ciudad = $this->ciudadRepository->findOrCreate($pais->getId(), $nombreCiudad);
        $ciudad->setIdPais($pais->getId());
        return $ciudad;
    }

    public function getPaisRepository(): PaisRepository
    {
        return $this->paisRepository;
    }


    public function getCiudadRepository(): CiudadRepository
    {
        return $this->ciudadRepository;
    }

}