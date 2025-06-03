<?php
use Repository\UsuarioRepository;
use Repository\PartidaRepository;
use Entity\Partida;

require_once __DIR__ . '/../configuration/Database.php';
require_once __DIR__ . '/../model/Entity/Usuario.php';
require_once __DIR__ . '/../model/Entity/Partida.php';
require_once __DIR__ . '/../model/Repository/UsuarioRepository.php';
require_once __DIR__ . '/../model/Repository/PartidaRepository.php';

try {
   $usuarioRepo = new UsuarioRepository();
   $usuario = $usuarioRepo->findByUsername('usuarioEditor123');
   if (!$usuario) {
      throw new Exception("Usuario no encontrado");
   }

   $partida = new Partida([
      "id" => 0,
      "puntaje" => 100,
      "estado" => "GANADA",
      "preguntas_correctas" => 10
   ], $usuario);

   $partidaRepo = new PartidaRepository();
   $partidaRepo->saveGame($partida);


   try {
      echo "ID del usuario: " . $partida->getUsuario()->getId() . PHP_EOL;
      $partidaRepo->saveGame($partida);
      echo "✅ Partida registrada correctamente con ID: " . $partida->getId();
   } catch (PDOException $e) {
      echo "❌ Error al registrar partida: " . $e->getMessage();
   }
}catch (Exception $e) {
   echo "❌ Error: " . $e->getMessage();

}