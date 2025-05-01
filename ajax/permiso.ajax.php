<?php

require_once "../modelos/Permiso.modelo.php";
require_once "../controladores/Permiso.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'guardar':
                ControladorPermiso::ctrGuardarPermiso();
                break;
            case 'actualizar':
                ControladorPermiso::ctrActualizarPermiso();
                break;
            case 'eliminar':
                ControladorPermiso::ctrEliminarPermiso();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los permisos
    ControladorPermiso::ctrMostrarPermisos();
}