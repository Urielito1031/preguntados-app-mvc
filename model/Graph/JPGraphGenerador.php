<?php

namespace Graph;
// Incluir manualmente los archivos de JPGraph
require_once __DIR__ . '/../../vendor/jpgraph-4.4.2/src/jpgraph.php';
require_once __DIR__ . '/../../vendor/jpgraph-4.4.2/src/jpgraph_bar.php';
require_once __DIR__ . '/../../vendor/jpgraph-4.4.2/src/jpgraph_pie.php';
require_once __DIR__ . '/../../vendor/jpgraph-4.4.2/src/jpgraph_pie3d.php';

use Graph;
use BarPlot;
use PieGraph;
use PiePlot;


class JPGraphGenerador
{

   //crea el grafico de barras (bar chart) con los datos proporcionados,
   // retorna una imagen como cadena base64

   public function generateBarChart(array $data, string $title): string
   {

      if (!is_array($data['values']) || empty($data['values'])) {
         return '';
      }

      $values = array_values($data['values']);


      try {
         $graph = new Graph(400, 300);
         $graph->SetScale("textlin");
         $graph->title->Set($title);

         $bplot = new BarPlot($values);
         $graph->Add($bplot);

         $filename = tempnam(sys_get_temp_dir(), 'graph') . '.png';
         $graph->Stroke($filename);

         $imageData = file_get_contents($filename);
         unlink($filename); // Eliminar archivo temporal

         return base64_encode($imageData);
      } catch (\Exception $e) {
         error_log("Error en generateBarChart para '$title': " . $e->getMessage());
         return '';
      }
   }

   public function generatePieChart(array $data, string $title): string
   {
      // Validar datos
      if (!isset($data['values']) || !is_array($data['values']) || empty($data['values'])) {
         error_log("Error en generatePieChart: Datos inválidos para '$title': " . print_r($data, true));
         return ''; // Devolver cadena vacía si los datos son inválidos
      }

      // Verificar que todos los valores sean numéricos y positivos
      $values = array_values($data['values']);
      $labels = array_keys($data['values']);
      if (array_filter($values, fn($v) => !is_numeric($v) || $v <= 0)) {
         error_log("Error en generatePieChart: Valores no numéricos o no positivos para '$title': " . print_r($values, true));
         return '';
      }

      try {
         $graph = new PieGraph(400, 300);
         $graph->title->Set($title);

         $p1 = new PiePlot($values);
         $p1->SetLegends($labels);
         $graph->Add($p1);

         $filename = tempnam(sys_get_temp_dir(), 'graph') . '.png';
         $graph->Stroke($filename);

         $imageData = file_get_contents($filename);
         unlink($filename); // Eliminar archivo temporal

         return base64_encode($imageData);
      } catch (\Exception $e) {
         error_log("Error en generatePieChart para '$title': " . $e->getMessage());
         return '';
      }
   }
}