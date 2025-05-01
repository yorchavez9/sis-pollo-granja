<?php

require_once "../modelos/Usuario.modelo.php";
require_once "../controladores/Usuario.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorUsuarios::ctrCrearUsuario();
                break;
            case 'editar':
                ControladorUsuarios::ctrEditarUsuario();
                break;
            case 'actualizar':
                ControladorUsuarios::ctrActualizarUsuario();
                break;
            case 'cambiarEstado':
                ControladorUsuarios::ctrCambiarEstadoUsuario();
                break;
            case 'eliminar':
                ControladorUsuarios::ctrBorrarUsuario();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los usuarios o filtrar por parámetros GET
    $item = null;
    $valor = null;
    ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
}