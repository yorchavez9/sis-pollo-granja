<?php

require_once "Conexion.php";

class ModeloConfiguracionSistema
{

    /*=============================================
	MOSTRAR CONFIGURACION 
	=============================================*/
    static public function mdlMostrarConfiguracionSistema($tabla, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tabla");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    /*=============================================
	REGISTRAR CONFIGURACION 
	=============================================*/
    public static function mdlIngresarConfiguracionSistema($tabla, $datos)
    {
        // Crear la consulta SQL directamente
        $sql = "INSERT INTO $tabla (
                nombre, 
                icon_pestana, 
                img_sidebar, 
                img_sidebar_min, 
                img_login, 
                icon_login
            ) VALUES (
                :nombre, 
                :icon_pestana, 
                :img_sidebar, 
                :img_sidebar_min, 
                :img_login, 
                :icon_login
            )";

        // Preparar la consulta
        $stmt = Conexion::conectar()->prepare($sql);

        // Vincular parámetros explícitamente
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":icon_pestana", $datos["icon_pestana"], PDO::PARAM_STR);
        $stmt->bindParam(":img_sidebar", $datos["img_sidebar"], PDO::PARAM_STR);
        $stmt->bindParam(":img_sidebar_min", $datos["img_sidebar_min"], PDO::PARAM_STR);
        $stmt->bindParam(":img_login", $datos["img_login"], PDO::PARAM_STR);
        $stmt->bindParam(":icon_login", $datos["icon_login"], PDO::PARAM_STR);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null; // Liberar recursos
    }


    /*=============================================
	EDITAR CONFIGURACION 
	=============================================*/
    static public function mdlEditarConfiguracionSistema($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																nombre = :nombre, 
																icon_pestana = :icon_pestana, 
																img_sidebar = :img_sidebar, 
																img_sidebar_min = :img_sidebar_min, 
																img_login = :img_login, 
																icon_login = :icon_login
																WHERE id_img = :id_img");

        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":icon_pestana", $datos["icon_pestana"], PDO::PARAM_STR);
        $stmt->bindParam(":img_sidebar", $datos["img_sidebar"], PDO::PARAM_STR);
        $stmt->bindParam(":img_sidebar_min", $datos["img_sidebar_min"], PDO::PARAM_STR);
        $stmt->bindParam(":img_login", $datos["img_login"], PDO::PARAM_STR);
        $stmt->bindParam(":icon_login", $datos["icon_login"], PDO::PARAM_STR);
        $stmt->bindParam(":id_img", $datos["id_img"], PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }


    /*=============================================
	BORRAR CONFIGURACION 
	=============================================*/
    static public function mdlBorrarConfiguracionSistema($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_img = :id_img");
        $stmt->bindParam(":id_img", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }
}
