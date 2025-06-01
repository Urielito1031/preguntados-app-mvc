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
         // Validaciones para cada campo
         if (empty(trim($usuario->getNombre()))) {
            return new DataResponse(false, "El nombre es obligatorio.");
         }

         if (empty(trim($usuario->getApellido()))) {
            return new DataResponse(false, "El apellido es obligatorio.");
         }
         if(empty($usuario->getFechaNacimiento())) {
            return new DataResponse(false, "La fecha de nacimiento es obligatoria.");
         }
         if(!$usuario->getUrlFotoPerfil()){
            return new DataResponse(false, "La foto de perfil es obligatoria.");
         }

         if (empty(trim($usuario->getNombreUsuario()))) {
            return new DataResponse(false, "El nombre de usuario es obligatorio.");
         }

         if (strlen($usuario->getNombreUsuario()) < 3) {
            return new DataResponse(false, "El nombre de usuario debe tener al menos 3 caracteres.");
         }

         if (empty(trim($usuario->getCorreo()))) {
            return new DataResponse(false, "El correo es obligatorio.");
         }

         if ($usuario->getFechaNacimiento() === null || $usuario->getFechaNacimiento()->format('Y') > date('Y') - 13) {
            return new DataResponse(false, "Debes tener al menos 13 años para registrarte.");
         }


         if (!filter_var($usuario->getCorreo(), FILTER_VALIDATE_EMAIL)) {
            return new DataResponse(false, "El correo no es válido.");
         }

         // Validar contraseñas (comparar con repetir_contrasenia del POST)
         $passwordRecibido = $_POST['contrasenia'] ?? '';
         $repetirContrasenia = $_POST['repetir_contrasenia'] ?? '';
         if (empty($passwordRecibido) || empty($repetirContrasenia)) {
            return new DataResponse(false, "Ambas contraseñas son obligatorias.");
         }

         if ($passwordRecibido !== $repetirContrasenia) {
            return new DataResponse(false, "Las contraseñas no coinciden.");
         }

         if (strlen($passwordRecibido) < 8) {
            return new DataResponse(false, "La contraseña debe tener al menos 8 caracteres.");
         }

         if (empty($usuario->getSexo()) ) {
            return new DataResponse(false, "Debes seleccionar un género.");
         }

         if (empty(trim($_POST['pais'] ?? '')) && empty(trim($_POST['ciudad'] ?? ''))) {
            return new DataResponse(false, "Debes ingresar al menos un país o ciudad.");
         }

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