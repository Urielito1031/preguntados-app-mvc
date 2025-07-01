<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ajustá las rutas según el punto de entrada de tu app
require_once __DIR__ . '/../vendor/phpmailer/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/SMTP.php';

class MailerService
{
    public function enviarValidacion(string $destinatario, int $token): void
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'seehoferezequiel@gmail.com';
            $mail->Password = 'vdce bnco bnvm rbvu';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Remitente y destinatario
            $mail->setFrom('seehoferezequiel@gmail.com', 'QuizCode');
            $mail->addAddress($destinatario);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Valida tu cuenta con el siguiente codigo token';
            $mail->Body = "<p><strong>TOKEN de 6 dígitos:</strong></p><br><br>
                            <p>{$token}</p>";
            $mail->send();
        } catch (Exception $e) {
            error_log("No se pudo enviar el correo: {$mail->ErrorInfo}");
        }
    }
}

