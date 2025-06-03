<?php

use Service\UsuarioService;

class RankingController
{
    private $view;
    private $usuarioService;

    public function __construct(UsuarioService $usuarioService, MustachePresenter $view) {
        $this->view = $view;
        $this->usuarioService = $usuarioService;
    }

    // Este metodo se repite en diferentes clases
    private function getUserSessionData() : array {
        return [
            'usuario' => $_SESSION['user_name'] ?? '',
            'foto_perfil' => $_SESSION['foto_perfil']
        ];
    }

    public function show():void {
        $viewData = array_merge($this->getUserSessionData(), [
            'jugadores' => 'metodo del servicio que traiga los jugadores',

        ]);
        $this->view->render("ranking", $viewData);
    }


}