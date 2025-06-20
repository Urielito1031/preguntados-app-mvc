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
   private $graphGenerator;

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

      $graficos = [
         'grafico1' => $this->graphGenerator->generateBarChart(
            ['values' => ['Jugadores' => $data['jugadores']]],
            'Jugadores (Total)'
         ),
         'grafico2' => $this->graphGenerator->generateBarChart(
            ['values' =>  ['Partidas' =>  $data['partidas']]],
            'Partidas Jugadas'
         ),
         'grafico3' => $this->graphGenerator->generateBarChart(
            ['values' =>  ['Preguntas' => $data['preguntas'] ] ],
            'Preguntas Totales'
         ),
         'grafico4' => $this->graphGenerator->generatePieChart(
            ['values' => array_column($data['usuarios_pais'],
                           'total',
                           'nombre')
            ],
            'Usuarios por País'
         ),
         'grafico5' => $this->graphGenerator->generatePieChart(
            ['values' => array_column( $data['usuarios_sexo'],
                                    'total',
                                    'sexo' )
            ],
            'Usuarios por Género'
         ),
         'grafico6' => $this->graphGenerator->generatePieChart(
            ['values' => array_column( $data['usuarios_edad'],
                           'total',
                           'grupo_edad')
            ],
            'Usuarios por Grupo de Edad'
         )
      ];

      return ['data' => $data,
              'graficos' => $graficos];
   }
}