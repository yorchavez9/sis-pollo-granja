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
                        p.id_persona,
                        u.id_usuario,
                        c.fecha_cotizacion, 
                        c.hora_cotizacion,
                        sn.id_serie_num, 
                        c.impuesto, 
                        c.total_cotizacion,
                        c.total_pago, 
                        c.sub_total,
                        c.igv_total, 
                        c.tipo_pago, 
                        c.forma_pago,
                        p.razon_social,
                        p.numero_documento,
                        p.direccion,
                        p.telefono,
                        p.email
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
	MOSTRAR DETALLE COTIZACION PARA LA VENTA    
	=============================================*/

    static public function mdlMostrarListaDetalleCotizacionVenta($tablaDC, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDC WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDC  ORDER BY c.id_cotizacion DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }


    /*=============================================
	MOSTRAR DETALLE COTIZACION
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
	ACTUALIZAR ESTADOS DE LA COTIZACION
	=============================================*/
    static public function mdlActualizarCotizacion($tabla, $item1, $valor1, $item2,
        $valor2
    ) {
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
