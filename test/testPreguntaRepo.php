<?php
use Repository\UsuarioRepository;
use Repository\PartidaRepository;
use Entity\Partida;

// Requiere tus clases
require_once __DIR__ . '/../configuration/Database.php';
require_once __DIR__ . '/../model/Entity/Usuario.php';
require_once __DIR__ . '/../model/Entity/Partida.php';
require_once __DIR__ . '/../model/Repository/UsuarioRepository.php';
require_once __DIR__ . '/../model/Repository/PartidaRepository.php';

try {
   $usuarioRepo = new UsuarioRepository();
   $usuario = $usuarioRepo->findByUsername('usuarioEditor123'); // o por correo
   if (!$usuario) {
      throw new Exception("Usuario no encontrado");
   }

   // Ahora que tenés el usuario, podés usar su ID para la partida
   $partida = new Partida([
      "id" => 0, // este se sobreescribe
      "puntaje" => 100,
      "estado" => "ACTIVA",
      "id_usuario"=> $usuario->getId(),
      "preguntas_correctas" => 3
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