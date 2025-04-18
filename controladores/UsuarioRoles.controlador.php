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
            $errors = [];
            $success = true;

            foreach ($modulos as $id_modulo => $acciones) {
                $datosModulo = array(
                    "id_usuario" => $id_usuario,
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
                        "id_usuario" => $id_usuario,
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
        $tabla_usuario_roles = "usuario_roles";
        $tabla_role_modulos = "role_modulos";
        $tabla_rol_modulo_accion = "role_acciones";

        $id_usuario = $_POST["edit_id_usuario"];
        $id_rol = $_POST["edit_id_rol"];
        $modulos = json_decode($_POST["modulosAcciones"], true);

        try {
            // Eliminar registros existentes
            $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla_rol_modulo_accion WHERE id_usuario = :id_usuario AND id_rol = :id_rol");
            $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(":id_rol", $id_rol, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla_role_modulos WHERE id_usuario = :id_usuario AND id_rol = :id_rol");
            $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(":id_rol", $id_rol, PDO::PARAM_INT);
            $stmt->execute();

            // Insertar nuevos datos
            $errors = [];
            $success = true;

            foreach ($modulos as $id_modulo => $acciones) {
                $datosModulo = array(
                    "id_usuario" => $id_usuario,
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
                        "id_usuario" => $id_usuario,
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

            if ($success) {
                echo json_encode([
                    "status" => true,
                    "message" => "Los permisos se actualizaron correctamente."
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Ocurrieron algunos errores: " . implode(", ", $errors)
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                "status" => false,
                "message" => "Error al actualizar: " . $e->getMessage()
            ]);
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
        
        $respuesta = ModeloUsuariorRoles::mdlBorrarUsuarioRoles($tabla_usuario_rol, $id_usuario, $id_rol);
        
        if($respuesta["status"] == true){
            $response_role_modulos = ModeloUsuariorRoles::mdlBorrarRolModulos($tabla_role_modulos, $id_usuario, $id_rol);
            if($response_role_modulos["status"] == true){
                $response_role_acciones = ModeloUsuariorRoles::mdlBorrarRolModuloAccion($tabla_role_acciones, $id_usuario, $id_rol);
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