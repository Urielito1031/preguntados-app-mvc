<?php

use Entity\Usuario;
use Service\ImageService;
use Service\UsuarioService;

class UsuarioController
{
   private UsuarioService $usuarioService;
   private $view;
   private ImageService $imageService;
   private UbicacionService $ubicacionService;


    public function __construct(UsuarioService $usuarioService, MustachePresenter $view)
   {
      $this->usuarioService = $usuarioService;
      $this->view = $view;
      $this->imageService = new ImageService();
      $this->ubicacionService = new UbicacionService(new \Repository\PaisRepository(), new \Repository\CiudadRepository());
   }

    public function showLoginForm(){
       $viewData = ['logo_url' => '/public/img/LogoQuizCode.png', 'foto_perfil' => 'public/img/person-fill.svg'];

        if(isset($_SESSION['user_name'])) {
            $viewData['display'] = "display: block";
        }else{
            $viewData['display'] = "display: none";
        }

       $this->view->render("login", $viewData);
    }

   public function processLogin() {
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         $this->showLoginForm();
         return;
      }

      $email = $_POST['correo'] ?? '';
      $password = $_POST['contrasenia'] ?? '';

      // Delegar TODA la validación al servicio
      $response = $this->usuarioService->login($email, $password);

       $viewData = ['error' => $response->message, 'logo_url' => '/public/img/LogoQuizCode.png'];

      if(isset($_SESSION['user_name'])) {
           $viewData['display'] = "display: block";
       }else{
           $viewData['display'] = "display: none";
       }

       if ($response->success) {
           $this->handleLoginSuccess($response->data);
           header('Location: /home/show');
           exit;
       } else {
           $this->view->render("login", $viewData); //
       }
   }

   private function handleLoginSuccess(Usuario $usuario){
       $_SESSION['user_id'] = $usuario->getId();
       $_SESSION['user_email'] = $usuario->getCorreo();
       $_SESSION['user_name'] = $usuario->getNombreUsuario();
       $_SESSION['foto_perfil'] = $usuario->getUrlFotoPerfil();
       $_SESSION['puntaje_total'] = $usuario->getPuntajeTotal();
   }
    public function logout() {
        session_unset();
        session_destroy();
        // Redirige a la raíz del sitio (que a su vez mostrará el login).
        header('Location: /');
        exit();
    }

   public function showRegisterForm(){
       $options = [
            ['value' => '', 'show' => 'Sexo'],
            ['value' => 'masculino', 'show' => 'Masculino'],
            ['value' => 'femenino', 'show' => 'Femenino'],
            ['value' => 'otro', 'show' => 'Otro']
       ];

      $viewData = ['sexo' => $options, 'titulo_h1' => 'REGISTRARSE'];

      if(isset($_SESSION['user_name'])) {
          $viewData['display'] = "display: block";
      }else{
          $viewData['display'] = "display: none";
      }

      $this->view->render("register", $viewData);
   }


    /**
     * @throws Exception
     */
    public function processRegister()
   {
      $nombre = $_POST['nombre'] ?? '';
      $apellido = $_POST['apellido'] ?? '';
      $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
      $nombre_usuario = $_POST['nombre_usuario'] ?? '';
      $email = $_POST['correo'] ?? '';
      $genero = $_POST['genero'] ?? '';
      $cuentaValidada = $_POST ['estado'] ?? '';

      $id_ciudad = $this->ubicacionService->processUbication($_POST['pais']??'',$_POST['ciudad'])->getId();

      if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
         $url_foto_perfil = $this->imageService->uploadImage($_FILES['imagen']);
      } else {
         $url_foto_perfil = null;
      }

   $passwordRecibido = $_POST['contrasenia'] ?? '';
      $contrasenia =password_hash($passwordRecibido, PASSWORD_DEFAULT); //corresponde que este aca?

      $user = new Usuario(
         [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'fecha_nacimiento' => $fecha_nacimiento,
            'nombre_usuario' => $nombre_usuario,
            'genero' => $genero,
            'correo' =>$email,
            'contrasenia' => $contrasenia,
             'url_foto_perfil' => $url_foto_perfil,
             'id_ciudad' => $id_ciudad,
             'cuenta_validada' => $cuentaValidada

         ]
      );

      $response = $this->usuarioService->save($user);

      $viewData = [
          'message' => 'Fui al controlador y volvi ',
          'correo' => $response->message,
          'logo_url' => '/public/img/LogoQuizCode.png'
      ];

      if(isset($_SESSION['user_name'])) {
          $viewData['display'] = "display: block";
      }else{
          $viewData['display'] = "display: none";
      }

      $this->view->render("login", $viewData);
   }


   public function showProfile(){
       $pais = 'Argentina';
       $ciudad = 'Villa Celina';

       $ubicacion = urlencode($ciudad . ', ' . $pais);

       $url = "https://maps.google.com/maps?q={$ubicacion}&output=embed";

       $viewData = ['usuario' => $_SESSION['user_name'] ?? '',
                'foto_perfil' => $_SESSION['foto_perfil'] ?? '',
                'puntaje_total' => $_SESSION['puntaje_total'] ?? '',
                'mapa_url' => $url
       ];

        $this->view->render("profile", $viewData);
   }
}