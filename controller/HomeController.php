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
      if (!isset($_SESSION['user_id'])) {
         $this->view->render("login");
         exit;
      }

      $this->view->render("home");
   }
}