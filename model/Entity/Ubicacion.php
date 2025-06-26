<?php

namespace Entity;

class Ubicacion
{
    private ?int $id;
    private Pais $pais;
    private Ciudad $ciudad;

    public function __construct(Pais $pais , Ciudad $ciudad)
    {
        $this->pais = $pais ?? null;
        $this->ciudad =$ciudad ?? null;
    }

    public function getPais(): Pais
    {
        return $this->pais;
    }

    public function setPais(Pais $pais): void
    {
        $this->pais = $pais;
    }

    public function getCiudad(): Ciudad
    {
        return $this->ciudad;
    }

    public function setCiudad(Ciudad $ciudad): void
    {
        $this->ciudad = $ciudad;
    }

}
