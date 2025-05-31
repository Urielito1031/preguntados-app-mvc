<?php


use Repository\PreguntaRepository;
use Entity\Nivel;
include_once "../model/Repository/PreguntaRepository.php";

require_once __DIR__ . '/../configuration/Database.php';

$repo = new PreguntaRepository();

// Guardar
try {
   $pregunta = $repo->find(3443);
   var_dump($pregunta);
   exit;

}catch(PDOException $e){
   echo "🔥 Error: " . $e->getMessage();
   exit;
} catch (Exception $e) {
}
