<?php

require_once "Conexion.php";

class ModeloUsuariorRoles
{

    /*=============================================
	MOSTRAR USUARIOS ROLES
	=============================================*/

    static public function mdlMostrarUsuarioRoles($tablaUsuarioRol, $tablaUsuarios, $tablaRoles, $item, $valor)
    {
        try {
            $conexion = Conexion::conectar();

            if ($item != null) {
                $stmt = $conexion->prepare("SELECT u.id_usuario, u.nombre_usuario, r.id_rol, r.nombre_rol, r.descripcion FROM $tablaUsuarioRol ur JOIN $tablaUsuarios u ON ur.id_usuario = u.id_usuario JOIN $tablaRoles r ON ur.id_rol = r.id_rol WHERE u.$item = :$item");
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = $conexion->prepare("SELECT u.id_usuario, u.nombre_usuario, r.nombre_rol, r.descripcion
                                            FROM $tablaUsuarioRol ur
                                            JOIN $tablaUsuarios u ON ur.id_usuario = u.id_usuario
                                            JOIN $tablaRoles r ON ur.id_rol = r.id_rol
                                            ORDER BY u.id_usuario DESC");
            }

            $stmt->execute();
            return $stmt->fetchAll(); // Retorna múltiples filas
        } catch (PDOException $e) {
            return "error: " . $e->getMessage();
        } finally {
            $stmt = null;
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
                return "ok";
            } else {
                return "error";
            }

            $stmt = null; // Cerrar conexión
        } catch (PDOException $e) {
            return "error";
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
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }
}
