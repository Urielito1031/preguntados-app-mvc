<?php

namespace Repository;

use Config\Database;
use Entity\PreguntaSugerida;
use PDO;
use PDOException;


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

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id_categoria', $pregunta->getIdCategoria());
            $stmt->bindValue(':enunciado', $pregunta->getEnunciado());
            $stmt->execute();

            // Segunda query
            foreach ($pregunta->getRespuestas() as $index => $respuesta) {
                $stmt = $this->conn->prepare($queryTwo);
                $stmt->bindValue(':respuesta', $respuesta);
                $stmt->bindValue(':id_pregunta', $this->getIdPreguntaByStatement($pregunta->getEnunciado()));
                $stmt->bindValue(':es_correcta', $index == $pregunta->getPosicionArrayDeRespuestaCorrecta(), PDO::PARAM_BOOL);
                $stmt->execute();
            }

        } catch (PDOException $e) {
            throw new PDOException("No se pudo guardar la nueva pregunta sugerida " . $e);
        }

    }

    private function getIdPreguntaByStatement (String $enunciado) {
        $query = "SELECT id FROM pregunta_sugerida WHERE enunciado = :enunciado";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['enunciado' => $enunciado]);
        $idObtenido = $stmt->fetchColumn();

        return $idObtenido;
    }
}