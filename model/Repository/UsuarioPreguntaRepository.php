<?php

namespace Repository;

use Config\Database;
use Entity\UsuarioPregunta;
use PDO;
use PDOException;

class UsuarioPreguntaRepository
{

   private PDO $conn;

   public function __construct()
   {
      $this->conn = Database::connect();
   }

   public function save(UsuarioPregunta $usuarioPregunta): void{
      $sql = "INSERT INTO usuario_pregunta (id_usuario, id_pregunta) 
              VALUES (:id_usuario, :id_pregunta)";
      try{

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':id_usuario', $usuarioPregunta->getIdUsuario(), PDO::PARAM_INT);
      $stmt->bindValue(':id_pregunta', $usuarioPregunta->getIdPregunta(), PDO::PARAM_INT);
      $stmt->execute();
      }catch(PDOException $e){
         throw new PDOException("Error al guardar la pregunta del usuario: " . $e->getMessage());
      }
   }

   //devuelve los ids de las preguntas asociadas al usuario
   public function getPreguntasIdByUsuario(int $idUsuario): array
   {
      $sql = "SELECT * FROM usuario_pregunta WHERE id_usuario = :id_usuario";
      try{

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
      }catch(PDOException $e){
         throw new PDOException("Error al obtener las preguntas del usuario: " . $e->getMessage());
      }
   }
   public function delete(UsuarioPregunta $usuarioPregunta): void
   {
      $sql = "DELETE FROM usuario_pregunta WHERE id_usuario = :id_usuario AND id_pregunta = :id_pregunta";
      try{

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':id_usuario', $usuarioPregunta->getIdUsuario(), PDO::PARAM_INT);
      $stmt->bindValue(':id_pregunta', $usuarioPregunta->getIdPregunta(), PDO::PARAM_INT);
      $stmt->execute();
      }catch(PDOException $e){
         throw new PDOException("Error al eliminar la pregunta del usuario: " . $e->getMessage());
      }
   }

}