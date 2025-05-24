<?php

use Entity\Usuario;

require_once __DIR__ . '/../configuration/Database.php';
require_once __DIR__ . '/../model/entity/Usuario.php';
require_once __DIR__ . '/../model/Repository/UsuarioRepository.php';

// Usuario Admin (Rol 1)
$usuarioEditor = [
   'correo' => 'admin@example.com',
   'nombre_usuario' => 'admin_user',
   'contrasenia' => 'admin1234',
   'id_rol' => 3
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