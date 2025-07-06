<?php


use Service\QrService;
use Service\UsuarioService;

class ProfileController
{
   private UsuarioService $usuarioService;
   private QrService $qrService;
   private UbicacionService $ubicacionService;
   private MustachePresenter $view;

   public function __construct(
      UsuarioService $usuarioService,
      QrService $qrService,
      UbicacionService $ubicacionService,
      MustachePresenter $view
   ) {
      $this->usuarioService = $usuarioService;
      $this->qrService = $qrService;
      $this->ubicacionService = $ubicacionService;
      $this->view = $view;
   }

   public function show()
   {
      if (!isset($_SESSION['user_id'])) {
         header("Location: /usuario/showLoginForm");
         exit;
      }

      $idCiudad = $this->usuarioService->obtenerIdCiudadDeUsuario($_SESSION['user_id']);
      $ubicacionObtenida = $this->ubicacionService->obtenerPaisYCiudadDelUsuario($idCiudad);
      $ubicacionUrl = urlencode($ubicacionObtenida->getCiudad()->getNombre() . ', ' . $ubicacionObtenida->getPais()->getNombre());

      $url = "https://maps.google.com/maps?q={$ubicacionUrl}&output=embed";

      $viewData = [
         'usuario' => $_SESSION['user_name'] ?? '',
         'foto_perfil' => $_SESSION['foto_perfil'] ?? '',
         'puntaje_total' => $_SESSION['puntaje_total'] ?? '',
         'mapa_url' => $url,
         'url_qr' => $this->qrService->generarQrCode($_SESSION['user_name'])
      ];

      $this->view->render("profile", $viewData);
   }
}