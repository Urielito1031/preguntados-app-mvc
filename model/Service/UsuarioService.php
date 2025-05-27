<?php

namespace Service;

use Entity\Usuario;
use Service\ImageService;
use Repository\UsuarioRepository;
use Response\DataResponse;

require_once __DIR__ . '/../Response/DataResponse.php';
class UsuarioService
{
   private UsuarioRepository $repository;


   public function __construct($usuarioRepository)
   {
      $this->repository = $usuarioRepository;

   }
   public function save(Usuario $usuario): DataResponse
   {
      try {
         if ($this->repository->findByEmail($usuario->getCorreo())) {
            return new DataResponse(false, "El email ya está registrado.");
         }
         if ($this->repository->findByUsername($usuario->getNombreUsuario())) {
            return new DataResponse(false, "El nombre de usuario ya está en uso.");
         }

          $this->repository->save($usuario);
         return new DataResponse(true, "Usuario guardado correctamente", $usuario);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al guardar el usuario: " . $e->getMessage());
      }
   }

   public function login(string $email, string $password): DataResponse
   {
      if(empty(trim($email)) || empty(trim($password))){
         return new DataResponse(false, "Email y contraseña son obligatorios.");
      }

      if(!$this->validateEmail($email)){
         return new DataResponse(false, "El email no es válido.");
      }
      try {
         $user = $this->repository->findByEmail($email);
         if (!$user) {
            return new DataResponse(false, "Usuario no encontrado.");
         }
         if (!$user->getCuentaValidada()) {
            return new DataResponse(false, "Cuenta no validada. Revisa tu email.");
         }
         if (!$user->verificarContrasenia($password)) {
            return new DataResponse(false, "Contraseña incorrecta.");
         }
         return new DataResponse(true, "Login exitoso", $user);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al iniciar sesión: " . $e->getMessage());
      }
   }

   public function activateAccount(string $email): DataResponse
   {
      try {
         $user = $this->repository->findByEmail($email);
         if (!$user) {
            return new DataResponse(false, "Usuario no encontrado.");
         }
         if ($user->getCuentaValidada()) {
            return new DataResponse(false, "La cuenta ya está validada.");
         }
         $this->repository->activateAccount($email);
         return new DataResponse(true, "Cuenta activada con éxito.");
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al activar la cuenta: " . $e->getMessage());
      }
   }

   private function validateEmail(string $email): bool
   {
      return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
   }


}