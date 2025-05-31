<?php

namespace Repository;

use Config\Database;

use PDO;
use PDOException;


class PartidaRepository
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::connect();

    }

    /**
     * @throws \Throwable
     */
    public function saveGame(int $id_usuario,int $puntaje, int $cantidad_de_preguntas_correctas){

        $this->conn->beginTransaction();
        try{
        $sql = "INSERT INTO partida (id_usuario, puntaje, cantidad_de_preguntas_correctas ) VALUES (:id_usuario,:puntaje,:cantidad_de_preguntas_correctas)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':puntaje', $puntaje);
        $stmt->bindParam(':cantidad_de_preguntas_correctas', $cantidad_de_preguntas_correctas);
        $stmt->execute();

        $this->conn->commit();
        }
        catch (PDOException $e){
            $this->conn->rollBack();
            throw new PDOException("No se pudo guardar la partida: " . $e);
        }

        catch (\Throwable $e){
            $this->conn->rollBack();
            throw $e;
        }

    }

    public function arrayGamesByIdUser(int $id_usuario): array
    {
        $sql = "SELECT * FROM pais WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function numForAllGamesInApp(): int {
        $sql = "SELECT COUNT(*) FROM partida";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}

