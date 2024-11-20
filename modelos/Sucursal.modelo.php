<?php

require_once "Conexion.php";

class ModeloSucursal
{

    /*=============================================
	MOSTRAR SUCURSALES
	=============================================*/
    static public function mdlMostrarSucursales($tabla, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tabla ORDER BY id_sucursal DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
        $stmt = null;
    }

    /*=============================================
	REGISTRAR SUCURSAL
	=============================================*/
    static public function mdlIngresarSucursal($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                    nombre_sucursal, 
                                                                    direccion,
                                                                    telefono
                                                                    )
                                                                    VALUES (
                                                                    :nombre_sucursal, 
                                                                    :direccion,
                                                                    :telefono
                                                                    )");

        $stmt->bindParam(":nombre_sucursal", $datos["nombre_sucursal"], PDO::PARAM_STR);
        $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }

    /*=============================================
	EDITAR SUCURSAL
	=============================================*/
    static public function mdlEditarSucursal($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																nombre_sucursal = :nombre_sucursal, 
																direccion = :direccion,
																telefono = :telefono,
																WHERE id_sucursal = :id_sucursal");

        $stmt->bindParam(":nombre_sucursal", $datos["nombre_sucursal"], PDO::PARAM_STR);
        $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
        $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }

    /*=============================================
	ACTUALIZAR SUCURSAL
	=============================================*/
    static public function mdlActualizarSucursal($tabla, $item1, $valor1, $item2, $valor2)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
        $stmt->bindParam(":" . $item1, $valor1, PDO::PARAM_STR);
        $stmt->bindParam(":" . $item2, $valor2, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }

    /*=============================================
	BORRAR SUCURSAL
	=============================================*/
    static public function mdlBorrarSucursal($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_sucursal = :id_sucursal");
        $stmt->bindParam(":id_sucursal", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }
}
