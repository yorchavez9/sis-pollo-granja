<?php
require_once '../controladores/Cotizacion.controllador.php';
require_once '../modelos/Cotizacion.modelo.php';
require_once '../vendor/phpmailer/phpmailer/src/Exception.php';
require_once '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once '../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once '../controladores/Configuracion.ticket.controlador.php';
require_once '../modelos/Configuracion.ticket.modelo.php';
require_once '../controladores/Correo.config.controlador.php';
require_once '../modelos/Correo.config.modelo.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Cargar el autoload de Composer
require '../vendor/autoload.php';

// Crear una instancia de PHPMailer; pasar `true` habilita las excepciones
$mail = new PHPMailer(true);

$itemConfig = null;
$valorConfig = null;
$configuracion = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($itemConfig, $valorConfig);
$configuracionCorreo = ControladorCorreoConfig::ctrMostrarConfigCorreo($itemConfig, $valorConfig);

foreach ($configuracion as $key => $value) {
    $nombreEmpresa = $value['nombre_empresa'];
    $telefono = $value['telefono'];
    $direccion = $value['direccion'];
}

foreach ($configuracionCorreo as $key => $correoconfig) {
    $nombreEmpresa = $correoconfig['id'];
    $id_usuario = $correoconfig['id_usuario'];
    $smtp = $correoconfig['smtp'];
    $usuario_config = $correoconfig['usuario'];
    $password_config = $correoconfig['password'];
    $puerto_config = $correoconfig['puerto'];
    $correo_remitente = $correoconfig['correo_remitente'];
    $nombre_remitente = $correoconfig['nombre_remitente'];
}

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
        $mail->Host       = $smtp;                   // Servidor SMTP para enviar el correo
        $mail->SMTPAuth   = true;                                   // Habilitar autenticación SMTP
        $mail->Username   = $usuario_config;                  // Nombre de usuario SMTP
        $mail->Password   = $password_config;                          // Contraseña SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Habilitar cifrado TLS implícito
        $mail->Port       = $puerto_config;
        $mail->CharSet    = 'UTF-8';                                    // Puerto TCP para la conexión (usar 587 para STARTTLS si es necesario)

        // Destinatarios
        $mail->setFrom($correo_remitente, $nombre_remitente);               // Remitente del correo
        $mail->addAddress($_POST['email'], '');           // Agregar destinatario principal

        // Contenido del correo
        $mail->isHTML(true); // Establecer el formato del correo en HTML
        $mail->Subject = 'Cotización Adjunta';
        $template = file_get_contents('plantillas/correo_cotizacion.html');   
                                             
        $body = str_replace(
            ['{{id_cotizacion}}', '{{fecha_cotizacion}}', '{{total_cotizacion}}','{{numero_telefono}}', '{{direccion_negocio}}'],
            [$cotizacion['id_cotizacion'], $cotizacion['fecha_cotizacion'], $cotizacion['total_cotizacion'], $telefono, $direccion],
            $template
        );

        $mail->Body = $body;
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
