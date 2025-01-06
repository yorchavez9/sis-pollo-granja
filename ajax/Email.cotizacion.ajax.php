<?php
require_once '../controladores/Cotizacion.controllador.php';
require_once '../modelos/Cotizacion.modelo.php';
require_once '../vendor/phpmailer/phpmailer/src/Exception.php';
require_once '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once '../vendor/phpmailer/phpmailer/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Cargar el autoload de Composer
require '../vendor/autoload.php';

// Crear una instancia de PHPMailer; pasar `true` habilita las excepciones
$mail = new PHPMailer(true);


if (!isset($_POST['documento']) || !isset($_POST['id_cotizacion']) || !isset($_POST['email'])) {
    throw new Exception('Datos incompletos');
}else{

    // Obtener datos de la cotización
    $item = "id_cotizacion";
    $valor = $_POST['id_cotizacion'];
    $cotizacion = ControladorCotizacion::ctrMostrarListaCotizaciones($item, $valor);

    if (!$cotizacion) {
        throw new Exception('Cotización no encontrada');
    }

    try {
        // Configuración del servidor
        /* $mail->SMTPDebug = SMTP::DEBUG_SERVER;    */                   // Habilitar salida de depuración detallada
        $mail->isSMTP();                                            // Usar SMTP para enviar el correo
        $mail->Host       = 'smtp.hostinger.com';                   // Servidor SMTP para enviar el correo
        $mail->SMTPAuth   = true;                                   // Habilitar autenticación SMTP
        $mail->Username   = 'apuuray@apuuray.com';                  // Nombre de usuario SMTP
        $mail->Password   = 'Apuuray123$';                          // Contraseña SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Habilitar cifrado TLS implícito
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';                                    // Puerto TCP para la conexión (usar 587 para STARTTLS si es necesario)

        // Destinatarios
        $mail->setFrom('apuuray@apuuray.com', 'Apuuray');               // Remitente del correo
        $mail->addAddress($_POST['email'], 'Jorge Chavez Huincho');           // Agregar destinatario principal

        // Contenido del correo
        $mail->isHTML(true);                                        // Establecer el formato del correo en HTML
        $mail->Subject = 'Cotización N°' . $cotizacion['id_cotizacion'];                     // Asunto del correo
        $mail->Body = '
                <h2>Cotización Adjunta</h2>
                <p>Estimado cliente,</p>
                <p>Adjunto encontrará la cotización solicitada.</p>
                <p>Detalles:</p>
                <ul>
                    <li>Número: ' . $cotizacion['id_cotizacion'] . '</li>
                    <li>Fecha: ' . $cotizacion['fecha_cotizacion'] . '</li>
                    <li>Total: S/. ' . $cotizacion['total_cotizacion'] . '</li>
                </ul>
                <p>Gracias por su preferencia.</p>
            ';

        // Adjuntar PDF
        $mail->addStringAttachment(
            file_get_contents("../extensiones/" . $cotizacion['tipo_comprobante_sn'] . "/" . $cotizacion['tipo_comprobante_sn'] . "/cotizacion/" . $cotizacion['tipo_comprobante_sn'] . "_c_". $cotizacion['id_cotizacion'].".pdf"),
            $cotizacion['tipo_comprobante_sn'] . '_c_'. $cotizacion['id_cotizacion'].'.pdf'
        );

        // Enviar el correo
        $mail->send();
        echo json_encode([
            'success' => true,
            'message' => 'Correo enviado exitosamente'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al enviar: ' . $e->getMessage()
        ]);
    }

}
