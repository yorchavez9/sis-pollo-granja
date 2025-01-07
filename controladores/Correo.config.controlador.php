<?php

class ControladorCorreoConfig
{

    /*=============================================
	REGISTRO DE CONFIGURACION CORREO
	=============================================*/
    static public function ctrCrearConfigCorreo()
    {
        $tabla = "config_correo";
        $datos = array(
            "id_usuario" => $_POST["id_usuario"],
            "smtp" => $_POST["smtp"],
            "usuario" => $_POST["usuario"],
            "password" => $_POST["password"],
            "puerto" => $_POST["puerto"],
            "correo_remitente" => $_POST["correo_remitente"],
            "nombre_remitente" => $_POST["nombre_remitente"]
        );
        $respuesta = ModeloCorreoConfig::mdlIngresarConfigCorreo($tabla,    $datos);
        if ($respuesta["status"] == true) {
            echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
        } else {
            echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
        }
    }

    /*=============================================
	MOSTRAR CONFIGURACION CORREO
	=============================================*/
    static public function ctrMostrarConfigCorreo($item, $valor)
    {
        $tabla = "config_correo";
        $respuesta = ModeloCorreoConfig::mdlMostrarConfigCorreo($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
	EDITAR CONFIGURACION CORREO
	=============================================*/
    static public function ctrEditarConfigCorreo()
    {
        $tabla = "config_correo";
        $datos = array(
            "id" => $_POST["edit_id_correo_config"],
            "id_usuario" => $_POST["id_usuario_edit"],
            "smtp" => $_POST["smtp_edit"],
            "usuario" => $_POST["usuario_edit"],
            "password" => $_POST["password_edit"],
            "puerto" => $_POST["puerto_edit"],
            "correo_remitente" => $_POST["correo_remitente_edit"],
            "nombre_remitente" => $_POST["nombre_remitente_edit"]
        );
        $respuesta = ModeloCorreoConfig::mdlEditarConfigCorreo($tabla, $datos);
        if ($respuesta["status"] == true) {
            echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
        }
    }

    /*=============================================
	BORRAR CONFIGURACION CORREO
	=============================================*/
    static public function ctrBorraConfigCorreo()
    {
        
        $tabla = "config_correo";
        $datos = $_POST["idCorreoConfigDelete"];
        $respuesta = ModeloCorreoConfig::mdlBorrarConfigCorreo($tabla, $datos);
        if ($respuesta["status"] == true) {
            echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
        }else{
            echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
        }
        
    }
}
