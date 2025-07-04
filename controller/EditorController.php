<?php

use Service\PreguntaService;
use Registry\CategoriaRegistry;
use Service\CategoriaService;

require_once __DIR__ . '/../Model/Registry/CategoriaRegistry.php';
class EditorController{

    private $view;

    private PreguntaService $preguntaService;
    private CategoriaService $categoriaService;
    public function __construct(MustachePresenter $view, PreguntaService $preguntaService,CategoriaService $categoriaService)
    {
        $this->preguntaService = $preguntaService;
        $this->categoriaService = $categoriaService;
        $this->view = $view;
    }

    private function getUserSessionData() : array {
        return [
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
    }
    public function verPreguntas()
    {
        $preguntas = $this->preguntaService->getPreguntas();

        $viewData =  array_merge($this->getUserSessionData(), [
            'texto' => 'Estamos funcionando',
            'preguntas' => $preguntas
        ]);

        $this->view->render("administradorpreguntas", $viewData);
    }

    public function edicionRealizada(){
        if(!$this->haySessionDeEdicionActiva()){
            header('Location: ../home/showEditor');
        }
        $viewData =array_merge($this->getUserSessionData(), [
            'mensaje' => $this->getMensajeSession(),
        ]);
        $this->clearSession();
        return $this->view->render("edicionrealizada",$viewData);
    }
    public function nuevaPregunta(){
        $categorias = $this->categoriaService->getCategorias();

        $viewData =array_merge($this->getUserSessionData(), [
            'categorias' => $categorias
        ]);

        return $this->view->render("editornuevapregunta", $viewData);
    }

    public function editarPregunta(){

        if (empty($_POST['id'])){
            header('Location: ../editor/verPreguntas');
        }
        $pregunta = $this->preguntaService->findByIdParaEditor($_POST['id']);
        $categorias = $this->categoriaService->getCategorias();

        $respuestasIncorrectas = $pregunta->data['respuestasincorrectas'];

        for ($i = 0; $i < count($respuestasIncorrectas); $i++) {
            $indiceDeseado = $i +1;
            $respuestasIncorrectas[$i]["name"] = "respuesta$indiceDeseado";
            $respuestasIncorrectas[$i]["indice"] = "$indiceDeseado";
        }

        $viewData = array_merge($this->getUserSessionData(), [
            'id' => $pregunta->data['data']['id'],
            'enunciado' => $pregunta->data['data']['enunciado'],
            'respuesta_correcta' => $pregunta->data['data']['respuesta_correcta'][0]['respuesta'],
            'idRespuestaCorrecta'=> $pregunta->data['data']['respuesta_correcta'][0]['idRespuestaCorrecta'],
            'respuestas' => $respuestasIncorrectas,
            'categoriaActual' => [
                'id' => $pregunta->data['categoria']->getId(),
                'descripcion' => $pregunta->data['categoria']->getDescripcion(),
                'color' => $pregunta->data['categoria']->getColor()
            ],
            'categorias' => $categorias
        ]);

        $this->view->render("editarpregunta", $viewData);
    }

    public function borrarPregunta(){
        if (empty($_POST['id'])){
            header('Location: ../editor/verPreguntas');
        }

        $resultado = $this->preguntaService->eliminarPregunta($_POST['id']);

        $this->setMensajeSession($resultado);
        header('Location: ../editor/edicionRealizada');
    }

    public function processNuevaPregunta(){
        $pregunta['enunciado'] = $_POST['enunciado'];
        $pregunta['idCategoria'] = $_POST['idCategoria'];
        $pregunta['respuestaCorrecta'] = $_POST['respuesta_correcta'];

        $respuesta1 = array( 'respuesta' => $_POST['respuesta1']);
        $respuesta2 = array( 'respuesta' => $_POST['respuesta2']);
        $respuesta3 = array( 'respuesta' => $_POST['respuesta3']);

        $pregunta['respuestas'] =array($respuesta1,$respuesta2,$respuesta3);

        $resultado = $this->preguntaService->guardarNuevaPregunta($pregunta);
        $this->setMensajeSession($resultado);
        header('Location: ../editor/edicionRealizada');
    }

    public function processEdit(){
        $pregunta['idPregunta'] = $_POST['idPregunta'];
        $pregunta['enunciado'] = $_POST['enunciado'];
        $pregunta['idCategoria'] = $_POST['idCategoria'];

        $respuestaCorrecta = array('id' => $_POST['IDRespuestaCorrecta'], 'respuesta' => $_POST['respuesta_correcta']);
        $respuesta1 = array('id' => $_POST['IDrespuesta1'], 'respuesta' => $_POST['respuesta1']);
        $respuesta2 = array('id' => $_POST['IDrespuesta2'], 'respuesta' => $_POST['respuesta2']);
        $respuesta3 = array('id' => $_POST['IDrespuesta3'], 'respuesta' => $_POST['respuesta3']);

        $pregunta['respuestas'] =array($respuestaCorrecta,$respuesta1,$respuesta2,$respuesta3);

        $resultado = $this->preguntaService->editarPregunta($pregunta);
        $this->setMensajeSession($resultado);
        header('Location: ../editor/edicionRealizada');
    }

    public function verReportes(){}

    public function verSugeridas(){

        $preguntas = $this->preguntaService->getPreguntasSugeridas();

        foreach ($preguntas as &$pregunta) {
            $pregunta['respuestas'] = [];

            // Traemos las respuestas de la pregunta actual
            $respuestas = $this->preguntaService->getRespuestasSugeridas($pregunta['id']);

            // Recorremos las respuestas y las agregamos al array de la pregunta
            foreach ($respuestas as $respuesta) {
                if($pregunta['id'] == $respuesta['id_pregunta'])
                    $pregunta['respuestas'][] = $respuesta['respuesta'];

            }
        }
        unset($pregunta);

        $viewData = [
            'preguntas' => $preguntas
        ];

        $this->view->render("preguntasSugeridas", $viewData);
    }

    public function haySessionDeEdicionActiva(): bool{
        return isset($_SESSION['mensajedeEdicion']);
    }
    public function clearSession(): void
    {
        unset(
            $_SESSION['mensajedeEdicion']
        );
    }

    public function setMensajeSession($mensaje)
    {
        $_SESSION['mensajedeEdicion']= $mensaje;
    }

    public function getMensajeSession(){
        if($this->haySessionDeEdicionActiva()){
            return $_SESSION['mensajedeEdicion'];
        }
    }

}