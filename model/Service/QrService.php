<?php

namespace Service;


use QRcode;

require "vendor/phpqrcode/qrlib.php";

class QrService
{
    public function __construct(){

    }
    public static function generarQrCode($nombreUsuario) : String {
        // Nombre de la carpeta
        $dir = 'temp/';

        if (!file_exists($dir)){
            mkdir($dir);
        }

        // Modificar esto para que cada qr sea diferente segun el usuario
        $nombreArchivo = $dir . 'qr.png';

        $tamanio = 10;
        $level = 'M';
        $frameSize = 3;

        // Modificar la url para mostrar el perfil segun el usuario
        $contenido = 'http://localhost/usuario/showProfile?usuario=' . $nombreUsuario ;

        QRcode::png($contenido, $nombreArchivo, $level, $tamanio, $frameSize);

        return $nombreArchivo;
    }
}