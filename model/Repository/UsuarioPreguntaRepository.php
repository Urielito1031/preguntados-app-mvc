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

      $this->conn->beginTransaction();
      $sql = "INSERT INTO usuario_pregunta (id_usuario, id_pregunta) 
              VALUES (:id_usuario, :id_pregunta)";
      try{

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':id_usuario', $usuarioPregunta->getIdUsuario(), PDO::PARAM_INT);
      $stmt->bindValue(':id_pregunta', $usuarioPregunta->getIdPregunta(), PDO::PARAM_INT);
      $stmt->execute();
      $this->conn->commit();
      }catch(PDOException $e){
         $this->conn->rollBack();
         throw new PDOException("Error al guardar la pregunta del usuario: " . $e->getMessage());
      }
   }
   //obtenerPreguntaAleatoria no respondida por usuario
   public function getPreguntaIdRandomNoRespondida(int $idUsuario): ?int
   {
      try {
         $preguntasRealizadas = $this->getPreguntasIdByUsuario($idUsuario);

         // Si ya respondió todas las preguntas disponibles
         if (count($preguntasRealizadas) >= 50) { //DATO HARDCODEADO
            return null;
         }

         //query qeu selecciona una pregunta aleatoria que no ha sido respondida por el usuario
         $sql = "SELECT id 
                FROM pregunta 
                WHERE id NOT IN (
                    SELECT id_pregunta 
                    FROM usuario_pregunta 
                    WHERE id_usuario = :id_usuario
                )
                ORDER BY RAND()
                LIMIT 1";

         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
         $stmt->execute();

         return $stmt->fetchColumn() ?: null;

      } catch(PDOException $e) {
         throw new PDOException("Error al obtener pregunta aleatoria no respondida: " . $e->getMessage());
      }
   }

   public function getPreguntasIdByUsuario(int $idUsuario): array
   {
      $sql = "SELECT id_pregunta FROM usuario_pregunta WHERE id_usuario = :id_usuario";
      try{

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_COLUMN,0);
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

   //se llama cuando el usuario ya respondió todas las preguntas del juego
   public function resetearPreguntasParaUsuario(int $idUsuario)
   {
      $sql = "DELETE FROM usuario_pregunta WHERE id_usuario = :id_usuario";
      try{

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
      $stmt->execute();
      }catch(PDOException $e){
         throw new PDOException("Error al resetear las preguntas del usuario: " . $e->getMessage());
      }
   }

}