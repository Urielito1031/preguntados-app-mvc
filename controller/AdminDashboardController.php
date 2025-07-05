<?php

namespace Controller;



use MustachePresenter;
use Service\DashboardService;
use Service\ExportarPdfService;

require_once __DIR__. '/../model/Service/DashboardService.php';
require_once __DIR__. '/../model/Service/ExportarPdfService.php';

class AdminDashboardController
{
   private $view;
   private DashboardService $dashboardService;
   private ExportarPdfService $exportarPdfService;

   public function __construct(MustachePresenter  $view, DashboardService $dashboardService, ExportarPdfService $exportarPdfService)
   {
      $this->view = $view;
      $this->dashboardService = $dashboardService;
      $this->exportarPdfService = $exportarPdfService;
   }

   private function getUserSessionData() : array {
      return [
         'usuario' => $_SESSION['user_name'] ?? '',
         'foto_perfil' => $_SESSION['foto_perfil']
      ];
   }
   public function show():void
   {
      $results = $this->dashboardService->generateDashboardData();


      $userData = $this->getUserSessionData();
      $viewData = array_merge($userData, [
         'title' => 'Dashboard',
         'data' => $results['data'],
         'graficos' => $results['graficos']
      ]);

      $this->view->render("admin", $viewData);
   }

   public function exportarDashboardPdf(): void
   {
      $results = $this->dashboardService->generateDashboardData();
      $graficos = $results['graficos'];

      $html = '<h1 style="text-align: center;">Reporte generado</h1>';

      foreach ($graficos as $grafico) {
         $html .= '<div style="text-align: center; margin-bottom: 30px;">';
         $html .= '<img src="data:image/png;base64,' . $grafico['src'] . '" alt="' . $grafico['alt'] . '" style="width: 400px; height: auto;">';
         $html .= '<p>' . $grafico['alt'] . '</p>';
         $html .= '</div>';
      }

      $this->exportarPdfService->exportar($html, 'dashboard.pdf');
   }


}