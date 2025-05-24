<?php

class GroupController
{
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function request()
    {
        $integrantes["integrantes"] = $this->model->getIntegrantes();
        $this->view->render("group", $integrantes);
    }

    public function add()
    {
        $nombre = $_POST["nombre"]; // Acá valido que el parametro no sea vacio o erroneo
        $instrumento = $_POST["instrumento"]; // Acá valido que el parametro no sea vacio o erroneo
        $this->model->add($nombre, $instrumento);
        $this->redirectTo("/group/success");
    }

    public function success()
    {
        $this->view->render("groupSuccess");
    }

    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }
}