<?php

namespace Repository;

use Config\Database;
use Entity\Pregunta;
use PDO;
use PDOException;
use Registry\CategoriaRegistry;
use Registry\NivelRegistry;

require_once __DIR__ . '/../Registry/CategoriaRegistry.php';
require_once __DIR__ . '/../Registry/NivelRegistry.php';
require_once __DIR__ . '/../Entity/Pregunta.php';

class PreguntaRepository
{
   private PDO $conn;

   public function __construct(){
      $this->conn = Database::connect();

      if (empty(CategoriaRegistry::getAll())) {
         CategoriaRegistry::init($this->conn);
      }
      if (empty(NivelRegistry::getAll())) {
         NivelRegistry::init($this->conn);
      }
   }


   public function find(int $id):?Pregunta
   {
      $query = "SELECT * FROM pregunta WHERE id = :id";
      try{

      $stmt = $this->conn->prepare($query);
      $stmt->execute(['id' => $id]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
         if (!$data) return null;

         $categoria = CategoriaRegistry::get($data['id_categoria']);
         $nivel = NivelRegistry::get($data['id_nivel']);

         if ($categoria === null || $nivel === null) {
            throw new \Exception("Categoría o Nivel no encontrado en Registry");
         }
         $respuestasIncorrectas = $this->getRespuestasIncorrectas($id);

         return new Pregunta($data, $categoria, $nivel, $respuestasIncorrectas);


      }catch (PDOException $e){
         throw new PDOException("No se pudo obtener la consulta:  " . $e);
      }


   }

   //El objeto Pregunta debe recibir obligatoriamente un array de respuestas_incorrectas;

   /**
    * @throws \Throwable
    */
   public function save(Pregunta $pregunta):void
   {
      $this->conn->beginTransaction();
      $query = "INSERT INTO pregunta (respuesta_correcta,id_categoria,id_nivel,enunciado) 
                VALUES (:respuesta_correcta, :id_categoria, :id_nivel, :enunciado)";
      try{
         $stmt = $this->conn->prepare($query);
         $stmt->bindValue(':respuesta_correcta', $pregunta->getRespuestaCorrecta());
         $stmt->bindValue(':id_categoria', $pregunta->getCategoria()->getId());
         $stmt->bindValue(':id_nivel', $pregunta->getNivel()->getId());
         $stmt->bindValue(':enunciado', $pregunta->getEnunciado());
         $stmt->execute();

         $pregunta->setId((int)$this->conn->lastInsertId());

         //otra query, por eso es importante el rollback por si llega a fallar algo
         //que no se ejecute ninguno
         $this->saveRespuestasIncorrectas($pregunta);

         $this->conn->commit();


      }
      catch (PDOException $e){
         // En caso de error no guardar nada y descartamos todo.
         $this->conn->rollBack();
         throw new PDOException("No se pudo guardar la pregunta: " . $e);
      }
      // Capturamos cualquier error.
      catch (\Throwable $e){
         $this->conn->rollBack();
         throw  $e;
      }



   }

   public function getRespuestasIncorrectas(int $idPregunta):array
   {
      $query = "SELECT respuesta FROM respuesta_incorrecta WHERE id_pregunta = :id";
      try{
         $stmt = $this->conn->prepare($query);
         $stmt->execute(['id' => $idPregunta]);
         return  $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

      }catch (PDOException $e){
         throw new PDOException("No se pudo obtener las respuestas incorrectas: " . $e);
      }
   }

   public function getPreguntaByCategoria(String $idCategoria):?Pregunta
   {
       $query = "SELECT * FROM pregunta WHERE id_categoria = :id_categoria";
       try{

           $stmt = $this->conn->prepare($query);
           $stmt->execute(['id_categoria' => $idCategoria]);
           $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

           $preguntaAleatoria = $data[array_rand($data)];
           $respuestasIncorrectas = $this->getRespuestasIncorrectas($preguntaAleatoria['id']);

           $categoria = new Categoria($preguntaAleatoria);
           $nivel = new Nivel($preguntaAleatoria);


           return new Pregunta($preguntaAleatoria, $categoria, $nivel, $respuestasIncorrectas);
       }catch (PDOException $e){
           throw new PDOException("No se pudo obtener la consulta:  " . $e);
       }
   }

}