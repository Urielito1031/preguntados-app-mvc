<?php

namespace Entity;

class Ciudad
{
      private ?int $id;
      private string $nombre;
      private int $idPais;

      public function __construct(array $data)
      {
         $this->id = $data['id'] ?? null;
         $this->nombre = $data['nombre'];
         $this->idPais  = $data['id_pais']?? null ;
      }

      public function getId(): ?int
      {
         return $this->id;
      }

      public function getNombre(): string
      {
         return $this->nombre;
      }

      public function getIdPais(): int
      {
         return $this->idPais;
      }
   public function setId(int $id): void
   {
      $this->id = $id;
   }

   public function setIdPais(?int $idPais)
   {
      $this->idPais = $idPais;
   }

}