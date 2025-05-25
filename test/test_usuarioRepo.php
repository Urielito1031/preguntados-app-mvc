<?php

use Entity\Usuario;
use Repository\UsuarioRepository;

require_once __DIR__ . '/../configuration/Database.php';
require_once __DIR__ . '/../model/entity/Usuario.php';
require_once __DIR__ . '/../model/Repository/UsuarioRepository.php';

// Usuario Editor (Rol 2)
$usuarioEditor = [
   'correo' => 'editor123@gmail.com',
   'nombre_usuario' => 'usuarioEditor123',
   'contrasenia' => 'editor123',
   'id_rol' => 2
];
// Crear usuario
$usuario = new Usuario($usuarioEditor);
$usuario->hashContrasenia($usuarioEditor['contrasenia']);

// Guardar
try {
   $repo = new UsuarioRepository();
   $result = $repo->save($usuario);

   echo $result
      ? "✅ Usuario guardado! ID: " . $usuario->getId()
      : "❌ Error al guardar";

   // Opcional: Mostrar datos guardados
   echo "\nDatos guardados:\n";
   print_r($usuario);

} catch (PDOException $e) {
   echo "🔥 Error: " . $e->getMessage();
}

// Limpiar (opcional)
// $repo->delete($usuario->getId());