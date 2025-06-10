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

         if ($categoria === null) {
            throw new \Exception("Categoría no encontrado en Registry");
         }
         $respuestasIncorrectas = $this->getRespuestasIncorrectas($id);
         $respuestaCorrecta = $this->getRespuestaCorrecta($id);
          $data['respuesta_correcta']= $respuestaCorrecta['0'];
         return new Pregunta($data, $categoria, $respuestasIncorrectas);


      }catch (PDOException $e){
         throw new PDOException("No se pudo obtener la consulta:  " . $e);
      }


   }
   //opcional el uso de categoria como parametro
   public function getAll(?string $idCategoria = null): array
   {
      try {
         $params = [];
         $query = "SELECT * FROM pregunta";
         if ($idCategoria !== null) {
            $query .= " WHERE id_categoria = :id_categoria";
            $params[':id_categoria'] = $idCategoria;
         }

         $stmt = $this->conn->prepare($query);
         $stmt->execute($params);
         $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

         $preguntas = [];
         foreach ($data as $row) {
            $categoria = CategoriaRegistry::get($row['id_categoria']);
            if (!$categoria) {
               throw new PDOException("Categoría no encontrada en Registry para la pregunta ID: " . $row['id']);
            }
            $respuestasIncorrectas = $this->getRespuestasIncorrectas($row['id']);
            $preguntas[] = new Pregunta($row, $categoria, $respuestasIncorrectas);
         }

         return $preguntas;
      } catch (PDOException $e) {
         throw new PDOException("Error al obtener las preguntas: " . $e->getMessage());
      }
   }

    public function getRespuestaCorrecta(int $idPregunta):array
    {
        $query = "SELECT respuesta FROM respuesta WHERE id_pregunta = :id AND es_correcta = 1";
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $idPregunta]);
            return  $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        }catch (PDOException $e){
            throw new PDOException("No se pudo obtener respuesta correcta: " . $e);
        }
    }

   public function getRespuestasIncorrectas(int $idPregunta):array
   {
      $query = "SELECT respuesta FROM respuesta WHERE id_pregunta = :id AND es_correcta = 0";
      try{
         $stmt = $this->conn->prepare($query);
         $stmt->execute(['id' => $idPregunta]);
         return  $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

      }catch (PDOException $e){
         throw new PDOException("No se pudo obtener las respuestas incorrectas: " . $e);
      }
   }

   public function acumularPreguntaJugada(Pregunta $pregunta)
   {
      $query = "UPDATE pregunta SET cantidad_jugada = cantidad_jugada + ". $pregunta::SUMAR_PUNTAJE." WHERE id = :id";
      try {
         $stmt = $this->conn->prepare($query);
         $stmt->bindValue(':id', $pregunta->getId(), PDO::PARAM_INT);
         $stmt->execute();
         $pregunta->setCantidadJugada($pregunta->getCantidadJugada() + $pregunta::SUMAR_PUNTAJE);


      } catch (PDOException $e) {
         throw new PDOException("No se pudo actualizar la pregunta jugada: " . $e->getMessage());
      }

   }

   public function acumularCantidadAciertos(Pregunta $pregunta)
   {
      $query = "UPDATE pregunta SET cantidad_aciertos = cantidad_aciertos + ". $pregunta::SUMAR_ACIERTOS. " WHERE id = :id";
      try {
         $stmt = $this->conn->prepare($query);
         $stmt->bindValue(':id', $pregunta->getId(), PDO::PARAM_INT);
         $stmt->execute();
        $pregunta->setCantidadAciertos($pregunta->getCantidadAciertos() + $pregunta::SUMAR_ACIERTOS);
      } catch (PDOException $e) {
         throw new PDOException("No se pudo actualizar la cantidad de aciertos: " . $e->getMessage());
      }
   }

    public function calcularNivelDePregunta(?Pregunta $preguntaObtenida)
    {
        $idPregunta = $preguntaObtenida->getId();

        $query = "SELECT cantidad_aciertos / cantidad_jugada  AS ratio, cantidad_jugada 
                    FROM pregunta
                    WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $idPregunta, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) {
                return null;
            }
            if ($data['cantidad_jugada'] == 0) {
                return 1; // RETORNA NIVEL FACIL
            }

            return $data['ratio'];
        } catch (PDOException $e) {
            throw new PDOException("Error al buscar nivel de pregunta por ID: " . $e->getMessage());
        }
    }


    private function saveRespuestasIncorrectas(Pregunta $pregunta): void
   {
      $query = "INSERT INTO respuesta (respuesta, id_pregunta,es_correcta) 
                VALUES (:respuesta, :id_pregunta, 0)";
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



   public function getPreguntasPorDificultad(string $nivel): array
   {
      try {
         $params = [];
         $query = "SELECT * FROM pregunta";



         $query .= " WHERE cantidad_jugada >= :min_jugadas";

         $ratio = "(cantidad_aciertos / cantidad_jugada)";
         // Definir los rangos de ratio según el nivel
         $condicionRatio = match (strtoupper($nivel)) {
            'DIFICIL' => "AND {$ratio} >  0   AND {$ratio} < 0.3",
            'MEDIO' =>   "AND {$ratio} >= 0.3 AND {$ratio} < 0.7",
            'FACIL' =>   "AND {$ratio} >= 0.7 AND {$ratio} <= 1",
            default => throw new \InvalidArgumentException("Nivel de dificultad no válido: $nivel")
         };

         $query .= $condicionRatio;
         $params[':min_jugadas'] = 10;

         $stmt = $this->conn->prepare($query);
         $stmt->execute($params);
         $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

         $preguntas = [];
         foreach ($data as $row) {
            $categoria = CategoriaRegistry::get($row['id_categoria']);
            if (!$categoria) {
               throw new PDOException("Categoría no encontrada en Registry para la pregunta ID: " . $row['id']);
            }
            $respuestasIncorrectas = $this->getRespuestasIncorrectas($row['id']);
            $respuestaCorrecta = $this->getRespuestaCorrecta($row['id']);
            $row['respuesta_correcta']= $respuestaCorrecta['0'];
            $preguntas[] = new Pregunta($row, $categoria, $respuestasIncorrectas);
         }

         return $preguntas;
      } catch (PDOException $e) {
         throw new PDOException("Error al obtener las preguntas por dificultad: " . $e->getMessage());
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
          $respuestaCorrecta = $this->getRespuestaCorrecta($preguntaAleatoria['id']);
          $preguntaAleatoria['respuesta_correcta']= $respuestaCorrecta['0'];
         $respuestasIncorrectas = $this->getRespuestasIncorrectas($preguntaAleatoria['id']);
         $categoria = CategoriaRegistry::get($preguntaAleatoria['id_categoria']);

         if (!$categoria ) {
            // Lanza una excepción si no se pueden cargar las dependencias.
            throw new \Exception("Categoría no encontrado en Registry para la pregunta ID: " . $preguntaAleatoria['id']);
         }

         // Retorna una nueva instancia del objeto Pregunta.
         return new Pregunta(
            $preguntaAleatoria,
            $categoria,
            $respuestasIncorrectas
         );

      } catch (PDOException $e) {
         // Relanza la excepción con un mensaje más descriptivo.
         throw new PDOException("Error al obtener la pregunta por categoría: " . $e->getMessage());
      }

   }



}