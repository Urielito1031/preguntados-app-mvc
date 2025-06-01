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


   private function saveRespuestasIncorrectas(Pregunta $pregunta): void
   {
      $query = "INSERT INTO respuesta_incorrecta (respuesta, id_pregunta) 
                VALUES (:respuesta, :id_pregunta)";
      try {

         $stmt = $this->conn->prepare($query);
         foreach ($pregunta->getRespuestasIncorrectas() as $respuesta) {
            $stmt->bindValue(':respuesta', $respuesta);
            $stmt->bindValue(':id_pregunta', $pregunta->getId());
            $stmt->execute();
         }
      } catch (PDOException) {
         throw new PDOException("No se pudo guardar las respuestas incorrectas: " .$e);
      }
   }
   public function getPreguntaByCategoria(string $idCategoria, array $array_id_preguntas_realizadas): ?Pregunta
   {
      try {
         $params = [':id_categoria' => $idCategoria];
         $query = "SELECT * FROM pregunta WHERE id_categoria = :id_categoria";

         // Filtra el array para asegurarse de que solo contiene enteros.
         $array_id_preguntas_realizadas = array_filter($array_id_preguntas_realizadas, 'is_int');

         if (!empty($array_id_preguntas_realizadas)) {
            // Crea placeholders para los IDs a excluir para prevenir inyecciones SQL.
            $placeholders = [];
            foreach ($array_id_preguntas_realizadas as $key => $id) {
               $placeholder = ":exclude_{$key}";
               $placeholders[] = $placeholder;
               $params[$placeholder] = $id;
            }
            $query .= " AND id NOT IN (" . implode(',', $placeholders) . ")";
         }

         $stmt = $this->conn->prepare($query);
         $stmt->execute($params);

         $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

         if (empty($data)) {
            return null;
         }

         // Selecciona una pregunta aleatoria del conjunto de resultados.
         $preguntaAleatoria = $data[array_rand($data)];

         // Obtiene los datos relacionados.
         $respuestasIncorrectas = $this->getRespuestasIncorrectas($preguntaAleatoria['id']);
         $categoria = CategoriaRegistry::get($preguntaAleatoria['id_categoria']);
         $nivel = NivelRegistry::get($preguntaAleatoria['id_nivel']);

         if (!$categoria || !$nivel) {
            // Lanza una excepción si no se pueden cargar las dependencias.
            throw new \Exception("Categoría o Nivel no encontrado en Registry para la pregunta ID: " . $preguntaAleatoria['id']);
         }

         // Retorna una nueva instancia del objeto Pregunta.
         return new Pregunta(
            $preguntaAleatoria,
            $categoria,
            $nivel,
            $respuestasIncorrectas
         );

      } catch (PDOException $e) {
         // Relanza la excepción con un mensaje más descriptivo.
         throw new PDOException("Error al obtener la pregunta por categoría: " . $e->getMessage());
      }

   }



}