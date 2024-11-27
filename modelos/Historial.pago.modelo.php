<?php

require_once "Conexion.php";

class ModeloHistorialPago
{

    /*=============================================
	MOSTRAR HISTORIAL PAGO
	=============================================*/
    static public function mdlMostrarHistorialPago($tabla_ventas, $tabla_historial_pago, $tabla_personas, $item, $valor)
    {
        if ($item == "id_pago") {
            $stmt = Conexion::conectar()->prepare("SELECT 
                                                        hp.id_pago,
                                                        p.razon_social,
                                                        hp.id_venta,
                                                        hp.fecha_pago,
                                                        hp.tipo_pago,
                                                        hp.forma_pago,
                                                        hp.monto_pago, 
                                                        hp.estado_pago,
                                                        v.numero_serie_pago,
                                                        hp.comprobante_imagen
                                                    FROM 
                                                    $tabla_ventas as v inner join $tabla_historial_pago as hp on v.id_venta = hp.id_venta
                                                    inner join $tabla_personas as p on p.id_persona = v.id_persona
                                                    WHERE $item = :$item");

            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        }else if($item == "id_venta") {
            $stmt = Conexion::conectar()->prepare("SELECT 
                                                        hp.id_pago,
                                                        p.razon_social,
                                                        hp.id_venta,
                                                        hp.fecha_pago,
                                                        hp.tipo_pago,
                                                        hp.forma_pago,
                                                        hp.monto_pago, 
                                                        hp.estado_pago,
                                                        v.numero_serie_pago,
                                                        hp.comprobante_imagen
                                                    FROM 
                                                    $tabla_ventas as v inner join $tabla_historial_pago as hp on v.id_venta = hp.id_venta
                                                    inner join $tabla_personas as p on p.id_persona = v.id_persona WHERE v.$item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);                          
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }


    /*=============================================
	REGISTRO DE HISTORIAL DE PAGO
	=============================================*/
    static public function mdlIngresarHistorialPago($tabla, $datos)
    {
        if (empty($datos["fecha_vencimiento"])) {
            $datos["fecha_vencimiento"] = NULL; // Asignar NULL si no se proporciona fecha
        }
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                id_categoria,
                                                                codigo_producto, 
                                                                nombre_producto, 
                                                                precio_producto, 
                                                                stock_producto, 
                                                                fecha_vencimiento, 
                                                                descripcion_producto, 
                                                                imagen_producto) 
                                                                VALUES (
                                                                :id_categoria, 
                                                                :codigo_producto, 
                                                                :nombre_producto, 
                                                                :precio_producto, 
                                                                :stock_producto, 
                                                                :fecha_vencimiento, 
                                                                :descripcion_producto, 
                                                                :imagen_producto)");

        $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
        $stmt->bindParam(":codigo_producto", $datos["codigo_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre_producto", $datos["nombre_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":precio_producto", $datos["precio_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":stock_producto", $datos["stock_producto"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_vencimiento", $datos["fecha_vencimiento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_producto", $datos["descripcion_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":imagen_producto", $datos["imagen_producto"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {

            return "error";
        }
    }

    /*=============================================
	EDITAR HISTORIAL PAGO
	=============================================*/
    static public function mdlEditarHistorialPago($tabla, $datos)
    {
        if (empty($datos["fecha_vencimiento"])) {
            $datos["fecha_vencimiento"] = NULL; // Asignar NULL si no se proporciona fecha
        }
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_categoria = :id_categoria, 
																codigo_producto = :codigo_producto, 
																nombre_producto = :nombre_producto, 
																precio_producto = :precio_producto, 
																stock_producto = :stock_producto, 
																fecha_vencimiento = :fecha_vencimiento, 
																descripcion_producto = :descripcion_producto, 
																imagen_producto = :imagen_producto
																WHERE id_producto = :id_producto");

        $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
        $stmt->bindParam(":codigo_producto", $datos["codigo_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre_producto", $datos["nombre_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":precio_producto", $datos["precio_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":stock_producto", $datos["stock_producto"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_vencimiento", $datos["fecha_vencimiento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_producto", $datos["descripcion_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":imagen_producto", $datos["imagen_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    /*=============================================
	ACTUALIZAR HISTORIAL PAGO
	=============================================*/

    static public function mdlActualizarHistorialPago($tabla, $item1, $valor1, $item2, $valor2)
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
	BORRAR HISTORIAL PAGO
	=============================================*/

    static public function mdlBorrarHistorialPago($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_producto = :id_producto");
        $stmt->bindParam(":id_producto", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }
}
