<?php

class ControladorVenta
{


	/*=============================================
	MOSTRAR LISTA VENTAS
	=============================================*/

	static public function ctrMostrarListaVentas($item, $valor)
	{
		$tablaD = "detalle_venta";
		$tablaV = "ventas";
		$tablaP = "personas";
		$respuesta = ModeloVenta::mdlMostrarListaVenta($tablaD, $tablaV, $tablaP, $item, $valor);
		return $respuesta;
	}

	/*=============================================
    MOSTRAR SERIE Y NUMERO DE LA VENTA
    =============================================*/
	static public function ctrMostrarSerieNumero($item, $valor, $folioInicial)
	{
		$tablaSerieNumero = "serie_num_comprobante";
		$tablaVentas = "ventas";
		$respuesta = ModeloVenta::mdlMostrarSerieNumero($tablaSerieNumero, $tablaVentas, $item, $valor);

		if ($respuesta) {
			// Obtener el último número de comprobante y el folio final
			$ultimoNumero = $respuesta['num_comprobante'];
			$folioFinal = $respuesta['folio_final'];

			// Incrementar el número de comprobante
			$nuevoNumero = $ultimoNumero + 1;

			// Verificar si el nuevo número excede el folio final
			if ($nuevoNumero <= $folioFinal) {
				return $nuevoNumero;
			} else {
				// Si el número excede el folio final, devolver un mensaje de error
				return "Error: El número de comprobante excede el límite.";
			}
		} else {
			return $folioInicial;
		}
	}


	/*=============================================
	MOSTRAR SUMA TOTAL DE VENTA
	=============================================*/
	static public function ctrMostrarSumaTotalVenta($item, $valor)
	{
		$tablaD = "detalle_venta";
		$tablaV = "ventas";
		$tablaP = "personas";
		$respuesta = ModeloVenta::mdlMostrarSumaTotalVenta($tablaD, $tablaV, $tablaP, $item, $valor);
		return $respuesta;
	}

	
	/*=============================================
	MOSTRAR SUMA TOTAL DE VENTA
	=============================================*/
	static public function ctrMostrarSumaTotalVentaContado($item, $valor)
	{
		$tablaD = "detalle_venta";
		$tablaV = "ventas";
		$tablaP = "personas";
		$respuesta = ModeloVenta::mdlMostrarSumaTotalVentaContado($tablaD, $tablaV, $tablaP, $item, $valor);
		return $respuesta;
	}

	
	/*=============================================
	MOSTRAR SUMA TOTAL DE VENTA
	=============================================*/

