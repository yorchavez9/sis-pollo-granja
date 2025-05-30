<?php
require_once "Conexion.php";

class ModeloPermiso
{
    /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    static public function mdlMostrarPermisos()
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT p.*, r.nombre as nombre_rol, m.nombre as nombre_modulo, a.nombre as nombre_accion
                 FROM permisos p
                 LEFT JOIN roles r ON p.id_rol = r.id_rol
                 LEFT JOIN modulos m ON p.id_modulo = m.id_modulo
                 LEFT JOIN acciones a ON p.id_accion = a.id_accion
                 ORDER BY p.id_rol, p.id_modulo, p.id_accion"
            );

            $stmt_usuario_rol = Conexion::conectar()->prepare("SELECT * FROM usuarios INNER JOIN usuario_roles ON usuarios.id_usuario = usuario_roles.id_usuario");
            $stmt_usuario_rol->execute();

            $stmt->execute();
            return json_encode([
                "status" => true,
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC),
                "usuarios_roles" => $stmt_usuario_rol->fetchAll(PDO::FETCH_ASSOC)
            ]);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    GUARDAR PERMISO
    =============================================*/
    static public function mdlGuardarPermiso($idUsuario, $idRol, $permisos)
    {
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();

            // Verificar si ya existen permisos para este rol
            /*  $stmtVerificar = $conexion->prepare(
                "SELECT COUNT(*) as total FROM permisos WHERE id_rol = :id_rol"
            );
            $stmtVerificar->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $existenPermisos = $stmtVerificar->fetch()['total'] > 0;

            if ($existenPermisos) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existen permisos para este rol. Use la opción de editar."
                ]);
            } */

            // Insertar usuario roles
            $stmtInsertarUsuarioModelos = $conexion->prepare("INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (:id_usuario, :id_rol)");
            $stmtInsertarUsuarioModelos->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $stmtInsertarUsuarioModelos->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
            $stmtInsertarUsuarioModelos->execute();

            // Insertar los nuevos permisos
            $stmtInsertar = $conexion->prepare(
                "INSERT INTO permisos (id_usuario, id_rol, id_modulo, id_accion) 
                 VALUES (:id_usuario, :id_rol, :id_modulo, :id_accion)"
            );

            foreach ($permisos as $permiso) {
                $stmtInsertar->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
                $stmtInsertar->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
                $stmtInsertar->bindParam(":id_modulo", $permiso['id_modulo'], PDO::PARAM_INT);
                $stmtInsertar->bindParam(":id_accion", $permiso['id_accion'], PDO::PARAM_INT);
                $stmtInsertar->execute();
            }

            $conexion->commit();

            return json_encode([
                "status" => true,
                "message" => "Permisos guardados correctamente"
            ]);
        } catch (Exception $e) {
            $conexion->rollBack();
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ACTUALIZAR PERMISO Y ROL DE USUARIO - VERSIÓN CORREGIDA
    =============================================*/
    static public function mdlActualizarPermiso($idUsuario, $idRol, $permisos)
    {
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();

            // 1. Actualizar el rol del usuario
            $stmtActualizarRol = $conexion->prepare(
                "UPDATE usuario_roles SET 
                id_rol = :id_rol,
                fecha_asignacion = NOW()
             WHERE id_usuario = :id_usuario"
            );
            $stmtActualizarRol->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $stmtActualizarRol->bindParam(":id_rol", $idRol, PDO::PARAM_INT);

            if (!$stmtActualizarRol->execute()) {
                throw new Exception("Error al actualizar el rol del usuario");
            }

            // 2. Eliminar todos los permisos anteriores del usuario
            $stmtEliminarPermisos = $conexion->prepare(
                "DELETE FROM permisos 
             WHERE id_usuario = :id_usuario"
            );
            $stmtEliminarPermisos->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);

            if (!$stmtEliminarPermisos->execute()) {
                throw new Exception("Error al eliminar permisos anteriores");
            }

            // 3. Insertar los nuevos permisos
            if (!empty($permisos)) {
                $stmtInsertarPermiso = $conexion->prepare(
                    "INSERT INTO permisos 
                (id_usuario, id_rol, id_modulo, id_accion, fecha_asignacion) 
                VALUES (:id_usuario, :id_rol, :id_modulo, :id_accion, NOW())"
                );

                foreach ($permisos as $permiso) {
                    $stmtInsertarPermiso->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
                    $stmtInsertarPermiso->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
                    $stmtInsertarPermiso->bindParam(":id_modulo", $permiso['id_modulo'], PDO::PARAM_INT);
                    $stmtInsertarPermiso->bindParam(":id_accion", $permiso['id_accion'], PDO::PARAM_INT);

                    if (!$stmtInsertarPermiso->execute()) {
                        throw new Exception("Error al insertar permiso");
                    }
                }
            }

            $conexion->commit();

            return json_encode([
                "status" => true,
                "message" => "Rol y permisos actualizados correctamente"
            ]);
        } catch (Exception $e) {
            if ($conexion) {
                $conexion->rollBack();
            }
            return json_encode([
                "status" => false,
                "message" => "Error: " . $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ELIMINAR PERMISO
    =============================================*/
    static public function mdlEliminarPermiso($idUsuario)
    {
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();

            // Eliminar permisos asociados al rol
            $stmtPermisos = $conexion->prepare(
                "DELETE FROM permisos WHERE id_usuario = :id_usuario"
            );
            $stmtPermisos->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);

            if (!$stmtPermisos->execute()) {
                throw new Exception("Error al eliminar los permisos: " . implode(" ", $stmtPermisos->errorInfo()));
            }

            // Eliminar el registro de la tabla usuario_roles
            $stmtUsuarioRoles = $conexion->prepare(
                "DELETE FROM usuario_roles WHERE id_usuario = :id_usuario"
            );
            $stmtUsuarioRoles->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);

            if (!$stmtUsuarioRoles->execute()) {
                throw new Exception("Error al eliminar el registro de usuario_roles: " . implode(" ", $stmtUsuarioRoles->errorInfo()));
            }

            $conexion->commit();

            return json_encode([
                "status" => true,
                "message" => "Permisos eliminados correctamente"
            ]);
        } catch (Exception $e) {
            if ($conexion) {
                $conexion->rollBack();
            }
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
