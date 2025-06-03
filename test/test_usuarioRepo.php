<?php

use Entity\Usuario;
use Repository\UsuarioRepository;

require_once __DIR__ . '/../configuration/Database.php';
require_once __DIR__ . '/../model/entity/Usuario.php';
require_once __DIR__ . '/../model/Repository/UsuarioRepository.php';

$usuarioEditor = [
   'correo' => 'editor123@gmail.com',
   'nombre_usuario' => 'usuarioEditor123',
   'contrasenia' => 'editor123',
   'id_rol' => 2
];
$usuario = new Usuario($usuarioEditor);
$usuario->hashContrasenia($usuarioEditor['contrasenia']);


try {
   $repo = new UsuarioRepository();
   $result = $repo->save($usuario);

   echo $result
      ? "✅ Usuario guardado! ID: " . $usuario->getId()
      : "❌ Error al guardar";

   echo "\nDatos guardados:\n";
   print_r($usuario);

} catch (PDOException $e) {
   echo "🔥 Error: " . $e->getMessage();
}
