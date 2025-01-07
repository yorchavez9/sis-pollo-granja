<?php

require_once "Conexion.php";

class ModeloCorreoConfig
{

    /*=============================================
	MOSTRAR CONFIGURACION DEL CORREO
	=============================================*/
    static public function mdlMostrarConfigCorreo($tabla, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tabla ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    /*=============================================
	REGISTRAR CONFIGURACION DEL CORREO
	=============================================*/
    static public function mdlIngresarConfigCorreo($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                    id_usuario, 
                                                                    smtp,
                                                                    usuario,
                                                                    password,
                                                                    puerto,
                                                                    correo_remitente,
                                                                    nombre_remitente
                                                                    )
                                                                    VALUES (
                                                                    :id_usuario, 
                                                                    :smtp,
                                                                    :usuario,
                                                                    :password,
                                                                    :puerto,
                                                                    :correo_remitente,
                                                                    :nombre_remitente)");

        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt->bindParam(":smtp", $datos["smtp"], PDO::PARAM_STR);
        $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
        $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
        $stmt->bindParam(":puerto", $datos["puerto"], PDO::PARAM_INT);
        $stmt->bindParam(":correo_remitente", $datos["correo_remitente"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre_remitente", $datos["nombre_remitente"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Correo de Configuración registrado exitosamente."
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al registrar la configuración."
            ];
        }
    }

    /*=============================================
	EDITAR CONFIGURACION DEL CORREO
	=============================================*/
    static public function mdlEditarConfigCorreo($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_usuario = :id_usuario, 
																smtp = :smtp,
																usuario = :usuario,
																password = :password,
																puerto = :puerto,
																correo_remitente = :correo_remitente,
																nombre_remitente = :nombre_remitente
																WHERE id = :id");

        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt->bindParam(":smtp", $datos["smtp"], PDO::PARAM_STR);
        $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
        $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
        $stmt->bindParam(":puerto", $datos["puerto"], PDO::PARAM_INT);
        $stmt->bindParam(":correo_remitente", $datos["correo_remitente"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre_remitente", $datos["nombre_remitente"], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Correo de Configuración actualizado exitosamente."
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al actualizar la configuración."
            ];
        }
    }

    /*=============================================
	ACTUALIZAR CONFIGURACION DEL CORREO
	=============================================*/
    static public function mdlActualizarConfigCorreo($tabla, $item1, $valor1, $item2, $valor2)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
        $stmt->bindParam(":" . $item1, $valor1, PDO::PARAM_STR);
        $stmt->bindParam(":" . $item2, $valor2, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    /*=============================================
	BORRAR CONFIGURACION DEL CORREO
	=============================================*/

    static public function mdlBorrarConfigCorreo($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "Correo de Configuración eliminado exitosamente."
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al eliminar la configuración."
            ];
        }
    }
}
