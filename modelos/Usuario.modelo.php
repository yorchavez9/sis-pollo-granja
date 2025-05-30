<?php
require_once "Conexion.php";

class ModeloUsuarios
{
    /* =============================================
    MOSTRAR USUARIO PARA INICIAR SESIÓN
    ============================================= */
    static public function mdlMostrarLoginUsuario($tabla, $item, $valor)
    {
        try {
            $stmt = Conexion::conectar()->prepare("
                SELECT u.*, s.nombre_sucursal 
                FROM $tabla u
                LEFT JOIN sucursales s ON u.id_sucursal = s.id_sucursal
                WHERE u.$item = :$item
            ");
            
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $usuario ?: false;
            
        } catch (PDOException $e) {
            error_log("Error en mdlMostrarLoginUsuario: " . $e->getMessage());
            return false;
        } finally {
            $stmt = null;
        }
    }
    
    /* =============================================
    ACTUALIZAR USUARIO (ÚLTIMO LOGIN)
    ============================================= */
    static public function mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2)
    {
        try {
            $stmt = Conexion::conectar()->prepare("
                UPDATE $tabla 
                SET $item1 = :$item1 
                WHERE $item2 = :$item2
            ");
            
            $stmt->bindParam(":".$item1, $valor1);
            $stmt->bindParam(":".$item2, $valor2);
            
            $resultado = $stmt->execute();
            
            return $resultado ? "ok" : "error";
            
        } catch (PDOException $e) {
            error_log("Error en mdlActualizarUsuario: " . $e->getMessage());
            return "error";
        } finally {
            $stmt = null;
        }
    }
    
    /* =============================================
    OBTENER DATOS COMPLETOS DEL USUARIO PARA SESIÓN
    ============================================= */
    static public function mdlObtenerDatosSesion($idUsuario)
    {
        try {
            $usuario = self::mdlMostrarLoginUsuario("usuarios", "id_usuario", $idUsuario);
            
            if (!$usuario) {
                return false;
            }
            
            $datosSesion = [
                "id_usuario" => $usuario["id_usuario"],
                "nombre_usuario" => $usuario["nombre_usuario"],
                "usuario" => $usuario["usuario"],
                "imagen_usuario" => $usuario["imagen_usuario"],
                "id_sucursal" => $usuario["id_sucursal"],
                "nombre_sucursal" => $usuario["nombre_sucursal"] ?? null,
                "ultimo_login" => $usuario["ultimo_login"]
            ];
            
            return $datosSesion;
            
        } catch (PDOException $e) {
            error_log("Error en mdlObtenerDatosSesion: " . $e->getMessage());
            return false;
        }
    }

    /* =============================================
    MOSTRAR USUARIOS
    ============================================= */
    static public function mdlMostrarUsuarios($tabla, $item, $valor)
    {
        try {
            $where = ($item !== null) ? "WHERE u.$item = :$item" : "";
            
            $stmt = Conexion::conectar()->prepare("
                SELECT 
                    u.*, 
                    s.nombre_sucursal
                FROM $tabla u 
                LEFT JOIN sucursales s ON u.id_sucursal = s.id_sucursal
                $where
                ORDER BY u.id_usuario DESC
            ");
            
            if ($item !== null) {
                $stmt->bindParam(":".$item, $valor);
            }
            
            $stmt->execute();
            
            $result = ($item !== null) ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                "status" => true,
                "data" => $result,
                "exists" => ($item !== null) ? ($result !== false) : null
            ];
            
        } catch (PDOException $e) {
            error_log("Error en mdlMostrarUsuarios: " . $e->getMessage());
            return [
                "status" => false,
                "message" => "Error al obtener los usuarios"
            ];
        } finally {
            $stmt = null;
        }
    }

    /* ==============================================
    OBTENER ROLES DEL USUARIO
    ============================================== */
    static public function mdlObtenerRolesUsuario($idUsuario){
        $stmt = Conexion::conectar()->prepare("
            SELECT r.* FROM roles r
            JOIN usuario_roles ur ON r.id_rol = ur.id_rol
            WHERE ur.id_usuario = :id_usuario
            AND r.estado = 1
        ");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
        $stmt->close();
        $stmt = null;
    }

    /* ==============================================
    OBTENER PERMISOS DEL USUARIO
    ============================================== */
    static public function mdlObtenerPermisosUsuario($idUsuario){
        $stmt = Conexion::conectar()->prepare("
            SELECT 
                m.nombre as modulo,
                a.nombre as accion
            FROM permisos p
            JOIN usuario_roles ur ON p.id_rol = ur.id_rol
            JOIN modulos m ON p.id_modulo = m.id_modulo
            JOIN acciones a ON p.id_accion = a.id_accion
            WHERE ur.id_usuario = :id_usuario
            AND a.estado = 1
            ORDER BY m.id_modulo ASC
        ");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $permisos = array();
        $resultados = $stmt->fetchAll();
        
        foreach($resultados as $resultado){
            $modulo = $resultado["modulo"];
            $accion = $resultado["accion"];
            
            if(!isset($permisos[$modulo])){
                $permisos[$modulo] = array(
                    "acciones" => array()
                );
            }
            
            $permisos[$modulo]["acciones"][] = $accion;
        }
        
        return $permisos;
        $stmt->close();
        $stmt = null;
    }

    /* =============================================
    VERIFICAR USUARIO EXISTENTE
    ============================================= */
    static public function mdlVerificarUsuarioExistente($tabla, $item, $valor, $excluirId)
    {
        try {
            $stmt = Conexion::conectar()->prepare("
                SELECT COUNT(*) as total 
                FROM $tabla 
                WHERE $item = :$item 
                AND id_usuario != :excluirId
            ");
            
            $stmt->bindParam(":".$item, $valor);
            $stmt->bindParam(":excluirId", $excluirId, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en mdlVerificarUsuarioExistente: " . $e->getMessage());
            return false;
        } finally {
            $stmt = null;
        }
    }

    /* =============================================
    REGISTRAR USUARIO
    ============================================= */
    static public function mdlIngresarUsuario($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("
                INSERT INTO $tabla(
                    id_sucursal, nombre_usuario, 
                    telefono, correo, usuario, 
                    contrasena, imagen_usuario, estado
                ) VALUES (
                    :id_sucursal, :nombre_usuario, 
                    :telefono, :correo, :usuario, 
                    :contrasena, :imagen_usuario, :estado
                )
            ");
            
            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":nombre_usuario", $datos["nombre_usuario"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
            $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
            $stmt->bindParam(":contrasena", $datos["contrasena"], PDO::PARAM_STR);
            $stmt->bindParam(":imagen_usuario", $datos["imagen_usuario"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Usuario registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            }
            
            return json_encode([
                "status" => false,
                "message" => "Error al registrar el usuario"
            ]);
            
        } catch (PDOException $e) {
            error_log("Error en mdlIngresarUsuario: " . $e->getMessage());
            return json_encode([
                "status" => false,
                "message" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } finally {
            $stmt = null;
        }
    }

    /* =============================================
    ACTUALIZAR USUARIO
    ============================================= */
    static public function mdlActualizarUsuarioU($tabla, $datos)
    {
        try {
            $sql = "UPDATE $tabla SET ";
            $params = [];

            if (isset($datos["id_sucursal"])) {
                $sql .= "id_sucursal = :id_sucursal, ";
                $params[":id_sucursal"] = $datos["id_sucursal"];
            }

            if (isset($datos["nombre_usuario"])) {
                $sql .= "nombre_usuario = :nombre_usuario, ";
                $params[":nombre_usuario"] = $datos["nombre_usuario"];
            }

            if (isset($datos["telefono"])) {
                $sql .= "telefono = :telefono, ";
                $params[":telefono"] = $datos["telefono"];
            }

            if (isset($datos["correo"])) {
                $sql .= "correo = :correo, ";
                $params[":correo"] = $datos["correo"];
            }

            if (isset($datos["usuario"])) {
                $sql .= "usuario = :usuario, ";
                $params[":usuario"] = $datos["usuario"];
            }

            if (!empty($datos["contrasena"])) {
                $sql .= "contrasena = :contrasena, ";
                $params[":contrasena"] = $datos["contrasena"];
            }

            if (!empty($datos["imagen_usuario"])) {
                $sql .= "imagen_usuario = :imagen_usuario, ";
                $params[":imagen_usuario"] = $datos["imagen_usuario"];
            }

            $sql = rtrim($sql, ", ") . " WHERE id_usuario = :id_usuario";
            $params[":id_usuario"] = $datos["id_usuario"];

            $stmt = Conexion::conectar()->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Usuario actualizado con éxito"
                ]);
            }

            return json_encode([
                "status" => false,
                "message" => "Error al actualizar el usuario"
            ]);

        } catch (PDOException $e) {
            error_log("Error en mdlActualizarUsuarioU: " . $e->getMessage());
            return json_encode([
                "status" => false,
                "message" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } finally {
            $stmt = null;
        }
    }

    /* =============================================
    CAMBIAR ESTADO DE USUARIO
    ============================================= */
    static public function mdlCambiarEstadoUsuario($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("
                UPDATE $tabla 
                SET estado = :estado 
                WHERE id_usuario = :id_usuario
            ");
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado del usuario actualizado"
                ]);
            }
            
            return json_encode([
                "status" => false,
                "message" => "Error al cambiar el estado"
            ]);
            
        } catch (PDOException $e) {
            error_log("Error en mdlCambiarEstadoUsuario: " . $e->getMessage());
            return json_encode([
                "status" => false,
                "message" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } finally {
            $stmt = null;
        }
    }

    /* =============================================
    ELIMINAR USUARIO
    ============================================= */
    static public function mdlBorrarUsuario($tabla, $idUsuario)
    {
        try {
            $usuario = self::mdlMostrarUsuarios($tabla, "id_usuario", $idUsuario);
            
            if ($usuario["status"] === false) {
                return json_encode($usuario);
            }
            
            $stmt = Conexion::conectar()->prepare("
                DELETE FROM $tabla 
                WHERE id_usuario = :id_usuario
            ");
            
            $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                
                return json_encode([
                    "status" => true,
                    "message" => "Usuario eliminado con éxito"
                ]);
            }
            
            return json_encode([
                "status" => false,
                "message" => "Error al eliminar el usuario"
            ]);
            
        } catch (PDOException $e) {
            error_log("Error en mdlBorrarUsuario: " . $e->getMessage());
            return json_encode([
                "status" => false,
                "message" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } finally {
            $stmt = null;
        }
    }
}