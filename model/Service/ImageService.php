<?php

namespace Service;
use Exception;

class ImageService
{
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function uploadImage(array $file): string
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Debes subir una imagen válida.");
        }


        $maxFileSize = 5 * 1024 * 1024; // 5MB en bytes
        if ($file['size'] > $maxFileSize) {
            throw new Exception("La imagen no puede exceder los 5MB.");
        }


        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new Exception("Formato de imagen no permitido. Los formatos permitidos son: " . implode(', ', $this->allowedExtensions));
        }

        $uploadDir = __DIR__ . '/../../public/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }


        $imageName = uniqid() . '-' . basename($file['name']);
        $imagePath = 'public/img/' . $imageName;
        $uploadFile = $uploadDir . $imageName;

        if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
            throw new Exception("Error al subir la imagen.");
        }

        return $imagePath;
    }

}
