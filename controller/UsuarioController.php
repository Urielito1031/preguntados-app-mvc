<?php

use Entity\Usuario;
use Service\UsuarioService;

class UsuarioController
{
   private UsuarioService $usuarioService;
   private $view;

   public function __construct(UsuarioService $usuarioService, MustachePresenter $view)
   {
      $this->usuarioService = $usuarioService;
      $this->view = $view;
   }

   public function showLoginForm(){
      $this->view->render("login");
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

   public function showRegisterForm(){
        $this->view->render("register");
   }

   public function processRegister(){

   }

}