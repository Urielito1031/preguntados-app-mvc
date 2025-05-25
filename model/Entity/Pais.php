<?php

namespace Entity;

class Pais
{
   private ?int $id;
   private string $nombre;


   public function __construct(array $data)
   {
      $this->id = $data['id'] ?? null;
      $this->nombre = $data['nombre'] ?? '';
   }
   public function getId(): ?int
   {
      return $this->id;
   }
   public function getNombre(): string
   {
      return $this->nombre;
   }

}