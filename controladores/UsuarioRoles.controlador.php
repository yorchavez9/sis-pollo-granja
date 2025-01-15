<?php

class ControladorUsuarioRoles
{

    /*=============================================
	REGISTRO DE USUARIO ROLES
	=============================================*/
    static public function ctrCrearUsuarioRoles()
    {
        $tabla_usuario_roles = "usuario_roles";
        $tabla_role_modulos = "role_modulos";
        $tabla_rol_modulo_accion = "role_acciones";

        $id_usuario = $_POST["id_usuario_permiso"];
        $id_rol = $_POST["id_rol_permiso"];
        $modulos = json_decode($_POST["modulosAcciones"], true);

        $datos = array(
            "id_usuario" => $id_usuario,
            "id_rol" => $id_rol
        );

        $respuesta = ModeloUsuariorRoles::mdlIngresarUsuarioRoles($tabla_usuario_roles, $datos);

        if ($respuesta["status"] == true) {
            $errors = []; // Almacena errores si ocurren
            $success = true; // Controla si todo se realizó con éxito

            foreach ($modulos as $id_modulo => $acciones) {
                $datosModulo = array(
                    "id_rol" => $id_rol,
                    "id_modulo" => $id_modulo
                );
                $response = ModeloUsuariorRoles::mdlIngresarRolModulos($tabla_role_modulos, $datosModulo);

                if ($response["status"] != true) {
                    $success = false;
                    $errors[] = $response["message"];
                    continue;
                }

                foreach ($acciones as $id_accion) {
                    $datosRolModuloAccion = array(
                        "id_rol" => $id_rol,
                        "id_modulo" => $id_modulo,
                        "id_accion" => $id_accion
                    );

                    $respuestaAccion = ModeloUsuariorRoles::mdlIngresarRolModuloAccion($tabla_rol_modulo_accion, $datosRolModuloAccion);

                    if ($respuestaAccion["status"] != true) {
                        $success = false;
                        $errors[] = $respuestaAccion["message"];
                    }
                }
            }

            // Emitir un único mensaje según el resultado
            if ($success) {
                echo json_encode([
                    "status" => true,
                    "message" => "Todos los datos se guardaron correctamente."
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Ocurrieron algunos errores: " . implode(", ", $errors)
                ]);
            }
        } else {
            echo json_encode([
                "status" => false,
                "message" => $respuesta["message"]
            ]);
        }
    }


    /*=============================================
	MOSTRAR USUARIO ROLES
	=============================================*/

    static public function ctrMostrarUsuarioRoles($item, $valor)
    {
        $respuesta = ModeloUsuariorRoles::mdlMostrarUsuarioRoles($item, $valor);
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

        $respuesta = ModeloUsuariorRoles::mdlEditarUsuarioRoles($tabla, $datos);

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
        $tabla_usuario_rol = "usuario_roles";
        $tabla_role_modulos = "role_modulos";
        $tabla_role_acciones = "role_acciones";

        $id_usuario = $_POST["idUsuarioPermisoDelete"];
        $id_rol = $_POST["idRolPermisoDelete"];

        $respuesta = ModeloUsuariorRoles::mdlBorrarUsuarioRoles($tabla_usuario_rol, $id_usuario);
        
        if($respuesta["status"] == true){
            $response_role_modulos = ModeloUsuariorRoles::mdlBorrarRolModulos($tabla_role_modulos, $id_rol);
            if($response_role_modulos["status"] == true){
                $response_role_acciones = ModeloUsuariorRoles::mdlBorrarRolModuloAccion($tabla_role_acciones, $id_rol);
                if($response_role_acciones["status"] == true){
                    echo json_encode([
                        "status" => true,
                        "message" => "Los datos se han borrado correctamente."
                    ]);
                }else{
                    echo json_encode([
                        "status" => false,
                        "message" => "Ocurrió un error al borrar las acciones del rol: ". $response_role_acciones["message"]
                    ]);
                }
            }else{
                echo json_encode([
                    "status" => false,
                    "message" => "Ocurrió un error al borrar los módulos del rol: ". $response_role_modulos["message"]
                ]);
            }
        }else{
            echo json_encode([
                "status" => false,
                "message" => "Ocurrió un error al borrar los roles del usuario: ". $respuesta["message"]
            ]);
        }
    }
}
