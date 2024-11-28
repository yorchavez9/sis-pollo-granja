<?php

require_once "Conexion.php";

class ModeloHistorialPago
{

    /*=============================================
	MOSTRAR HISTORIAL PAGO PDF
	=============================================*/
    static public function mdlMostrarHistorialPagoPDF($tabla_ventas, $tabla_historial_pago, $tabla_personas, $item, $valor)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
                                                hp.id_pago,
                                                p.razon_social,
                                                v.total_venta,
                                                hp.id_venta,
                                                hp.forma_pago,
                                                hp.monto_pago, 
                                                hp.numero_serie_pago,
                                                hp.comprobante_imagen,
                                                hp.fecha_registro,
                                                (v.total_venta - v.total_pago) AS monto_restante
                                            FROM 
                                            $tabla_ventas as v 
                                            inner join $tabla_historial_pago as hp on v.id_venta = hp.id_venta
                                            inner join $tabla_personas as p on p.id_persona = v.id_persona
                                            WHERE hp.$item = :$item");
        $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }


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
                                                        hp.forma_pago,
                                                        hp.monto_pago, 
                                                        hp.numero_serie_pago,
                                                        hp.comprobante_imagen,
                                                        hp.fecha_registro
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
                                                        hp.forma_pago,
                                                        hp.monto_pago, 
                                                        hp.numero_serie_pago,
                                                        hp.comprobante_imagen,
                                                        hp.fecha_registro
                                                    FROM 
                                                    $tabla_ventas as v inner join $tabla_historial_pago as hp on v.id_venta = hp.id_venta
                                                    inner join $tabla_personas as p on p.id_persona = v.id_persona WHERE v.$item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);                          
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    /*=============================================
	HISTORIAL DE PAGOS
	=============================================*/
    static public function mdlIngresoHistorialPago($tabla, $datos)
    {
        try {
            // Preparar la consulta SQL para la inserción
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (
                                                id_venta, 
                                                monto_pago, 
                                                forma_pago, 
                                                numero_serie_pago, 
                                                comprobante_imagen
                                            ) 
                                            VALUES (
                                                :id_venta, 
                                                :monto_pago, 
                                                :forma_pago, 
                                                :numero_serie_pago, 
                                                :comprobante_imagen)");

            // Vincular los valores a los parámetros de la consulta
            $stmt->bindParam(":id_venta", $datos["id_venta"], PDO::PARAM_INT);
            $stmt->bindParam(":monto_pago", $datos["monto_pago"], PDO::PARAM_STR);
            $stmt->bindParam(":forma_pago", $datos["forma_pago"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_serie_pago", $datos["numero_serie_pago"], PDO::PARAM_STR);
            $stmt->bindParam(":comprobante_imagen", $datos["comprobante_imagen"], PDO::PARAM_STR);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Después de la inserción, obtener el ID del último registro insertado
                $stmtSelect = Conexion::conectar()->prepare("SELECT id_pago FROM $tabla ORDER BY id_pago DESC LIMIT 1");
                $stmtSelect->execute();
                $result = $stmtSelect->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    // Retornar tanto el ID como el mensaje "ok"
                    return [
                        'id_pago' => $result['id_pago'],
                        'message' => 'ok'
                    ];
                } else {
                    // Si no se pudo obtener el ID, retornar el mensaje de error
                    return [
                        'id_pago' => null,
                        'message' => 'Error: No se pudo obtener el ID'
                    ];
                }
            } else {
                return [
                    'id_pago' => null,
                    'message' => 'Error: La inserción falló'
                ];
            }
        } catch (PDOException $e) {
            // Manejo de errores
            return [
                'id_pago' => null,
                'message' => "Error: " . $e->getMessage()
            ];
        }
        // Cerrar la conexión
        $stmt = null;
        $stmtSelect = null;
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
	ACTUALIZAR PAGO PENDIENTE
	=============================================*/

    static public function mdlActualizarPagoPendiente($tabla, $datos)
    {
        $respuesta = array();

        // Obtener el total actual de pagos para este egreso
        $stmt = Conexion::conectar()->prepare("SELECT total_pago FROM $tabla WHERE id_venta = :id_venta");
        $stmt->bindParam(":id_venta", $datos["id_venta"], PDO::PARAM_STR);
        $stmt->execute();
        $totalActual = $stmt->fetchColumn();

        // Calcular el nuevo total de pagos sumando el monto proporcionado
        $nuevoTotal = $totalActual + $datos["total_pago"];

        // Obtener el total de la compra
        $stmt = Conexion::conectar()->prepare("SELECT total_venta FROM $tabla WHERE id_venta = :id_venta");
        $stmt->bindParam(":id_venta",
            $datos["id_venta"],
            PDO::PARAM_STR
        );
        $stmt->execute();
        $totalCompra = $stmt->fetchColumn();

        // Verificar si el nuevo total de pagos supera el total de la compra
        if ($nuevoTotal > $totalCompra) {
            // Si supera el total de la compra, retornar error
            $respuesta["estado"] = "error";
            $respuesta["mensaje"] = "El total de los pagos supera el total de la venta";
        } else {
            // Si no supera el total de la compra, proceder con la actualización
            $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET total_pago = :nuevo_total_pago WHERE id_venta = :id_venta");
            $stmt->bindParam(":nuevo_total_pago", $nuevoTotal, PDO::PARAM_STR);
            $stmt->bindParam(":id_venta", $datos["id_venta"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                $respuesta["estado"] = "ok";
                $respuesta["mensaje"] = "El pago se realizó correctamente";
            } else {
                $respuesta["estado"] = "error";
                $respuesta["mensaje"] = "No se pudo realizar el total de pagos";
            }
        }

        return $respuesta;
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
