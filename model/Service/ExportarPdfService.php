<?php

namespace Service;
//autoload.inc.php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;



class ExportarPdfService
{

   private Dompdf $dompdf;

   public function __construct()
   {
      $options = new Options();

      //Permite el uso de urls o imagen en base64 (isRemoteEnabled)
      $options->set('isRemoteEnabled', true);
      $this->dompdf = new Dompdf($options);
   }

   public function exportar(string $html, string $nombreArchivo = 'Dashboard.pdf'): void
   {
      $this->dompdf->loadHtml($html);
      $this->dompdf->setPaper('A4', 'portrait');
      $this->dompdf->render();
      // 'Attachment' = si es true el archivo se descarga,
      // si es false se muestra en el navegador
      $this->dompdf->stream($nombreArchivo, ['Attachment' => false]);
   }



}