	static public function ctrMostrarSumaTotalVentaCredito($item, $valor)
	{
		$tablaD = "detalle_venta";
		$tablaV = "ventas";
		$tablaP = "personas";
		$respuesta = ModeloVenta::mdlMostrarSumaTotalCredito($tablaD, $tablaV, $tablaP, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	MOSTRAR REPORTE VENTAS
	=============================================*/

	static public function ctrMostrarReporteVentas($fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
	{
		$tablaVentas = "ventas";
		$tablaDetalleV = "detalle_venta";
		$tablaProducto = "productos";
		$tablaUsuario = "usuarios";
		$tablaPersona = "personas";
		$respuesta = ModeloVenta::mdlMostrarReporteVenta($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto);
		return $respuesta;
	}

	/*=============================================
	MOSTRAR REPORTE VENTAS RANGO DE FECHAS
	=============================================*/

	static public function ctrMostrarReporteVentasRangoFechas($fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
	{
		$tablaVentas = "ventas";
		$tablaDetalleV = "detalle_venta";
		$tablaProducto = "productos";
		$tablaUsuario = "usuarios";
		$tablaPersona = "personas";
		$respuesta = ModeloVenta::mdlMostrarReporteVentaRangoFechas($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto);
		return $respuesta;
	}

	/*=============================================
	MOSTRAR REPORTE CREDITOS POR CLIENTE
	=============================================*/

	static public function ctrMostrarReporteVentasCreditoCliente($fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto, $id_cliente_reporte)
	{
		$tablaVentas = "ventas";
		$tablaDetalleV = "detalle_venta";
		$tablaProducto = "productos";
		$tablaUsuario = "usuarios";
		$tablaPersona = "personas";
		$respuesta = ModeloVenta::mdlMostrarReporteVentaCreditosCliente($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto, $id_cliente_reporte);
		return $respuesta;
	}

	/*=============================================
	MOSTRAR REPORTE PRECIOS MODIFICADO EN LA VENTA
	=============================================*/

	static public function ctrMostrarReporteVentasPrecioProducto($fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
	{

		$tablaVentas = "ventas";
		$tablaDetalleV = "detalle_venta";
		$tablaProducto = "productos";
		$tablaUsuario = "usuarios";
		$tablaPersona = "personas";

		$respuesta = ModeloVenta::mdlMostrarReporteVentaPrecioProducto($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto);

		return $respuesta;
	}


	/*=============================================
	MOSTRAR DETALLE VENTA
	=============================================*/

	static public function ctrMostrarDetalleVenta($item, $valor)
	{

		$tablaDV = "detalle_venta";
		$tablaP = "productos";

		$respuesta = ModeloVenta::mdlMostrarListaDetalleVenta($tablaDV, $tablaP, $item, $valor);

		return $respuesta;
	}

	/*=============================================
	REGISTRO DE VENTA
	=============================================*/
	static public function ctrCrearVenta()
	{
		$tabla = "ventas";
		$pago_total = 0;
		if ($_POST["tipo_pago"] == "contado") {
			$pago_total = $_POST["total"];
		} else {
			$pago_total = 0;
		}
		$datos = array(
			"id_persona" => $_POST["id_cliente_venta"],
			"id_usuario" => $_POST["id_usuario_venta"],
			"fecha_venta" => $_POST["fecha_venta"],
			"hora_venta" => $_POST["hora_venta"],
			"id_serie_num" => $_POST["comprobante_venta"],
			"serie_comprobante" => $_POST["serie_venta"],
			"num_comprobante" => $_POST["numero_venta"],
			"impuesto" => $_POST["igv_venta"],
			"total_venta" => $_POST["total"],
			"total_pago" => $pago_total,
			"sub_total" => $_POST["subtotal"],
			"igv" => $_POST["igv"],
			"tipo_pago" => $_POST["tipo_pago"],
			"forma_pago" => $_POST["metodos_pago_venta"],
			"numero_serie_pago" => $_POST["serie_de_pago_venta"],
			"pago_delante" => $_POST["pago_cuota_venta"],
			"estado_pago" => $_POST["estado_pago"]
		);

		ModeloVenta::mdlIngresarVenta($tabla, $datos);

		/* MOSTRANDO EL ULTIMO ID INGRESADO */
		$tabla = "ventas";
		$item = null;
		$valor = null;
		$respuestaDetalleVenta = ModeloVenta::mdlMostrarIdVenta($tabla, $item, $valor);
		$id_venta_ultimo = null;
		foreach ($respuestaDetalleVenta as $value) {
			$id_venta_ultimo = $value["id_venta"];
		}

		/* ==========================================
		INGRESO DE DATOS AL DETALLE VENTA
		========================================== */
		$tblDetalleVenta = "detalle_venta";
		$productos = json_decode($_POST["productoAddVenta"], true);
		$datos = array();
		foreach ($productos as $dato) {
			$nuevo_dato = array(
				'id_venta' => $id_venta_ultimo,
				'id_producto' => $dato['id_producto_venta'],
				'numero_javas' => $dato['numero_javas'],
				'numero_aves' => $dato['numero_aves'],
				'peso_promedio' => $dato['peso_promedio'],
				'peso_bruto' => $dato['peso_bruto'],
				'peso_tara' => $dato['peso_tara'],
				'peso_merma' => $dato['peso_merma'],
				'peso_neto' => $dato['peso_neto'],
				'precio_venta' => $dato['precio_venta']
			);
			$datos[] = $nuevo_dato;
			$respuestaDatos = ModeloVenta::mdlIngresarDetalleVenta($tblDetalleVenta, $nuevo_dato);
		}

		/* ==========================================
		ACTUALIZANDO EL STOCK DEL PRODUCTO
		========================================== */
		$tblProducto = "productos";
		$stocks = json_decode($_POST["productoAddVenta"], true);
		foreach ($stocks as $value) {
			$idProducto = $value['id_producto_venta'];
			$cantidad = $value['numero_aves'];
			// Actualizar el stock del producto
			ModeloVenta::mdlActualizarStockProducto($tblProducto, $idProducto, $cantidad);
		}

		$ruta = "../vistas/img/comprobantes/";
		if (isset($_FILES["recibo_de_pago_venta"]["tmp_name"])) {
			$extension = pathinfo($_FILES["recibo_de_pago_venta"]["name"], PATHINFO_EXTENSION);
			$tipos_permitidos = array("jpg", "jpeg", "png", "gif");
			if (in_array(strtolower($extension), $tipos_permitidos)) {
				$nombre_imagen = date("YmdHis") . rand(1000, 9999);
				$ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
				if (move_uploaded_file($_FILES["recibo_de_pago_venta"]["tmp_name"], $ruta_imagen)) {
				} else {
				}
			} else {
			}
		}

		/* ==========================================
		HISTORIAL DE PAGO
		========================================== */
		$tablaHistorialPago = "historial_pagos";
		$datosHistorialPago = array(
			"id_venta" => $id_venta_ultimo,
			"fecha_pago" => $_POST["fecha_venta"] . " " . $_POST["hora_venta"],
			"monto_pago" => (!empty($_POST["pago_cuota_venta"])) ? $_POST["pago_cuota_venta"] : $_POST["total"],
			"tipo_pago" => $_POST["tipo_pago"],
			"forma_pago" => $_POST["metodos_pago_venta"],
			"numero_serie_pago" => !empty($_POST["serie_de_pago_venta"]) ? $_POST["serie_de_pago_venta"] : null,
			"estado_pago" => $_POST["estado_pago"],
			"comprobante_imagen" => isset($_FILES["recibo_de_pago_venta"]["tmp_name"]) ? $ruta_imagen : null
		);


		ModeloVenta::mdlIngresoHistorialPago($tablaHistorialPago, $datosHistorialPago);

		/* ==========================================
		RESPUESTA FINAL
		========================================== */

		if ($respuestaDatos) {
			echo json_encode($respuestaDatos);
		}
	}


	/*=============================================
	ACTUALIZAR EL PAGO DE DEUDA EGRESO
	=============================================*/
	
	static public function ctrActualizarDeudaVenta(){

		$tabla = "ventas";

		$totalPago = number_format($_POST["monto_pagar_venta"], 2, '.', '');

		$datos = array(
			"id_venta" => $_POST["id_venta_pagar"],
			"total_pago" => $totalPago
		);

        $respuesta = ModeloVenta::mdlActualizarPagoPendiente($tabla, $datos);

        if ($respuesta == "ok") {

            echo json_encode($respuesta);

        } else {

            echo json_encode($respuesta);

        }

	}

	/*=============================================
	EDITAR VENTA
	=============================================*/

	static public function ctrEditarVenta()
	{



		$tabla = "ventas";

		$pago_total = 0;

		if($_POST["tipo_pago"] == "contado"){

			$pago_total = $_POST["total"];

		}else{
			$pago_total = 0;
		}

		


		$datos = array(
			"id_venta" => $_POST["edit_id_venta"],
			"id_persona" => $_POST["id_cliente_venta"],
			"id_usuario" => $_POST["id_usuario_venta"],
			"fecha_venta" => $_POST["fecha_venta"],
			"tipo_comprobante" => $_POST["comprobante_venta"],
			"serie_comprobante" => $_POST["serie_venta"],
			"num_comprobante" => $_POST["numero_venta"],
			"impuesto" => $_POST["igv_venta"],
			"total_venta" => $_POST["total"],
			"total_pago" => $pago_total,
			"sub_total" => $_POST["subtotal"],
			"igv" => $_POST["igv"],
			"tipo_pago" => $_POST["tipo_pago"],
			"estado_pago" => $_POST["estado_pago"],
			"pago_e_y" => $_POST["pago_e_y"]
		);

	

		$respuesta = ModeloVenta::mdlEditarVenta($tabla, $datos);


		/* ==========================================
		ACTUALIZANDO LOS DATOS DEL DETALLE PRODUCTO
		========================================== */

		$tblDetalleVenta = "detalle_venta";

		$productos = json_decode($_POST["productoAddVenta"], true);



		$datos = array();

		foreach ($productos as $dato) {
			$nuevo_dato = array(
				"id_venta" => $_POST["edit_id_venta"],
				'id_producto' => $dato['id_producto'],
				'precio_venta' => $dato['precio_venta'],
				'cantidad_u' => $dato['cantidad_u'],
				'cantidad_kg' => $dato['cantidad_kg']
			);

			$datos[] = $nuevo_dato;

			 $respuestaDatos = ModeloVenta::mdlEditarDetalleVenta($tblDetalleVenta, $nuevo_dato);

		}

		/* ==========================================
		ACTUALIZANDO EL STOCK DEL PRODUCTO
		========================================== */

		$tblProducto = "productos";

		$stocks = json_decode($_POST["productoAddVenta"], true);

		foreach ($stocks as $value) {
			
			$idProducto = $value['id_producto'];
			$cantidad = $value['cantidad_u'];

			// Actualizar el stock del producto
			$respStock = ModeloVenta::mdlActualizarStockProducto($tblProducto, $idProducto, $cantidad);
		}




		
		if ($respuestaDatos == "ok") {

            $response = array(
                "mensaje" => "La venta se actualizó con éxito",
                "estado" => "ok"
            );

            echo json_encode($response);

        } else {

            $response = array(
                "mensaje" => "Error al actualizar la venta",
                "estado" => "error"
            );

            echo json_encode($response);

        }


		
	}

	/*=============================================
	BORRAR VENTA
	=============================================*/

	static public function ctrBorrarVenta()
	{

		if (isset($_POST["ventaIdDelete"])) {

			$tablaV = "ventas";

			$datos = $_POST["ventaIdDelete"];

			$respuesta = ModeloVenta::mdlBorrarVenta($tablaV, $datos);


			$tablaD = "detalle_venta";

			$respuestaDetalle = ModeloVenta::mdlBorrarDetalleVenta($tablaD, $datos);

			echo json_encode($respuestaDetalle);


		}
	}
}
