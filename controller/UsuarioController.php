<?php

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

   public function processLogin()
   {
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         $this->showLoginForm();
         return;
      }

      $email = $_POST['correo'] ?? '';
      $password = $_POST['contrasenia'] ?? '';

      if (empty($email) || empty($password)) {
         $this->view->render("login", ['error' => 'Correo y contraseña son obligatorios.']);
         return;
      }

      $response = $this->usuarioService->login($email, $password);
      if ($response->success) {
         $this->view->render("message", ['message' => 'Login exitoso. Bienvenido al lobby!']);
      } else {
         $this->view->render("login", ['error' => $response->message]);
      }
   }
}