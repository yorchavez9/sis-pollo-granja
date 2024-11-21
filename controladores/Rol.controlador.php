<?php

class ControladorRol
{

    /*=============================================
	REGISTRO DE ROL
	=============================================*/
    static public function ctrCrearRol()
    {
        $tabla = "rol";
        $datos = array(
            "nombre_rol" => $_POST["nombre_rol"],
            "descripcion" => $_POST["descripcion_rol"]
        );
        $respuesta = ModeloRol::mdlIngresarRol($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        } else {
            echo json_encode("error");
        }
    }

    /*=============================================
	MOSTRAR ROLES
	=============================================*/
    static public function ctrMostrarRoles($item, $valor)
    {
        $tabla = "rol";
        $respuesta = ModeloRol::mdlMostrarRoles($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
	EDITAR ROL
	=============================================*/

    static public function ctrEditarRol()
    {
        $tabla = "rol";
        $datos = array(
            "id_rol" => $_POST["edit_id_rol"],
            "nombre_rol" => $_POST["edit_nombre_rol"],
            "descripcion" => $_POST["edit_descripcion_rol"]
        );
        $respuesta = ModeloRol::mdlEditarRol($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode($respuesta);
        }
        
    }

    /*=============================================
	BORRAR ROL
	=============================================*/
    static public function ctrBorraRol()
    {
        if (isset($_POST["delete_id_rol"])) {
            $tabla = "rol";
            $datos = $_POST["delete_id_rol"];
            $respuesta = ModeloRol::mdlBorrarRol($tabla, $datos);
            if ($respuesta == "ok") {
                echo json_encode("ok");
            }
        }
    }
}
