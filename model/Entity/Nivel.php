<?php

namespace Entity;

class Nivel
{
   private int $id;
   private string $nombre;

   public function __construct(array $data)
   {
      $this->id = $data['id_nivel'];
      $this->nombre = $data['nombre_nivel'] ?? '';
   }
   public function getId(): int
   {
      return $this->id;
   }
   public function setId(int $id): void
   {
      $this->id = $id;
   }
   public function getNombre(): string
   {
      return $this->nombre;
   }

}