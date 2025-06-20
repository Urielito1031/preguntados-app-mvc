<?php

namespace Graph;

use Graph;
use BarPlot;
use PieGraph;
use PiePlot;


//ni idea chichos pregunten a chatGPT jajaj
class JPGraphGenerador
{

   //crea el grafico de barras (bar chart) con los datos proporcionados,
   // retorna una imagen como cadena base64
   //
   public function generateBarChart(array $data, string $title): string
   {
      $graph = new Graph(400, 300);
      $graph->SetScale("textlin");
      $graph->title->Set($title);



      $values = array_values($data['values']);
      $labels = array_keys($data['values']);

      $bplot = new BarPlot($values);
      $graph->Add($bplot);

      $filename = tempnam(sys_get_temp_dir(), 'graph') . '.png';
      $graph->Stroke($filename);

      return base64_encode(file_get_contents($filename));
   }

   public function generatePieChart(array $data, string $title): string
   {
      $graph = new PieGraph(400, 300);
      $graph->title->Set($title);

      $values = array_values($data['values']);
      $labels = array_keys($data['values']);

      $p1 = new PiePlot($values);
      $p1->SetLegends($labels);
      $graph->Add($p1);

      $filename = tempnam(sys_get_temp_dir(), 'graph') . '.png';
      $graph->Stroke($filename);

      return base64_encode(file_get_contents($filename));
   }
}