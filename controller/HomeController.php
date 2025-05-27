<?php

class HomeController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function show()
    {


        if (!isset($_SESSION['user_name'])) {

            header('Location: /usuario/showLoginForm');
            exit;
        }


        $data = ['usuario' => $_SESSION['user_name'] ?? ''];
        $this->view->render("home", $data); //
    }
}