<?php

namespace Repository;

use Config\Database;
use Entity\PreguntaSugerida;
use PDO;
use PDOException;
use Registry\CategoriaRegistry;


class SugerenciaPreguntaRepository
{
    private PDO $conn;


    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function save(PreguntaSugerida $pregunta)
    {
        $query = "INSERT INTO pregunta_sugerida (id_categoria, enunciado) 
                VALUES (:id_categoria, :enunciado)";

        $queryTwo = "INSERT INTO respuesta_sugerida (respuesta, id_pregunta, es_correcta) 
                VALUES (:respuesta, :id_pregunta, :es_correcta)";


       try {
          $stmtPregunta = $this->conn->prepare($query);
          $stmtPregunta->bindValue(':id_categoria', $pregunta->getIdCategoria());
          $stmtPregunta->bindValue(':enunciado', $pregunta->getEnunciado());
          $stmtPregunta->execute();

          // Setear el id del objeto PreguntaSugerida
          // con el ID generado por la base de datos
          $idPregunta = (int) $this->conn->lastInsertId();
          $pregunta->setId($idPregunta);

          $stmtRespuesta = $this->conn->prepare($queryTwo);
          $correctaIndex = $pregunta->getPosicionArrayDeRespuestaCorrecta();

          foreach ($pregunta->getRespuestas() as $index => $respuesta) {
             $stmtRespuesta->bindValue(':respuesta', $respuesta);
             $stmtRespuesta->bindValue(':id_pregunta', $idPregunta);
             $stmtRespuesta->bindValue(':es_correcta', $index === $correctaIndex, PDO::PARAM_BOOL);
             $stmtRespuesta->execute();
          }

       } catch (PDOException $e) {
          throw new PDOException("No se pudo guardar la nueva pregunta sugerida: " . $e->getMessage(), (int)$e->getCode());
       }

    }

    private function getIdPreguntaByStatement (String $enunciado) {
        $query = "SELECT id FROM pregunta_sugerida WHERE enunciado = :enunciado";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['enunciado' => $enunciado]);
        $idObtenido = $stmt->fetchColumn();

        return $idObtenido;
    }

    public function getPreguntasSugeridas(){
        $stmt = $this->conn->prepare("SELECT * FROM pregunta_sugerida");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRespuestasSugeridas(int $idPregunta){
        $stmt = $this->conn->prepare("
        SELECT respuesta, id_pregunta
        FROM respuesta_sugerida
        WHERE id_pregunta = :idPregunta
         ");
        $stmt->bindParam(':idPregunta', $idPregunta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function findByIdParaEditor(int $id)
    {
        $query = "SELECT * FROM pregunta_sugerida WHERE id = :id";
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
        $query = "SELECT respuesta,id FROM respuesta_sugerida WHERE id_pregunta = :id AND es_correcta = 0";
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
        $query = "SELECT respuesta,id as idRespuestaCorrecta FROM respuesta_sugerida WHERE id_pregunta = :id AND es_correcta = 1";
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $idPregunta]);
            return  $stmt->fetchAll();

        }catch (PDOException $e){
            throw new PDOException("No se pudo obtener respuesta correcta: " . $e);
        }
    }

    public function eliminarPregunta($idPregunta){
        $query = "DELETE FROM pregunta_sugerida WHERE id = :idPregunta";
        $stmt = $this->conn->prepare($query);
        $resultado = $stmt->execute(['idPregunta' => $idPregunta]);
        return $resultado;
    }
}