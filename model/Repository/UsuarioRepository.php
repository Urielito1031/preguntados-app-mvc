<?php

use Config\Database;
use Entity\Usuario;

class UsuarioRepository

{
   private PDO $conn;

   public function __construct()
   {
      $this->conn = Database::connect();
   }
   public function save(Usuario $usuario): bool
   {
      $sql = "INSERT INTO usuario( nombre,apellido,fecha_nacimiento,sexo,correo,contrasenia,nombre_usuario,url_foto_perfil,url_qr,id_rol,id_ciudad,id_nivel,puntaje_total,cuenta_validada)
                VALUES(:nombre,:apellido,:fecha_nacimiento,:sexo,:correo,:contrasenia,:nombre_usuario,:url_foto_perfil,:url_qr,:id_rol,:id_ciudad,:id_nivel,:puntaje_total,:cuenta_validada)";

      try
      {
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':nombre', $usuario->getNombre());
         $stmt->bindValue(':apellido', $usuario->getApellido());
         $fechaNacimiento = $usuario->getFechaNacimiento()?->format('Y-m-d');
         $stmt->bindValue(':fecha_nacimiento', $fechaNacimiento);
         $stmt->bindValue(':sexo', $usuario->getSexo());
         $stmt->bindValue(':correo', $usuario->getCorreo());
         $stmt->bindValue(':contrasenia', $usuario->getContraseniaHash());
         $stmt->bindValue(':nombre_usuario', $usuario->getNombreUsuario());
         $stmt->bindValue(':url_foto_perfil', $usuario->getUrlFotoPerfil());
         $stmt->bindValue(':url_qr', $usuario->getUrlQr());
         $stmt->bindValue(':id_rol', $usuario->getIdRol(), PDO::PARAM_INT);
         $stmt->bindValue(':id_ciudad', $usuario->getIdCiudad(), PDO::PARAM_INT);
         $stmt->bindValue(':id_nivel', $usuario->getIdNivel(), PDO::PARAM_INT);
         $stmt->bindValue(':puntaje_total', $usuario->getPuntajeTotal(), PDO::PARAM_INT);
         $stmt->bindValue(':cuenta_validada', $usuario->getCuentaValidada(), PDO::PARAM_BOOL);
         $stmt->execute();

         $usuario->setId((int)$this->conn->lastInsertId());
         return true;

      }catch(PDOException $e){
         throw  new PDOException("Error al guardar usuario: ".$e->getMessage());
      }
   }

   public function findByEmail(string $email): ?Usuario
   {
      $sql = "SELECT * FROM usuario WHERE correo = :email";
      try{
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':email', $email, PDO::PARAM_STR);
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);

         return $row? new Usuario($row): null;

      }catch(PDOException $e){
         throw new PDOException("Error al buscar usuario por correo: ".$e->getMessage());
      }
   }
   public function findByUsername(string $username): ?Usuario
   {
      $sql = "SELECT * FROM usuario WHERE nombre_usuario = :username";
      try{
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':username', $username, PDO::PARAM_STR);
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);

         return $row? new Usuario($row): null;

      }catch(PDOException $e){
         throw new PDOException("Error al buscar usuario por nombre de usuario: ".$e->getMessage());
      }
   }

   public function activateAccount(string $email): void{
      $sql = "UPDATE usuario SET cuenta_validada = 1 WHERE correo = :email";
      try{
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':email', $email, PDO::PARAM_STR);
         $stmt->execute();
      }catch(PDOException $e){
         throw new PDOException("Error al activar cuenta: ".$e->getMessage());
      }

   }


}