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

    public function show() : void {
        $ranking = $this->usuarioService->getRanking();
        foreach ($ranking as $posicion => &$jugador) {
            $jugador['posicion'] = $posicion + 1;
        }

        $historialDePartidas = $this->usuarioService->getHistorialDePartidas($_SESSION['user_id']);

        foreach ($historialDePartidas as $posicionPartidas=> &$orden) {
            $orden['numero'] = $posicionPartidas + 1;
        }

        $viewData = array_merge($this->getUserSessionData(), [
            'jugadores' => $ranking,
            'partidas' => $historialDePartidas,
            'titulo_ranking' => 'Mejores Partidas'
        ]);



        $this->view->render("ranking", $viewData);
    }


}