<?php

require_once "Conexion.php";

class ModeloUsuariorRoles
{

    /*=============================================
	MOSTRAR USUARIOS ROLES
	=============================================*/

    static public function mdlMostrarUsuarioRoles($item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT 
													u.id_usuario,
													u.nombre_usuario,
													u.usuario,
													u.correo,
													u.contrasena,
													u.imagen_usuario,
													u.estado_usuario,
													u.imagen_usuario,
													r.nombre_rol,
													GROUP_CONCAT(DISTINCT m.modulo ORDER BY m.modulo) AS modulos,
													GROUP_CONCAT(DISTINCT 
														CONCAT(m.modulo, ': ', a.accion) ORDER BY m.modulo, a.accion) AS acciones
												FROM 
													usuarios u
												JOIN 
													usuario_roles ur ON u.id_usuario = ur.id_usuario
												JOIN 
													roles r ON ur.id_rol = r.id_rol
												JOIN 
													role_modulos rm ON r.id_rol = rm.id_rol
												JOIN 
													modulos m ON rm.id_modulo = m.id_modulo
												JOIN 
													role_acciones ra ON r.id_rol = ra.id_rol AND m.id_modulo = ra.id_modulo
												JOIN 
													acciones a ON ra.id_accion = a.id_accion WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT 
													u.id_usuario,
													u.nombre_usuario,
													u.usuario,
													u.correo,
													u.contrasena,
													u.imagen_usuario,
													u.estado_usuario,
													u.imagen_usuario,
													r.nombre_rol,
                                                    r.id_rol,
													GROUP_CONCAT(DISTINCT m.modulo ORDER BY m.modulo) AS modulos,
													GROUP_CONCAT(DISTINCT 
														CONCAT(m.modulo, ': ', a.accion) ORDER BY m.modulo, a.accion) AS acciones
												FROM 
													usuarios u
												JOIN 
													usuario_roles ur ON u.id_usuario = ur.id_usuario
												JOIN 
													roles r ON ur.id_rol = r.id_rol
												JOIN 
													role_modulos rm ON r.id_rol = rm.id_rol
												JOIN 
													modulos m ON rm.id_modulo = m.id_modulo
												JOIN 
													role_acciones ra ON r.id_rol = ra.id_rol AND m.id_modulo = ra.id_modulo
												JOIN 
													acciones a ON ra.id_accion = a.id_accion
												GROUP BY 
													u.id_usuario, r.id_rol");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}
	}

    /*=============================================
	REGISTRO DE USUARIO ROLES
	=============================================*/

    static public function mdlIngresarUsuarioRoles($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (id_usuario, id_rol) VALUES (:id_usuario, :id_rol)");
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
            $stmt->bindParam(":id_rol", $datos["id_rol"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    "status" => true,
                    "message" => "Registro exitoso"
                ];
            } else {
                return [
                    "status" => false,
                    "message" => "Error al registrar"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => false,
                "message" => "Error: ". $e->getMessage()
            ];
        }
    }

    /*=============================================
	REGISTRO DE ROL MODULOS
	=============================================*/
    static public function mdlIngresarRolModulos($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (id_usuario, id_rol, id_modulo) VALUES (:id_usuario, :id_rol, :id_modulo)");
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
            $stmt->bindParam(":id_rol", $datos["id_rol"], PDO::PARAM_INT);
            $stmt->bindParam(":id_modulo", $datos["id_modulo"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    "status" => true,
                    "message" => "Registro exitoso"
                ];
            } else {
                return [
                    "status" => false,
                    "message" => "Error al registrar"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => false,
                "message" => "Error: ". $e->getMessage()
            ];
        }
    }

    /*=============================================
	REGISTRO DE ROL MODULO ACCION
	=============================================*/
    static public function mdlIngresarRolModuloAccion($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (id_usuario, id_rol, id_modulo, id_accion) VALUES (:id_usuario, :id_rol, :id_modulo, :id_accion)");
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
            $stmt->bindParam(":id_rol", $datos["id_rol"], PDO::PARAM_INT);
            $stmt->bindParam(":id_modulo", $datos["id_modulo"], PDO::PARAM_INT);
            $stmt->bindParam(":id_accion", $datos["id_accion"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    "status" => true,
                    "message" => "Las acciones se guardaron con éxito"
                ];
            } else {
                return [
                    "status" => false,
                    "message" => "Error al guardar las acciones"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => false,
                "message" => "Error: ". $e->getMessage()
            ];
        }
    }


    /*=============================================
	EDITAR USUARIO ROLES
	=============================================*/
    public static function mdlEditarUsuarioRoles($tabla, $datos)
    {
        try {
            // Primero, eliminar los roles actuales del usuario
            $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_usuario = :id_usuario");
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
            $stmt->execute();

            // Insertar los nuevos roles
            foreach ($datos["roles"] as $rol) {
                $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (id_usuario, id_rol) VALUES (:id_usuario, :id_rol)");
                $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
                $stmt->bindParam(":id_rol", $rol,
                    PDO::PARAM_INT
                );
                $stmt->execute();
            }

            return "ok";
        } catch (Exception $e) {
            return "error";
        }
    }


    /*=============================================
	BORRAR USUARIO ROLES
	=============================================*/
    static public function mdlBorrarUsuarioRoles($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_usuario = :id_usuario");
        $stmt->bindParam(":id_usuario", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Registro eliminado"
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al eliminar"
            ];
        }
    }

    /*=============================================
	BORRAR ROL MODULOS
	=============================================*/
    static public function mdlBorrarRolModulos($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_usuario = :id_usuario");
        $stmt->bindParam(":id_usuario", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Registro eliminado"
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al eliminar"
            ];
        }
    }

    /*=============================================
	BORRAR ROL MODULOS
	=============================================*/
    static public function mdlBorrarRolModuloAccion($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_usuario = :id_usuario");
        $stmt->bindParam(":id_usuario", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Registro eliminado"
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al eliminar"
            ];
        }
    }


}
