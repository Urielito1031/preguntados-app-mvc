<?php

use Entity\Usuario;
use Entity\Ubicacion;
use Service\ImageService;
use Service\QrService;
use Service\UsuarioService;
use Repository\PaisRepository;
use Repository\CiudadRepository;



class UsuarioController
{
    private UsuarioService $usuarioService;
    private $view;
    private ImageService $imageService;
    private UbicacionService $ubicacionService;

    private QrService $qrService;


    public function __construct(UsuarioService $usuarioService, QrService $qrService, MustachePresenter $view)
    {
        $this->usuarioService = $usuarioService;
        $this->view = $view;
        $this->imageService = new ImageService();
        $this->ubicacionService = new UbicacionService(new \Repository\PaisRepository(), new \Repository\CiudadRepository());
        $this->qrService = $qrService;
    }

    public function showLoginForm()
    {
        $viewData = ['logo_url' => '/public/img/LogoQuizCode.png', 'foto_perfil' => 'public/img/person-fill.svg'];

        if (isset($_SESSION['user_name'])) {
            $viewData['display'] = "display: block";
        } else {
            $viewData['display'] = "display: none";
        }

        $this->view->render("login", $viewData);
    }

   public function processLogin(): void
   {
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         $this->showLoginForm();
         return;
      }

      $email = $_POST['correo'] ?? '';
      $password = $_POST['contrasenia'] ?? '';

      $viewData = [
         'logo_url' => '/public/img/LogoQuizCode.png',
         'foto_perfil' => 'public/img/person-fill.svg',
         'display' => isset($_SESSION['user_name']) ? "display: block" : "display: none"
      ];

      // Validar existencia del usuario
      $idResponse = $this->usuarioService->findIdUserByEmail($email);
      if (!$idResponse->success) {
         $viewData['error'] = $idResponse->message;
         $this->view->render("login", $viewData);
         return;
      }

      $id_usuario = $idResponse->data;

      // verifica si la cuenta esta validada
      if (!$this->usuarioService->validateAccountRequestByUserId($id_usuario)) {
         $this->view->render("validarCuenta", $viewData);
         return;
      }

      // aca nos logueamos, luego de las anteriores validaciones
      $loginResponse = $this->usuarioService->login($email, $password);
      if (!$loginResponse->success) {
         $viewData['error'] = $loginResponse->message;
         $this->view->render("login", $viewData);
         return;
      }


      $this->handleLoginSuccess($loginResponse->data);

