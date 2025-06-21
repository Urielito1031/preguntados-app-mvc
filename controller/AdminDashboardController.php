<?php

namespace Controller;



use MustachePresenter;
use Service\DashboardService;

require_once __DIR__. '/../model/Service/DashboardService.php';

class AdminDashboardController
{
   private $view;
   private DashboardService $dashboardService;

   public function __construct(MustachePresenter  $view, DashboardService $dashboardService)
   {
      $this->view = $view;
      $this->dashboardService = $dashboardService;
   }

   private function getUserSessionData() : array {
      return [
         'usuario' => $_SESSION['user_name'] ?? '',
         'foto_perfil' => $_SESSION['foto_perfil']
      ];
   }
   public function showDashboard():void
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




}