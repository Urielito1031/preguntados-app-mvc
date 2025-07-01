<?php

namespace Service;

use Entity\Usuario;
use MailerService;
use Service\ImageService;
use Repository\UsuarioRepository;
use Response\DataResponse;

require_once ('core/MailerService.php');
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
          $tokenParaValidar = $this->generarToken();
          $mailer = new MailerService();
          $mailer->enviarValidacion($usuario->getCorreo(), $tokenParaValidar);

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

   public function getRanking(){
       return $this->repository->getRanking();
   }

   public function getHistorialDePartidas($userId) {
       return $this->repository->getHistorialDePartidas($userId);
   }

   public function findById(int $idUsuario):DataResponse{
      try {
         $usuario = $this->repository->findById($idUsuario);
         if ($usuario === null) {
            return new DataResponse(false, "Usuario no encontrado.");
         }
         return new DataResponse(true, "Usuario encontrado.", $usuario);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al buscar el usuario: " . $e->getMessage());
      }
   }

   //duda si dejarlo o no, porque podria usarse cuando
   // solo tenemos el id y no queremos el usuario directamente;
   public function obtenerNivelUsuario(int $idUsuario): float
   {
      $response = $this->findById($idUsuario);
      if (!$response->success) {
         return 0.0;
      }
      $usuario = $response->data;
      return $usuario->getNivel();
   }
   public function sumarPreguntaEntregada(Usuario $usuario): DataResponse
   {
      try {
         $usuario->setPreguntasEntregadas($usuario->getPreguntasEntregadas() + 1);
         $this->repository->incrementarPreguntaEntregada($usuario);
         return new DataResponse(true, "Pregunta entregada correctamente.", $usuario);
      } catch (\Exception $e) {
         return new DataResponse(false, "Error al incrementar la pregunta del usuario: " . $e->getMessage());
      }
   }

   public function sumarRespuestaCorrecta(Usuario $usuario):DataResponse
   {
      try {

      $this->repository->sumarRespuestaCorrecta($usuario);
      return new DataResponse(true, "Respuesta enviada correctamente.", $usuario);

      }catch( \Exception $e) {
         return new DataResponse(false, "Error al sumar respuesta correcta: " . $e->getMessage());
      }
   }

    public function obtenerIdCiudadDeUsuario(int $idUsuario): int
    {
        return $this->repository->obtenerIdCiudadDeusuario($idUsuario);
    }

    private function generarToken() : int
    {
        return random_int(100000, 999999);

    }
}