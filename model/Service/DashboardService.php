<?php


namespace Service;

use Graph\JPGraphGenerador;
use Repository\PartidaRepository;
use Repository\PreguntaRepository;
use Repository\UsuarioRepository;


require_once __DIR__ . '/../Repository/PartidaRepository.php';
require_once __DIR__ . '/../Repository/PreguntaRepository.php';
require_once __DIR__ . '/../Repository/UsuarioRepository.php';
require_once __DIR__ . '/../Graph/JPGraphGenerador.php';


//se encarga de agupar la logica de los repositorios y la generacion de graficos
//con la utilizacion de la clase JPGraphGenerador
class DashboardService
{
   private $partidaRepository;
   private $preguntaRepository;
   private $usuarioRepository;
   //public para hardcodear datos
   public  $graphGenerator;

   public function __construct(
      PartidaRepository $partidaRepository,
      PreguntaRepository $preguntaRepository,
      UsuarioRepository $usuarioRepository,
      JPGraphGenerador $graphGenerator
   ) {
      $this->partidaRepository = $partidaRepository;
      $this->preguntaRepository = $preguntaRepository;
      $this->usuarioRepository = $usuarioRepository;
      $this->graphGenerator = $graphGenerator;
   }

   public function generateDashboardData(): array
   {
      $data = [
         //sin graficos para jugadores, partidas y preguntas.
         'jugadores' => $this->usuarioRepository->getAllPlayers(),
         'partidas' => $this->partidaRepository->getCantidadPartidasJugadas(),
         'preguntas' => $this->preguntaRepository->getCantidadPreguntas(),

         'usuarios_pais' => $this->usuarioRepository->getPlayersByCountry(),
         'usuarios_sexo' => $this->usuarioRepository->getPlayersByGender(),
         'usuarios_edad' => $this->usuarioRepository->getPlayersByGroupAge(),
         'porcentaje_aciertos' => $this->usuarioRepository->getPorcentajeAciertosByPlayer()
      ];


      $usuarios_pais =  array_column($data['usuarios_pais'], 'total', 'nombre');
      $usuarios_sexo =  array_column($data['usuarios_sexo'], 'total', 'sexo');
      $usuarios_edad =  array_column($data['usuarios_edad'], 'total', 'grupo_edad');
      $porcentaje_aciertos =  array_column($data['porcentaje_aciertos'], 'porcentaje_aciertos', 'nombre_usuario');

      $graficos = [

         ['src' => $this->graphGenerator->generatePieChart(
            ['values' => $usuarios_pais],
            'Usuarios por País'
         ), 'alt' => 'Gráfico por país'],

         ['src' => $this->graphGenerator->generatePieChart(
            ['values' => $usuarios_sexo],
            'Usuarios por Género'
         ), 'alt' => 'Gráfico por género'],

         ['src' => $this->graphGenerator->generatePieChart(
            ['values' => $usuarios_edad],
            'Usuarios por Grupo de Edad'
         ), 'alt' => 'Gráfico por edad'],
         ['src' => $this->graphGenerator->generateBarChart(
            ['values' => $porcentaje_aciertos],
            'Cantidad de Aciertos por Jugador'
         ), 'alt' => 'Gráfico de porcentaje de aciertos']
      ];



      return ['data' => $data, 'graficos' => $graficos];
   }
}