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
         'url_qr' => $this->qrService->generarQrCode($_SESSION['user_name'], $_SESSION['user_id']),
      ];

      $this->view->render("profile", $viewData);
   }

   public function showProfileById($userId = null, $isMyProfile = false)
   {


      $id_cuenta= (isset($_GET['id'])) ? $_GET['id'] :  (int)$_SESSION['id_usuario'];

      $userResponse = $this->usuarioService->findById($id_cuenta);
      if (!$userResponse->success) {
         $viewData = [
            'error' => $userResponse->message,
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'] ?? ''
         ];
         $this->view->render("error", $viewData);
         return;
      }

      $userProfile = $userResponse->data;

      $historialDePartidas = $this->usuarioService->getHistorialDePartidas($userId);

      foreach ($historialDePartidas as $posicionPartidas => &$orden) {
         $orden['numero'] = $posicionPartidas + 1;
      }

      $ciudadNombre = '';
      $paisNombre = '';

      if ($userProfile->getIdCiudad()) {
         $ciudadEntity = $this->ubicacionService->getCiudadRepository()->findById($userProfile->getIdCiudad());
         if ($ciudadEntity) {
            $ciudadNombre = $ciudadEntity->getNombre();
            $paisEntity = $this->ubicacionService->getPaisRepository()->findById($ciudadEntity->getIdPais());
            if ($paisEntity) {
               $paisNombre = $paisEntity->getNombre();
            }
         }
      }

      $ubicacion = urlencode($ciudadNombre . ', ' . $paisNombre);
      $mapUrl = "https://maps.google.com/maps?q={$ubicacion}&output=embed";

      $viewData = [
         'usuario' => $_SESSION['user_name'] ?? '',
         'foto_perfil' => $_SESSION['foto_perfil'] ?? '',
         'nombre_perfil' => $userProfile->getNombreUsuario(),
         'nombre' => $userProfile->getNombre(),
         'apellido_perfil' => $userProfile->getApellido(),
         'correo_perfil' => $userProfile->getCorreo(),
         'fecha_nacimiento_perfil' => $userProfile->getFechaNacimiento() ? $userProfile->getFechaNacimiento()->format('Y-m-d') : 'N/A',
         'sexo_perfil' => $userProfile->getSexo() ?? 'N/A',
         'ciudad_perfil' => $ciudadNombre,
         'pais_perfil' => $paisNombre,
         'foto_perfil_visitado' => $userProfile->getUrlFotoPerfil(),
         'puntaje_total_visitado' => $userProfile->getPuntajeTotal(),
         'mapa_url' => $mapUrl,
         'historial_partidas_visitado' => $historialDePartidas,
         'es_mi_perfil' => $isMyProfile,
         'url_qr' => $this->qrService->generarQrCode($userProfile->getNombreUsuario() , $userProfile->getId())
      ];

      $this->view->render("profile", $viewData);
   }

}