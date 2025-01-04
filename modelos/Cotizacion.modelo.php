<?php

require_once "Conexion.php";

class ModeloCotizacion
{

    /*=============================================
	MOSTRAR COTIZACIONES
	=============================================*/
    static public function mdlMostrarListaCotizacion($tabla_personas, $tabla_cotizacion, $tabla_usuarios, $tabla_s_n, $item, $valor)
    {
        // Si hay un filtro
        if ($item != null) {
            $sql = "SELECT  
						c.id_cotizacion,
						u.nombre_usuario,
						u.id_usuario,
						p.id_persona,
						p.razon_social,
						p.numero_documento,
						p.direccion,
						p.telefono,
						p.email,
						sn.tipo_comprobante_sn, 
						c.impuesto, 
						c.tipo_pago, 
						c.total_cotizacion,
						c.sub_total,
						c.igv_total, 
						c.total_pago, 
						c.fecha_cotizacion, 
						c.hora_cotizacion,  
						c.estado_pago
					FROM 
						$tabla_s_n AS sn 
					INNER JOIN 
						$tabla_cotizacion AS c ON sn.id_serie_num = c.id_serie_num 
					INNER JOIN 
						$tabla_personas AS p ON c.id_persona = p.id_persona
					INNER JOIN $tabla_usuarios AS u ON u.id_usuario = c.id_usuario
					WHERE $item = :$item";

            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(); // Retorna un solo resultado
        } else {
            // Sin filtros
            $sql = "SELECT 
                    c.id_cotizacion,
					u.nombre_usuario,
					u.id_usuario,
                    p.id_persona,
                    p.razon_social,
					p.numero_documento,
					p.direccion,
					p.telefono,
					p.email,
                    sn.tipo_comprobante_sn,
                    c.validez, 
					c.impuesto, 
                    c.tipo_pago, 
					c.sub_total,
					c.igv_total, 
                    c.total_pago, 
                    c.total_cotizacion, 
                    c.fecha_cotizacion, 
                    c.hora_cotizacion, 
                    c.estado_pago,
                    c.estado
                FROM 
                    $tabla_s_n AS sn 
                INNER JOIN 
                    $tabla_cotizacion AS c ON sn.id_serie_num = c.id_serie_num 
                INNER JOIN 
                    $tabla_personas AS p ON c.id_persona = p.id_persona
				INNER JOIN $tabla_usuarios AS u ON u.id_usuario = c.id_usuario 
				ORDER BY  c.id_cotizacion DESC";

            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    /*=============================================
	MOSTRAR REPORTE DE COTIZACIONES
	=============================================*/
    public static function mdlReporteCotizaciones($tabla_personas, $tabla_ventas, $tabla_usuarios, $tabla_s_n, $filtros)
    {
        // Crear la consulta base
        $sql = "SELECT 
						c.id_venta,
						u.nombre_usuario,
						u.id_usuario,
						p.id_persona,
						p.razon_social,
						p.numero_documento,
						p.direccion,
						p.telefono,
						p.email,
						sn.tipo_comprobante_sn, 
						sn.serie_prefijo,
						c.num_comprobante,
						c.impuesto, 
						c.tipo_pago, 
						c.total_venta,
						c.sub_total,
						c.igv, 
						c.total_pago, 
						c.fecha_venta, 
						c.hora_venta, 
						c.estado_pago
					FROM 
						$tabla_s_n AS sn 
					INNER JOIN 	$tabla_ventas AS v ON sn.id_serie_num = c.id_serie_num 
					INNER JOIN  $tabla_personas AS p ON c.id_persona = p.id_persona
					INNER JOIN $tabla_usuarios AS u ON u.id_usuario = c.id_usuario
					WHERE 1 = 1";

        // Arreglo de parámetros para la consulta
        $params = [];

        if (!empty($filtros['filtro_usuario_venta'])) {
            $sql .= " AND u.id_usuario = :usuario";
            $params[':usuario'] = $filtros['filtro_usuario_venta'];
        }

        // Filtro por rango de fecha de vencimiento
        if (!empty($filtros['filtro_fecha_desde_venta']) && !empty($filtros['filtro_fecha_hasta_venta'])) {
            $sql .= " AND c.fecha_venta BETWEEN :fecha_desde AND :fecha_hasta";
            $params[':fecha_desde'] = $filtros['filtro_fecha_desde_venta'];
            $params[':fecha_hasta'] = $filtros['filtro_fecha_hasta_venta'];
        }
        if (!empty($filtros['filtro_tipo_comprobante_venta'])) {
            $sql .= " AND sn.tipo_comprobante_sn = :tipo_comprobante";
            $params[':tipo_comprobante'] = $filtros['filtro_tipo_comprobante_venta'];
        }
        if (!empty($filtros['filtro_estado_pago_venta'])) {
            $sql .= " AND c.estado_pago = :estado_pago";
            $params[':estado_pago'] = $filtros['filtro_estado_pago_venta'];
        }
        // Filtro por rango de total de compra
        if (!empty($filtros['filtro_total_venta_min']) && !empty($filtros['filtro_total_venta_max'])) {
            $sql .= " AND c.total_venta BETWEEN :total_min AND :total_max";
            $params[':total_min'] = $filtros['filtro_total_venta_min'];
            $params[':total_max'] = $filtros['filtro_total_venta_max'];
        }
        // Ordenar por fecha de producto (DESC)
        $sql .= " ORDER BY c.fecha_venta DESC";

        // Preparar y ejecutar la consulta
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->execute($params);

        // Retornar los resultados
        return $stmt->fetchAll();
    }

    /*=============================================
	MOSTRAR SUMA TOTAL DE COTIZACIONES
	=============================================*/
    static public function mdlMostrarSumaTotalCotizacion($tablaD, $tablaV, $tablaP, $item, $valor)
    {
        $stmt = Conexion::conectar()->prepare("SELECT SUM(total_venta) AS total_ventas FROM $tablaV");
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_ventas'];
    }

    /*=============================================
	MOSTRAR SUMA TOTAL DE COTIZACION AL CONTADO
	=============================================*/
    static public function mdlMostrarSumaTotalCotizacionContado($tablaD, $tablaV, $tablaP, $item, $valor)
    {

        $stmt = Conexion::conectar()->prepare("SELECT SUM(total_venta) AS total_ventas_contado FROM $tablaV WHERE tipo_pago = 'contado'");
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total_ventas_contado'];
    }

    /*=============================================
	MOSTRAR SUMA TOTAL DE VENTAS AL CRÉDITO
	=============================================*/
    static public function mdlMostrarSumaTotalCredito($tablaD, $tablaV, $tablaP, $item, $valor)
    {

        $stmt = Conexion::conectar()->prepare("SELECT SUM(total_venta) AS total_ventas_credito FROM $tablaV WHERE tipo_pago = 'credito'");

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total_ventas_credito'];
    }

    /*=============================================
	MOSTRAR REPORTE DE COTIZACIONES
	=============================================*/
    static public function mdlMostrarReporteCotizaciones($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde,    $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
    {

        if ($fecha_desde != "" && $fecha_hasta != "" && $id_usuario != null && $tipo_pago == null) {


            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaPersona AS p INNER JOIN $tablaVentas AS v ON p.id_persona = c.id_persona	WHERE c.fecha_venta BETWEEN '$fecha_desde' AND '$fecha_hasta' AND c.id_usuario = $id_usuario");

            $stmt->execute();

            return $stmt->fetchAll();
        } else if ($fecha_desde != "" && $fecha_hasta != "" && $id_usuario != null && $tipo_pago != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaPersona AS p INNER JOIN $tablaVentas AS v ON p.id_persona = c.id_persona WHERE c.fecha_venta BETWEEN '$fecha_desde' AND '$fecha_hasta' AND c.id_usuario = $id_usuario AND c.tipo_pago = '$tipo_pago'");

            $stmt->execute();

            return $stmt->fetchAll();
        } else {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaPersona as p INNER JOIN $tablaVentas AS v ON p.id_persona = c.id_persona");

            $stmt->execute();

            return $stmt->fetchAll();
        }
    }

    /*=============================================
	MOSTRAR REPORTE DE COTIZACION DE RANGO DE FECHAS
	=============================================*/
    static public function mdlMostrarReporteCotizacionRangoFechas($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde,    $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
    {

        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaPersona AS p INNER JOIN $tablaVentas AS v ON p.id_persona = c.id_persona	WHERE c.fecha_venta BETWEEN '$fecha_desde' AND '$fecha_hasta' AND c.id_usuario = $id_usuario");

        $stmt->execute();

        return $stmt->fetchAll();
    }


    /*=============================================
	MOSTRAR REPORTE DE VENTAS POR PRECIOS DE PRODUCTOS
	=============================================*/
    static public function mdlMostrarReporteCotizacionPrecioProducto($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde,    $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
    {
        $stmt = Conexion::conectar()->prepare("SELECT $tablaProducto.nombre_producto, $tablaProducto.precio_producto, $tablaDetallec.precio_venta, $tablaVentas.fecha_venta, $tablaUsuario.nombre_usuario FROM $tablaProducto INNER JOIN $tablaDetalleV ON $tablaProducto.id_producto = $tablaDetallec.id_producto INNER JOIN $tablaVentas ON $tablaVentas.id_venta = $tablaDetallec.id_venta INNER JOIN $tablaUsuario ON $tablaUsuario.id_usuario = $tablaVentas.id_usuario WHERE $tablaProducto.precio_producto != $tablaDetallec.precio_venta AND $tablaVentas.id_usuario = $id_usuario");
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /*=============================================
	MOSTRAR DETALLE VENTAS
	=============================================*/

    static public function mdlMostrarListaDetalleCotizacion($tablaDC, $tablaP, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDC as dc INNER JOIN $tablaP as p ON dc.id_producto=p.id_producto WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDC as dc INNER JOIN $tablaP as p ON p.id_producto = dc.id_producto  ORDER BY c.id_cotizacion DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    /*=============================================
	MOSTRAR SERIE Y NUMERO DE COMPRA O EGRESO
	=============================================*/
    static public function mdlMostrarSerieNumero($tablaSerieNumero, $tablaVentas, $item, $valor)
    {
        $stmt = Conexion::conectar()->prepare("	SELECT 
													sn.id_serie_num,
													sn.folio_inicial,
													sn.folio_final, 
													sn.serie_prefijo, 
													c.num_comprobante 
												FROM $tablaSerieNumero AS sn 
												INNER JOIN $tablaVentas AS v 
												ON sn.id_serie_num = c.id_serie_num 
												WHERE sn.id_serie_num = :id_serie_num
												ORDER BY c.id_venta DESC 
												LIMIT 1
											");

        $stmt->bindParam(":id_serie_num", $valor, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Obtenemos un solo registro
            return $resultado; // Retornamos el último registro
        } else {
            print_r($stmt->errorInfo()); // Depurar errores si falla
            return null;
        }
    }

    /*=============================================
	MOSTRAR COTIZACION
	=============================================*/
    static public function mdlMostrarIdCotizacion($tablac, $tablasnc, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tablac WHERE $item = :$item ORDER BY id_cotizacion DESC LIMIT 1");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT c.id_cotizacion, snc.tipo_comprobante_sn from $tablac as c INNER JOIN $tablasnc as snc on c.id_serie_num = snc.id_serie_num ORDER BY c.id_cotizacion DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    /*=============================================
	REGISTRO DE COTIZACION
	=============================================*/
    static public function mdlIngresarCotizacion($tabla, $datos)
    {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                id_persona,
                                                                id_usuario, 
                                                                fecha_cotizacion, 
                                                                hora_cotizacion, 
                                                                id_serie_num, 
                                                                validez,
                                                                impuesto,
                                                                total_cotizacion,
                                                                total_pago,
                                                                sub_total,
                                                                igv_total,
                                                                tipo_pago,
                                                                estado_pago,
																forma_pago
																) 
                                                                VALUES (
                                                                :id_persona,
                                                                :id_usuario, 
                                                                :fecha_cotizacion, 
                                                                :hora_cotizacion, 
                                                                :id_serie_num, 
                                                                :validez,
                                                                :impuesto,
                                                                :total_cotizacion,
                                                                :total_pago,
                                                                :sub_total,
                                                                :igv_total,
                                                                :tipo_pago,
                                                                :estado_pago,
																:forma_pago)");

        $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_cotizacion", $datos["fecha_cotizacion"], PDO::PARAM_STR);
        $stmt->bindParam(":hora_cotizacion", $datos["hora_cotizacion"], PDO::PARAM_STR);
        $stmt->bindParam(":id_serie_num", $datos["id_serie_num"], PDO::PARAM_INT);
        $stmt->bindParam(":validez", $datos["validez"], PDO::PARAM_STR);
        $stmt->bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
        $stmt->bindParam(":total_cotizacion", $datos["total_cotizacion"], PDO::PARAM_STR);
        $stmt->bindParam(":total_pago", $datos["total_pago"], PDO::PARAM_STR);
        $stmt->bindParam(":sub_total", $datos["sub_total"], PDO::PARAM_STR);
        $stmt->bindParam(":igv_total", $datos["igv_total"], PDO::PARAM_STR);
        $stmt->bindParam(":tipo_pago", $datos["tipo_pago"], PDO::PARAM_STR);
        $stmt->bindParam(":estado_pago", $datos["estado_pago"], PDO::PARAM_STR);
        $stmt->bindParam(":forma_pago", $datos["forma_pago"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    /*=============================================
	REGISTRO DETALLE COTIZACION
	=============================================*/
    static public function mdlIngresarDetalleCotizacion($tabla, $datos)
    {
        // Preparar la consulta SQL
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                id_cotizacion,
                                                id_producto, 
                                                numero_javas, 
                                                numero_aves, 
                                                peso_promedio, 
                                                peso_bruto, 
                                                peso_tara, 
                                                peso_merma, 
                                                peso_neto, 
                                                precio_venta) 
                                            VALUES (
                                                :id_cotizacion, 
                                                :id_producto,
                                                :numero_javas, 
                                                :numero_aves, 
                                                :peso_promedio, 
                                                :peso_bruto, 
                                                :peso_tara, 
                                                :peso_merma, 
                                                :peso_neto, 
                                                :precio_venta)");

        // Vincular los parámetros correctamente según el tipo de datos
        $stmt->bindParam(":id_cotizacion", $datos["id_cotizacion"], PDO::PARAM_INT);
        $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);
        $stmt->bindParam(":numero_javas", $datos["numero_javas"], PDO::PARAM_INT);
        $stmt->bindParam(":numero_aves", $datos["numero_aves"], PDO::PARAM_INT);
        // Usar PDO::PARAM_STR para los valores decimales
        $stmt->bindParam(":peso_promedio", $datos["peso_promedio"], PDO::PARAM_STR);
        $stmt->bindParam(":peso_bruto", $datos["peso_bruto"], PDO::PARAM_STR);
        $stmt->bindParam(":peso_tara", $datos["peso_tara"], PDO::PARAM_STR);
        $stmt->bindParam(":peso_merma", $datos["peso_merma"], PDO::PARAM_STR);
        $stmt->bindParam(":peso_neto", $datos["peso_neto"], PDO::PARAM_STR);
        $stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "La cotización se creo correctamente"
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al guardar los datos"
            ];
        }
    }

    /*=============================================
	HISTORIAL DE PAGOS
	=============================================*/
    static public function mdlIngresoHistorialPago($tabla, $datos)
    {
        try {
            // Preparar la consulta SQL
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
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            // Manejo de errores
            return "Error: " . $e->getMessage();
        }
        // Cerrar la conexión
        $stmt = null;
    }

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
        $stmt->bindParam(
            ":id_venta",
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
	EDITAR VENTA
	=============================================*/
    static public function mdlEditarVenta($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_persona = :id_persona, 
																id_usuario = :id_usuario, 
																fecha_venta = :fecha_venta, 
																id_serie_num = :id_serie_num, 
																serie_comprobante = :serie_comprobante, 
																num_comprobante = :num_comprobante, 
																impuesto = :impuesto,
																total_venta = :total_venta,
																total_pago = :total_pago,
																sub_total = :sub_total,
																igv = :igv,
																tipo_pago = :tipo_pago,
																forma_pago = :forma_pago,
																numero_serie_pago = :numero_serie_pago
																WHERE id_venta = :id_venta");

        $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_venta", $datos["fecha_venta"], PDO::PARAM_STR);
        $stmt->bindParam(":id_serie_num", $datos["id_serie_num"], PDO::PARAM_STR);
        $stmt->bindParam(":serie_comprobante", $datos["serie_comprobante"], PDO::PARAM_STR);
        $stmt->bindParam(":num_comprobante", $datos["num_comprobante"], PDO::PARAM_STR);
        $stmt->bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
        $stmt->bindParam(":total_venta", $datos["total_venta"], PDO::PARAM_STR);
        $stmt->bindParam(":total_pago", $datos["total_pago"], PDO::PARAM_STR);
        $stmt->bindParam(":sub_total", $datos["sub_total"], PDO::PARAM_STR);
        $stmt->bindParam(":igv", $datos["igv"], PDO::PARAM_STR);
        $stmt->bindParam(":tipo_pago", $datos["tipo_pago"], PDO::PARAM_STR);
        $stmt->bindParam(":forma_pago", $datos["forma_pago"], PDO::PARAM_STR);
        $stmt->bindParam(":numero_serie_pago", $datos["numero_serie_pago"], PDO::PARAM_STR);
        $stmt->bindParam(":id_venta", $datos["id_venta"], PDO::PARAM_INT);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }


        $stmt = null;
    }

    /*=============================================
	EDITAR DETALLE VENTA
	=============================================*/
    static public function mdlEditarDetalleVenta($tabla, $datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_producto = :id_producto, 
																precio_venta = :precio_venta, 
																cantidad_u = :cantidad_u, 
																cantidad_kg = :cantidad_kg
																WHERE id_venta = :id_venta");

        $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);
        $stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);
        $stmt->bindParam(":cantidad_u", $datos["cantidad_u"], PDO::PARAM_INT);
        $stmt->bindParam(":cantidad_kg", $datos["cantidad_kg"], PDO::PARAM_STR);

        $stmt->bindParam(":id_venta", $datos["id_venta"], PDO::PARAM_INT);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }


        $stmt = null;
    }

    /*=============================================
	ACTUALIZAR VENTA
	=============================================*/
    static public function mdlActualizarVenta($tabla, $item1, $valor1, $item2, $valor2)
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
	ACTUALIZAR STOCK PRODUCTO
	=============================================*/
    static public function mdlActualizarStockProducto($tblProducto, $idProducto, $cantidad)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE $tblProducto SET stock_producto = stock_producto - :cantidad WHERE id_producto = :id_producto");
        $stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    /*=============================================
	BORRAR VENTA
	=============================================*/
    static public function mdlBorrarCotizacion($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_cotizacion = :id_cotizacion");
        $stmt->bindParam(":id_cotizacion", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }

    /*=============================================
	BORRAR DETALLE VENTA
	=============================================*/
    static public function mdlBorrarDetalleCotizacion($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_cotizacion = :id_cotizacion");
        $stmt->bindParam(":id_cotizacion", $datos, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }
}
