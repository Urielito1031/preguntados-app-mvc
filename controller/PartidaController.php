<?php

class PartidaController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function pregunta(){
        $viewData = [
            // Datos para el menu desplegable
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil']

        ];
        $this->view->render("pregunta", $viewData);
    }
}