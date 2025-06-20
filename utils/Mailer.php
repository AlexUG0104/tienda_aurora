<?php
// Archivo: Mailer.php
namespace Aurora;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);



        try {
            $this->mail->SMTPDebug = 2;
            $this->mail->Debugoutput = 'error_log';
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = 'infoauroraboutiquecr@gmail.com';
            $this->mail->Password   = 'jfth kwbv gwxv nbnw'; 
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $this->mail->Port       = 587;

            // DEBUG para SMTP
        $this->mail->SMTPDebug = 2; // Puedes usar 0 para desactivar
        $this->mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug level $level: $str");
        };
        
            $this->mail->setFrom('infoauroraboutiquecr@gmail.com', 'Aurora Boutique');
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("Mailer Init Error: " . $e->getMessage());
        }
    }

    /**
     * Envía correo HTML
     * @param string $destinatario Dirección email del cliente
     * @param string $asunto Asunto del correo
     * @param string $cuerpoHTML Contenido HTML del correo
     * @return bool True si se envió, False si hubo error
     */
    public function enviarCorreo($destinatario, $asunto, $cuerpoHTML) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($destinatario);
            $this->mail->Subject = $asunto;
            $this->mail->Body    = $cuerpoHTML;

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar correo: {$this->mail->ErrorInfo}");
            error_log("Error al enviar correo: " . $e->getMessage());
            error_log("PHPMailer error: " . $this->mail->ErrorInfo);

            return false;
        }
    }
}
