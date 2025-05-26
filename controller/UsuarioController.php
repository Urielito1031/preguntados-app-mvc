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

        $response = $this->usuarioService->login($email, $password); //

        if ($response->success) {
            $this->handleLoginSuccess($response->data);

            header('Location: /home/show');
            exit;
        } else {
            $this->view->render("login", ['error' => $response->message]); //
        }
    }

    private function handleLoginSuccess(Usuario $usuario) {

        $_SESSION['user_id'] = $usuario->getId(); //
        $_SESSION['user_email'] = $usuario->getCorreo(); //
        $_SESSION['user_name'] = $usuario->getNombreUsuario();
    }


    public function logout() {
        session_unset();
        session_destroy();
        // Redirige a la raíz del sitio (que a su vez mostrará el login).
        header('Location: /');
        exit();
    }

    public function showRegisterForm()
    {
        $viewData = [
            'titulo_h1' => "REGISTRARSE",
            'generos' => [
                ['valor' => 'Masculino', 'texto' => 'MASCULINO'],
                ['valor' => 'Femenino', 'texto' => 'FEMENINO'],
                ['valor' => 'Prefiero no cargarlo', 'texto' => 'OTRO']
            ]
        ];

        $this->view->render("register", $viewData);
    }

    public function processRegister()
    {
        $nombre = $_POST['nombre'] ?? ''; //
        $apellido = $_POST['apellido'] ?? ''; //
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? ''; //
        $nombre_usuario = $_POST['nombre_usuario'] ?? ''; //
        $email = $_POST['correo'] ?? ''; //
        $genero = $_POST['genero'] ?? ''; //

        $passwordRecibido = $_POST['contrasenia'] ?? ''; //
        $contrasenia =password_hash($passwordRecibido, PASSWORD_DEFAULT); //

        $user = new Usuario( //
            [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'fecha_nacimiento' => $fecha_nacimiento,
                'nombre_usuario' => $nombre_usuario,
                'genero' => $genero,
                'correo' =>$email,
                'contrasenia' => $contrasenia,
                'cuenta_validada' => true
                //activado por defecto, arreglar despues
            ]
        );
        $response = $this->usuarioService->save($user); //

        $this->view->render("register", ['message' => 'Fui al controlador y volvi ','correo' => $response->message]); //
    }
}