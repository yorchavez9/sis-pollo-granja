<?php

class ControladorUsuarioRoles
{

    /*=============================================
	REGISTRO DE USUARIO ROLES
	=============================================*/

    static public function ctrCrearUsuarioRoles()
    {
        $idUsuario = $_POST['id_usuario_roles'];
        $roles = json_decode($_POST['usuario_roles'], true);

        if(!is_array($roles) || empty($roles)){
            echo json_encode("Error");
            return;
        }

        $tabla = "usuario_rol";
        $respuesta = "ok";

        foreach ($roles as $idRol) {
            $datos = array(
                "id_usuario" => $idUsuario,
                "id_rol" => $idRol
            );

            $resultado = ModeloUsuariorRoles::mdlIngresarUsuarioRoles($tabla, $datos);
            if($resultado != "ok"){
                $respuesta = "Error";
            }
        }
        echo json_encode($respuesta);
    }

    /*=============================================
	MOSTRAR USUARIO ROLES
	=============================================*/

    static public function ctrMostrarUsuarioRoles($item, $valor)
    {
        // Define los nombres de las tablas
        $tablaUsuarioRol = "usuario_rol";
        $tablaUsuarios = "usuarios";
        $tablaRoles = "rol";

        // Llama al modelo pasando los nombres de las tablas
        $respuesta = ModeloUsuariorRoles::mdlMostrarUsuarioRoles($tablaUsuarioRol, $tablaUsuarios, $tablaRoles, $item, $valor);

        return $respuesta;
    }

    /*=============================================
	EDITAR USUARIO ROLES
	=============================================*/

    public static function ctrEditarUsuarioRoles()
    {

        $idUsuario = $_POST['edit_id_usuario_roles'];
        $rolesSeleccionados = json_decode($_POST['edit_usuario_roles'], true);

        $tabla = "usuario_rol";
        $datos = array(
            "id_usuario" => $idUsuario,
            "roles" => $rolesSeleccionados,
        );

        $respuesta = ModeloUsuariorRoles::mdlEditarUsuarioRoles($tabla,$datos);

        if ($respuesta == "ok") {
            echo json_encode("ok");
        } else {
            echo json_encode("error");
        }

    }


    /*=============================================
	BORRAR USUARIO ROLES
	=============================================*/

    static public function ctrBorrarUsuarioRoles()
    {

        if (isset($_POST["deleteIdUsuarioRol"])) {
            $tabla = "usuario_rol";
            $datos = $_POST["deleteIdUsuarioRol"];
            $respuesta = ModeloUsuariorRoles::mdlBorrarUsuarioRoles($tabla, $datos);
            if ($respuesta == "ok") {
                echo json_encode("ok");
            }
        }
    }
}
