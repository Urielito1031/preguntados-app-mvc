<?php

namespace Repository;

use Config\Database;

use Entity\Partida;
use Entity\Usuario;
use PDO;
use PDOException;


class PartidaRepository
{
    private PDO $conn;
    private UsuarioRepository $usuarioRepository;

    public function __construct()
    {
        $this->conn = Database::connect();
         $this->usuarioRepository = new UsuarioRepository();
    }


    //funciona bien
    public function saveGame(Partida $partida): void
    {

        $sql = "INSERT INTO partida (id_usuario, puntaje,estado, preguntas_correctas ) 
                VALUES (:id_usuario,:puntaje,:estado,:cantidad_de_preguntas_correctas)";
        try{
           $stmt = $this->conn->prepare($sql);
           $stmt->bindValue(':id_usuario', $partida->getUsuario()->getId(), PDO::PARAM_INT);
           $stmt->bindValue(':puntaje', $partida->getPuntaje());
           $stmt->bindValue(':estado', $partida->getEstado());
           $stmt->bindValue(':cantidad_de_preguntas_correctas',$partida->getCantidadPreguntasCorrectas());
           $stmt->execute();
           $partida->setId($this->conn->lastInsertId());

        }
        catch (PDOException $e){
            throw new PDOException("No se pudo guardar la partida: " . $e);
        }

    }

    public function arrayGamesByIdUser(int $id_usuario): array
    {
        $sql = "SELECT * FROM partida WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_STR);
        $stmt->execute();

       return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function numForAllGamesInApp(): int {
        $sql = "SELECT COUNT(*) FROM partida";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }


   public function getById(int $getId): ?Partida
   {
      $sql = "SELECT * FROM partida WHERE id = :id";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':id', $getId, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$data) {
         return null;
      }

      $usuario = $this->usuarioRepository->findById($data['id_usuario']);
      if (!$usuario) {
         throw new \Exception("Usuario no encontrado para la partida ID: $getId");
      }
      return new Partida($data,$usuario);
   }
}

