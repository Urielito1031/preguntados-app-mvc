<?php

namespace Repository;
use Config\Database;
use Entity\Usuario;
use PDO;
use PDOException;

require_once __DIR__ . '/../Entity/Usuario.php'; 

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

  public function incrementarPreguntaEntregada(Usuario $usuario): void
   {
      $sql = "UPDATE usuario
                SET preguntas_entregadas = preguntas_entregadas + 1 
                WHERE id_usuario = :id";
      try{
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':id', $usuario->getId(), PDO::PARAM_INT);
         $stmt->execute();
         $usuario->setPreguntasEntregadas($usuario->getPreguntasEntregadas() + 1);

      }catch(PDOException $e){
         throw new PDOException("Error al incrementar preguntas entregadas: ".$e->getMessage());
      }
   }
   public function sumarRespuestaCorrecta(Usuario $usuario): void
   {
      $sql = "UPDATE usuario
                SET respondidas_correctamente = respondidas_correctamente + 1 
                WHERE id_usuario = :id";
      try{
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':id', $usuario->getId(), PDO::PARAM_INT);
         $stmt->execute();
         $usuario->setRespondidasCorrectamente($usuario->getRespondidasCorrectamente() + 1);

      }catch(PDOException $e){
         throw new PDOException("Error al sumar respuesta correcta: ".$e->getMessage());
      }
   }

   public function findById(int $id_usuario): ?Usuario
   {
      $query = "SELECT * FROM usuario WHERE id_usuario = :id";

      try{
         $stmt = $this->conn->prepare($query);
         $stmt->bindValue(':id', $id_usuario, PDO::PARAM_INT);
         $stmt->execute();
         $data = $stmt->fetch(PDO::FETCH_ASSOC);

         if (!$data) {
             return null;
         }

         return new Usuario($data);

      }catch (PDOException $e){
         throw new PDOException("Error al buscar usuario por ID: " . $e->getMessage());
      }

   }

    public function getRanking()
    {
        $sql = "SELECT u.id_usuario AS id_usuario, u.nombre, SUM(p.puntaje) AS puntaje_total, COUNT(*) AS partidas_jugadas
                FROM partida p
                JOIN usuario u ON p.id_usuario = u.id_usuario
                GROUP BY u.id_usuario, u.nombre
                ORDER BY puntaje_total DESC; ";
        try{
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            throw new PDOException("Error al cargar ranking: ".$e->getMessage());
        }

    }

    public function getHistorialDePartidas($userId) {
        $sql = "SELECT puntaje AS puntaje
                    FROM partida p
                    WHERE id_usuario = :id_usuario
                    ORDER BY creado_en DESC";
        try{
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_usuario', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            throw new PDOException("Error al cargar historial: ".$e->getMessage());
        }

    }

    public function calcularNivel($userId)
    {
        $query = "SELECT respondidas_correctamente / preguntas_entregadas  AS ratio, preguntas_entregadas
                    FROM usuario
                    WHERE id_usuario = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) {
                return null;
            }

            if ($data['preguntas_entregadas'] == 0) {
                return 1; // RETORNA NIVEL FACIL
            }

            return $data['ratio'];
        } catch (PDOException $e) {
            throw new PDOException("Error al buscar nivel de usuario por ID: " . $e->getMessage());
        }
    }

    public function obtenerIdCiudadDeusuario(int $idUsuario) : int {
        $sql = "SELECT id_ciudad FROM usuario 
               WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $idCiudadObtenido = (int) $stmt->fetchColumn();

        return $idCiudadObtenido;
    }



}