<?php

namespace Registry;

use PDO;
use Repository\PartidaRepository;
use Repository\PreguntaRepository;
use Repository\UsuarioRepository;

class DashboardRegistry
{
   private static $instance;
   private PDO $pdo;
   private $repositories = [];
   private $filter = 'all';

   private function __construct(PDO $pdo)
   {
      $this->pdo = $pdo;
   }

   public static function getInstance(PDO $pdo): DashboardRegistry
   {
      if (self::$instance === null) {
         self::$instance = new self($pdo);
      }
      return self::$instance;
   }

   public function getRepository(string $name): ?object
   {
      if (!isset($this->repositories[$name])) {
         switch ($name) {
            case 'usuario':
               $this->repositories[$name] = new UsuarioRepository();
               break;
            case 'partida':
               $this->repositories[$name] = new PartidaRepository();
               break;
            case 'pregunta':
               $this->repositories[$name] = new PreguntaRepository();
               break;
            default:
               return null;
         }
      }
      return $this->repositories[$name];
   }

   // logica para aplicar en y poder filtrar por dia, semana, mes o año, por default esta en 'all'
   public function setFilter(string $period = 'all'): void
   {
      $validPeriods = ['day', 'week', 'month', 'year', 'all'];
      $this->filter = in_array($period, $validPeriods) ? $period : 'all';
   }

   public function getFilter(): string
   {
      return $this->filter;
   }


}