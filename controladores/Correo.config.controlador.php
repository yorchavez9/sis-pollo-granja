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
        $tabla = "categorias";
        $respuesta = ModeloCorreoConfig::mdlMostrarConfigCorreo($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
	EDITAR CONFIGURACION CORREO
	=============================================*/
    static public function ctrEditarConfigCorreo()
    {
        if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["edit_nombre_categoria"])) {
            $tabla = "categorias";
            $datos = array(
                "id_categoria" => $_POST["edit_id_categoria"],
                "nombre_categoria" => $_POST["edit_nombre_categoria"],
                "descripcion" => $_POST["edit_descripcion_categoria"]
            );
            $respuesta = ModeloCorreoConfig::mdlEditarConfigCorreo($tabla, $datos);
            if ($respuesta == "ok") {
                echo json_encode("ok");
            }
        } else {
            echo json_encode("ok");
        }
    }

    /*=============================================
	BORRAR CONFIGURACION CORREO
	=============================================*/
    static public function ctrBorraConfigCorreo()
    {
        if (isset($_POST["deleteIdCategoria"])) {
            $tabla = "categorias";
            $datos = $_POST["deleteIdCategoria"];
            $respuesta = ModeloCorreoConfig::mdlBorrarConfigCorreo($tabla, $datos);
            if ($respuesta == "ok") {
                echo json_encode("ok");
            }
        }
    }
}