      switch ($_SESSION['id_rol']) {
         case 1:
            header('Location: /admin/show');
            break;
         case 2:
            header('Location: /editor/show');
            break;
         default:
            header('Location: /home/show');
      }
      exit;
   }

   public function processValidation(): void
   {
      $email = $_POST['email'] ?? '';
      $tokenIngresado = $_POST['token'] ?? '';

      if (empty($email) || empty($tokenIngresado)) {
         $this->mostrarAlertaYRedirigir("Token o email inválidos. Intentá nuevamente desde el login.");
         return;
      }

      $idResponse = $this->usuarioService->findIdUserByEmail($email);
      if (!$idResponse->success) {
         $this->mostrarAlertaYRedirigir($idResponse->message);
         return;
      }

      $id_usuario = $idResponse->data;
      $tokenGenerado = $this->usuarioService->findTokenByUserId($id_usuario);

      if ($tokenIngresado === (string)$tokenGenerado) {
         $this->usuarioService->validateAccountByUserId($id_usuario);
         $this->mostrarAlertaYRedirigir("Validación exitosa, podés loguearte.");
      } else {
         $this->mostrarAlertaYRedirigir("El token ingresado no es válido.");
      }
   }

   private function mostrarAlertaYRedirigir(string $mensaje): void
   {
      echo "<script>
            alert('$mensaje');
            setTimeout(function() {
                window.location.href = '/';
            }, 3000);
          </script>";
      exit;
   }

    private function handleLoginSuccess(Usuario $usuario)
    {
        $_SESSION['user_id'] = $usuario->getId();
        $_SESSION['user_email'] = $usuario->getCorreo();
        $_SESSION['user_name'] = $usuario->getNombreUsuario();
        $_SESSION['foto_perfil'] = $usuario->getUrlFotoPerfil();
        $_SESSION['puntaje_total'] = $usuario->getPuntajeTotal();
        $_SESSION['id_rol'] = $usuario->getIdRol();
        $_SESSION['id_usuario'] = $usuario->getId();
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        // Redirige a la raíz del sitio (que a su vez mostrará el login).
        header('Location: /');
        exit();
    }

    public function showRegisterForm()
    {
        $options = [
            ['value' => '', 'show' => 'Sexo'],
            ['value' => 'masculino', 'show' => 'Masculino'],
            ['value' => 'femenino', 'show' => 'Femenino'],
            ['value' => 'otro', 'show' => 'Otro']
        ];

        $viewData = ['sexo' => $options, 'titulo_h1' => 'REGISTRARSE'];

        if (isset($_SESSION['user_name'])) {
            $viewData['display'] = "display: block";
        } else {
            $viewData['display'] = "display: none";
        }

        $this->view->render("register", $viewData);
    }


    /**
     * @throws Exception
     */
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
        $contrasenia = $_POST['contrasenia'] ?? '';
        $repetirContrasenia = $_POST['repetir_contrasenia'] ?? '';
        $pais = $_POST['pais'] ?? '';
        $ciudad = $_POST['ciudad'] ?? '';
        //$estado = $_POST['estado'] ?? ''; COMENTO EL INPUT DE ESTADO PORQUE LO ANULE EN EL FORM

        $id_ciudad = $this->ubicacionService->processUbication($pais, $ciudad)->getId();

        $url_foto_perfil = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $url_foto_perfil = $this->imageService->uploadImage($_FILES['imagen']);
        }

        $contraseniaHash = password_hash($contrasenia, PASSWORD_DEFAULT);


        $user = new Usuario(
            [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'fecha_nacimiento' => $fecha_nacimiento,
                'nombre_usuario' => $nombre_usuario,
                'sexo' => $genero,
                'correo' => $email,
                'contrasenia' => $contraseniaHash,
                'url_foto_perfil' => $url_foto_perfil,
                'id_ciudad' => $id_ciudad,
                //'cuenta_validada' => $estado COMENTO EL INPUT DE ESTADO PORQUE LO ANULE EN EL FORM
            ]
        );

        $options = [
            ['value' => '', 'show' => 'Sexo'],
            ['value' => 'Masculino', 'show' => 'Masculino'],
            ['value' => 'Femenino', 'show' => 'Femenino'],
            ['value' => 'Prefiero no cargarlo', 'show' => 'Otro']
        ];

        $viewData = [
            'sexo' => $options,
            'titulo_h1' => 'REGISTRARSE',
            'nombre' => $nombre,
            'apellido' => $apellido,
            'fecha_nacimiento' => $fecha_nacimiento,
            'nombre_usuario' => $nombre_usuario,
            'correo' => $email,
            'genero' => $genero,
            'pais' => $pais,
            'ciudad' => $ciudad,
            'display' => isset($_SESSION['user_name']) ? "display: block" : "display: none"
        ];

        $response = $this->usuarioService->save($user);

        if ($response->success) {
            $viewData['showModal'] = true;
            echo '<script>setTimeout(function() { window.location.href = "/usuario/showLoginForm"; }, 5000);</script>';
            $this->view->render("register", $viewData);
        } else {
            $viewData['error'] = $response->message;
            $this->view->render("register", $viewData);
        }
    }



    public function verificarDisponibilidad()
    {
        if (!isset($_GET['email'])) {
            echo json_encode(["error" => "Falta el parámetro email"]);
            exit;
        }
        $email = $_GET['email'];
        $resultado = $this->usuarioService->verificarDisponibilidad($email);
        if(empty($resultado)){
            $_SESSION['email_disponible'] = true;
        }else{
            $_SESSION['email_disponible'] = false;
        }

        echo json_encode(["available" => true, "resultado" => $resultado]);

    }
}