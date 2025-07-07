<?php

namespace Service;

use QRcode;

require "vendor/phpqrcode/qrlib.php";

class QrService
{
    public function __construct(){}

    public static function generarQrCode($nombreUsuario, $id_usuario) : String {
        $dir = 'public/qr/';

        if (!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        $nombreArchivo = $dir . $nombreUsuario . '.png';

        $tamanio = 10;
        $level = 'M';
        $frameSize = 3;

        // Modificar la url para mostrar el perfil segun el usuario
        $contenido = 'http://localhost/profile/showProfileById?id=' . $id_usuario ;

        QRcode::png($contenido, $nombreArchivo, $level, $tamanio, $frameSize);

        return '/public/qr/' . $nombreUsuario . '.png';
    }
}