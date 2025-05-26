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
        $viewData = ['logo_url' => '/public/img/LogoQuizCode.png'];
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


      if ($response->success) {

         $this->handleLoginSuccess($response->data);
         $nombre =  $response->data->getNombreUsuario();
         $this->view->render("home", ['usuario' => $nombre]);
      } else {
         $this->view->render("login", ['error' => $response->message]);
      }
   }

   private function handleLoginSuccess(Usuario $usuario) {
      $_SESSION['user_id'] = $usuario->getId();
      $_SESSION['user_email'] = $usuario->getCorreo();
   }
   public function showRegisterForm()
   {
      $this->view->render("register");
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
             'id_ciudad' => $id_ciudad

         ]
      );

      $response = $this->usuarioService->save($user);



      $this->view->render("home", ['message' => 'Fui al controlador y volvi ','correo' => $response->message]);
   }
}