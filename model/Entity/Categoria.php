<?php

namespace Entity;

class Categoria
{
   private int $id;
   private string $descripcion;
   private string $color;
   private string $urlImagen;

   public function __construct(array $data)
   {
      $this->id = $data['id'];
      $this->descripcion = $data['descripcion'] ?? '';
      $this->color = $data['color'] ?? '';
      $this->urlImagen = $data['url_imagen'] ?? '';
   }
   public function getId(): int
   {
      return $this->id;
   }
   public function setId(int $id): void
   {
      $this->id = $id;
   }
   public function getDescripcion(): string
   {
      return $this->descripcion;
   }

   public function getColor(): string
   {
      return $this->color;
   }
   public function getUrlImagen(): string
   {
      return $this->urlImagen;
   }



}