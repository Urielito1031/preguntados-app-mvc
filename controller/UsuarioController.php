<?php

use Entity\Usuario;
use Entity\Ubicacion;
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

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginForm();
            return;
        }

        $email = $_POST['correo'] ?? '';
        $password = $_POST['contrasenia'] ?? '';

        // Delegar TODA la validación al servicio
        $response = $this->usuarioService->login($email, $password);

        $viewData = ['error' => $response->message, 'logo_url' => '/public/img/LogoQuizCode.png'];

        if (isset($_SESSION['user_name'])) {
            $viewData['display'] = "display: block";
        } else {
            $viewData['display'] = "display: none";
        }

        if ($response->success) {
            $this->handleLoginSuccess($response->data);
            switch ($_SESSION['id_rol']) {
                case 1:
                    header('Location: /home/showAdmin');
                    exit;
                case 2:
                    header('Location: /home/showEditor');
                    exit;
                default:
                    header('Location: /home/show');
                    exit;
            }

        } else {
            $this->view->render("login", $viewData); //
        }
    }

    private function handleLoginSuccess(Usuario $usuario)
    {
        $_SESSION['user_id'] = $usuario->getId();
        $_SESSION['user_email'] = $usuario->getCorreo();
        $_SESSION['user_name'] = $usuario->getNombreUsuario();
        $_SESSION['foto_perfil'] = $usuario->getUrlFotoPerfil();
        $_SESSION['puntaje_total'] = $usuario->getPuntajeTotal();
        $_SESSION['id_rol'] = $usuario->getIdRol();
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


    public function showProfile()
    {

        // REALIZAR ESTOS METODOS
        $idCiudad = $this->usuarioService->obtenerIdCiudadDeUsuario($_SESSION['user_id']);

        $ubicacionObtenida = $this->ubicacionService->obtenerPaisYCiudadDelUsuario($idCiudad);
        // REALIZAR ESTOS METODOS
        var_dump($ubicacionObtenida->getCiudad()->getNombre());
        var_dump($ubicacionObtenida->getPais()->getNombre());

        $ubicacionUrl = urlencode($ubicacionObtenida->getCiudad()->getNombre() . ', ' . $ubicacionObtenida->getPais()->getNombre());

        $url = "https://maps.google.com/maps?q={$ubicacionUrl}&output=embed";
        var_dump($url);
        $viewData = ['usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil'] ?? '',
            'puntaje_total' => $_SESSION['puntaje_total'] ?? '',
            'mapa_url' => $url
        ];

        $this->view->render("profile", $viewData);
    }
}