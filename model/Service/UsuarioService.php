<?php

namespace Service;

use Entity\Usuario;
use Response\DataResponse;
use UsuarioRepository;

class UsuarioService
{
   private UsuarioRepository $repository;

   public function __construct($usuarioRepository)
   {
      $this->repository = $usuarioRepository;
   }
   public function save(Usuario $usuario): DataResponse
   {
      try{
         $this->repository->save($usuario);
         return new DataResponse(true, "Usuario guardado correctamente", $usuario);
      }catch (\Exception $e){
         return new DataResponse(false, "Error al guardar el usuario: " . $e->getMessage());
      }


   }

}