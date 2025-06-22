<?php

namespace Repository;

use Config\Database;

use Entity\Partida;
use Entity\Usuario;
use PDO;
use PDOException;
use Response\DataResponse;


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

        $sql = "INSERT INTO partida (id_usuario) 
                VALUES (:id_usuario)";
        try{
           $stmt = $this->conn->prepare($sql);
           $stmt->bindValue(':id_usuario', $partida->getUsuario()->getId(), PDO::PARAM_INT);
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

   public function sumarPuntaje(int $getPuntaje,Partida $partida)
   {
      $sql = "UPDATE partida SET puntaje = puntaje + :puntaje WHERE id = :id";
      try{

      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':puntaje', $getPuntaje, PDO::PARAM_INT);
      $stmt->bindValue(':id', $partida->getId(), PDO::PARAM_INT);
      $stmt->execute();
      }catch (PDOException $e){
         throw new PDOException("No se pudo sumar el puntaje: " . $e->getMessage());
      }
   }

   public function updatePartida(Partida $partida)
   {
      $sql = "UPDATE partida SET estado = :estado, puntaje = :puntaje WHERE id = :id";
      try{
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':estado', $partida->getEstado(), PDO::PARAM_STR);
         $stmt->bindValue(':puntaje', $partida->getPuntaje(), PDO::PARAM_INT);
         $stmt->bindValue(':id', $partida->getId(), PDO::PARAM_INT);
         $stmt->execute();
      }catch (PDOException $e){
         throw new PDOException("No se pudo actualizar la partida: " . $e->getMessage());
      }
   }
}

