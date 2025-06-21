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
         'jugadores' => $this->usuarioRepository->getAllPlayers(),
         'partidas' => $this->partidaRepository->getCantidadPartidasJugadas(),
         'preguntas' => $this->preguntaRepository->getCantidadPreguntas(),
         'usuarios_pais' => $this->usuarioRepository->getPlayersByCountry(),
         'usuarios_sexo' => $this->usuarioRepository->getPlayersByGender(),
         'usuarios_edad' => $this->usuarioRepository->getPlayersByGroupAge(),
         'porcentaje_aciertos' => $this->usuarioRepository->getPorcentajeAciertosByPlayer()
      ];

      // Validar y preparar datos para gráficos
      $jugadores = is_numeric($data['jugadores']) && $data['jugadores'] >= 0 ? (int)$data['jugadores'] : 0;
      $partidas = is_numeric($data['partidas']) && $data['partidas'] >= 0 ? (int)$data['partidas'] : 0;
      $preguntas = is_numeric($data['preguntas']) && $data['preguntas'] >= 0 ? (int)$data['preguntas'] : 0;
      $usuarios_pais = !empty($data['usuarios_pais']) ? array_column($data['usuarios_pais'], 'total', 'nombre') : ['Sin datos' => 1];
      $usuarios_sexo = !empty($data['usuarios_sexo']) ? array_column($data['usuarios_sexo'], 'total', 'sexo') : ['Sin datos' => 1];
      $usuarios_edad = !empty($data['usuarios_edad']) ? array_column($data['usuarios_edad'], 'total', 'grupo_edad') : ['Sin datos' => 1];


      $graficos = [
         ['src' => $this->graphGenerator->generateBarChart(
            ['values' => ['Jugadores' => $jugadores]],
            'Jugadores (Total)'
         ), 'alt' => 'Gráfico de jugadores'],

         ['src' => $this->graphGenerator->generateBarChart(
            ['values' => ['Partidas' => $partidas]],
            'Partidas Jugadas'
         ), 'alt' => 'Gráfico de partidas'],

         ['src' => $this->graphGenerator->generateBarChart(
            ['values' => ['Preguntas' => $preguntas]],
            'Preguntas Totales'
         ), 'alt' => 'Gráfico de preguntas'],

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
         ), 'alt' => 'Gráfico por edad']
      ];



      return ['data' => $data, 'graficos' => $graficos];
   }
}