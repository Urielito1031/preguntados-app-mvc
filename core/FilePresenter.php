<?php

class FilePresenter
{
    public function __construct()
    {
    }

    public function render($viewName, $data = [])
    {
        require_once("view/header.php");
        require_once("view/" . $viewName . "View.php");
        require_once("view/footer.php");
    }

}