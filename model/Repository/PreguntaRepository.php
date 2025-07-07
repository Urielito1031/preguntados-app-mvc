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

    public function traerTodasLaspreguntas(){
        $query = "SELECT * FROM pregunta";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    //ON DELETE -> CASCADE
    //Al borrar una pregunta se elimina su referencia en usuario_pregunta
    public function eliminarPregunta($idPregunta){

       // Eliminar registros relacionados en usuario_pregunta
       $sqlEliminarUsuarioPregunta = "DELETE FROM usuario_pregunta WHERE id_pregunta = :idPregunta";
       $stmtUsuarioPregunta = $this->conn->prepare($sqlEliminarUsuarioPregunta);
       $stmtUsuarioPregunta->execute(['idPregunta' => $idPregunta]);

       // Eliminar respuestas asociadas a la pregunta
       $sqlEliminarRespuestas = "DELETE FROM respuesta WHERE id_pregunta = :idPregunta";
       $stmtRespuestas = $this->conn->prepare($sqlEliminarRespuestas);
       $stmtRespuestas->execute(['idPregunta' => $idPregunta]);

       // Eliminar la pregunta
       $sqlEliminarPregunta = "DELETE FROM pregunta WHERE id = :idPregunta";
       $stmtPregunta = $this->conn->prepare($sqlEliminarPregunta);
       return $stmtPregunta->execute(['idPregunta' => $idPregunta]);
    }

    public function findByIdParaEditor(int $id)
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
            $respuestasIncorrectas = $this->getRespuestasIncorrectasParaEditor($id);
            $respuestaCorrecta = $this->getRespuestaCorrectaParaEditor($id);
            $data['respuesta_correcta']= $respuestaCorrecta;
            $respuesta['data'] = $data;
            $respuesta['categoria'] = $categoria;
            $respuesta['respuestasincorrectas'] = $respuestasIncorrectas;
            //$respuesta['data'] = array($data,$categoria, $respuestasIncorrectas);

            return $respuesta;


        }catch (PDOException $e){
            throw new PDOException("No se pudo obtener la consulta:  " . $e);
        }
    }

    public function getRespuestasIncorrectasParaEditor(int $idPregunta):array
    {
        $query = "SELECT respuesta,id FROM respuesta WHERE id_pregunta = :id AND es_correcta = 0";
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $idPregunta]);
            return  $stmt->fetchAll();

        }catch (PDOException $e){
            throw new PDOException("No se pudo obtener las respuestas incorrectas: " . $e);
        }
    }
    public function getRespuestaCorrectaParaEditor(int $idPregunta):array
    {
        $query = "SELECT respuesta,id as idRespuestaCorrecta FROM respuesta WHERE id_pregunta = :id AND es_correcta = 1";
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $idPregunta]);
            return  $stmt->fetchAll();

        }catch (PDOException $e){
            throw new PDOException("No se pudo obtener respuesta correcta: " . $e);
        }
    }
    public function editarPregunta($pregunta)
    {
        $query = "UPDATE pregunta SET enunciado = :enunciado, id_categoria = :idCategoria WHERE id = :id";
        $queryRespuesta = "UPDATE respuesta SET respuesta = :respuesta WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);

            $stmt->execute(['id' => $pregunta['idPregunta'],
                            'idCategoria' => $pregunta['idCategoria'],
                            'enunciado' => $pregunta['enunciado']]
                            );
            foreach($pregunta['respuestas'] as $respuesta){
                $stmtRespuestas = $this->conn->prepare($queryRespuesta);

                $stmtRespuestas->execute(['id' => $respuesta['id'],
                        'respuesta' => $respuesta['respuesta']
                    ]);
            }

            return "Edicion realizada correctamente";

        } catch (PDOException $e) {
            throw new PDOException("No se pudo actualizar la pregunta " . $e->getMessage());
        }
    }

    public function guardarNuevaPregunta($pregunta)
    {

        $query = "INSERT INTO pregunta (id_categoria,enunciado) VALUES(:idCategoria,:enunciado)";
        $queryRespuesta = "INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES(:respuesta,:idPregunta,:es_correcta)";
        //$queryRespuesta = "UPDATE respuesta SET respuesta = :respuesta WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);

            $resultado = $stmt->execute([
                    'idCategoria' => $pregunta['idCategoria'],
                    'enunciado' => $pregunta['enunciado']]
            );

            $idPreguntaGuardada =$this->conn->lastInsertId();

            $stmtRespuestaCorrecta = $this->conn->prepare($queryRespuesta);
            $stmtRespuestaCorrecta->execute([
                'respuesta' => $pregunta['respuestaCorrecta'],
                'idPregunta' => $idPreguntaGuardada,
                'es_correcta' => 1
            ]);

            foreach($pregunta['respuestas'] as $respuesta){
                $stmtRespuestas = $this->conn->prepare($queryRespuesta);

                $stmtRespuestas->execute([
                    'respuesta' => $respuesta['respuesta'],
                    'idPregunta' => $idPreguntaGuardada,
                    'es_correcta' => 0
                ]);
            }

            return "La pregunta se ha guardado correctamente, con id= $idPreguntaGuardada";

        } catch (PDOException $e) {
            throw new PDOException("No se pudo actualizar la pregunta " . $e->getMessage());
        }
    }

   //metodo para usar en dashboard
   public function getCantidadPreguntas():int{
      $query = "SELECT COUNT(p.id)  FROM pregunta p ";
      try{
         $stmt = $this->conn->prepare($query);
         $stmt->execute();
         return $stmt->fetchColumn();
      }catch (PDOException $e){
         throw new PDOException("No se pudo obtener la cantidad de preguntas: " . $e->getMessage());
      }
   }

    public function getPreguntasSugeridas(){
        $stmt = $this->conn->prepare("SELECT * FROM pregunta_sugerida");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reportarPregunta($idPregunta){
        $query = "UPDATE pregunta SET cantidad_reportes = cantidad_reportes + 1 WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $idPregunta, PDO::PARAM_INT);
            $stmt->execute();
            return "Pregunta reportada correctamente";
        } catch (PDOException $e) {
            throw new PDOException("No se pudo reportar pregunta: " . $e->getMessage());
        }
    }
    public function traerTodasLaspreguntasReportadas(){
        $query = "SELECT * FROM pregunta where cantidad_reportes > 0 order by cantidad_reportes DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function marcarComoResuelto($idPregunta)
    {
        $query = "UPDATE pregunta SET cantidad_reportes = 0 WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $idPregunta, PDO::PARAM_INT);
            $stmt->execute();
            return "Pregunta marcada como resuelta";
        } catch (PDOException $e) {
            throw new PDOException("No se pudo resolver reporte de pregunta: " . $e->getMessage());
        }
    }
}