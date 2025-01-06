<?php


class ControladorVenta
{


	/*=============================================
	MOSTRAR LISTA VENTAS
	=============================================*/

	static public function ctrMostrarListaVentas($item, $valor)
	{
		$tabla_personas = "personas";
		$tabla_ventas = "ventas";
		$tabla_usuarios = "usuarios";
		$tabla_s_n = "serie_num_comprobante";
		$respuesta = ModeloVenta::mdlMostrarListaVenta($tabla_personas, $tabla_ventas, $tabla_usuarios,$tabla_s_n, $item, $valor);
		return $respuesta;
	}

	/*=============================================
    MOSTRAR REPORTE DE VENTAS
    =============================================*/
	public static function ctrReporteVentas()
	{
		$tabla_personas = "personas";
		$tabla_ventas = "ventas";
		$tabla_usuarios = "usuarios";
		$tabla_s_n = "serie_num_comprobante";

		// Capturamos los filtros
		$filtros = [
			"filtro_usuario_venta" => isset($_POST['filtro_usuario_venta']) ? $_POST['filtro_usuario_venta'] : null,
			"filtro_fecha_desde_venta" => isset($_POST['filtro_fecha_desde_venta']) ? $_POST['filtro_fecha_desde_venta'] : null,
			"filtro_fecha_hasta_venta" => isset($_POST['filtro_fecha_hasta_venta']) ? $_POST['filtro_fecha_hasta_venta'] : null,
			"filtro_tipo_comprobante_venta" => isset($_POST['filtro_tipo_comprobante_venta']) ? $_POST['filtro_tipo_comprobante_venta'] : null,
			"filtro_estado_pago_venta" => isset($_POST['filtro_estado_pago_venta']) ? $_POST['filtro_estado_pago_venta'] : null,
			"filtro_total_venta_min" => isset($_POST['filtro_total_venta_min']) ? $_POST['filtro_total_venta_min'] : null,
			"filtro_total_venta_max" => isset($_POST['filtro_total_venta_max']) ? $_POST['filtro_total_venta_max'] : null
		];

		// Pasamos los filtros al modelo
		$respuesta = ModeloVenta::mdlReporteVentas($tabla_personas, $tabla_ventas, $tabla_usuarios, $tabla_s_n, $filtros);

		return $respuesta;
	}

	/*=============================================
    MOSTRAR REPORTE DE VENTAS PDF
    =============================================*/
	public static function ctrReporteVentasPDF()
	{
		$tabla_personas = "personas";
		$tabla_ventas = "ventas";
		$tabla_usuarios = "usuarios";
		$tabla_s_n = "serie_num_comprobante";

		// Capturamos los filtros
		$filtros = [
			"filtro_usuario_venta" => isset($_GET['filtro_usuario_venta']) ? $_GET['filtro_usuario_venta'] : null,
			"filtro_fecha_desde_venta" => isset($_GET['filtro_fecha_desde_venta']) ? $_GET['filtro_fecha_desde_venta'] : null,
			"filtro_fecha_hasta_venta" => isset($_GET['filtro_fecha_hasta_venta']) ? $_GET['filtro_fecha_hasta_venta'] : null,
			"filtro_tipo_comprobante_venta" => isset($_GET['filtro_tipo_comprobante_venta']) ? $_GET['filtro_tipo_comprobante_venta'] : null,
			"filtro_estado_pago_venta" => isset($_GET['filtro_estado_pago_venta']) ? $_GET['filtro_estado_pago_venta'] : null,
			"filtro_total_venta_min" => isset($_GET['filtro_total_venta_min']) ? $_GET['filtro_total_venta_min'] : null,
			"filtro_total_venta_max" => isset($_GET['filtro_total_venta_max']) ? $_GET['filtro_total_venta_max'] : null
		];

		// Pasamos los filtros al modelo
		$respuesta = ModeloVenta::mdlReporteVentas($tabla_personas, $tabla_ventas, $tabla_usuarios, $tabla_s_n, $filtros);

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
				'id_producto' => $dato['id_producto'],
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
			$idProducto = $value['id_producto'];
			$cantidad = $value['numero_aves'];
			// Actualizar el stock del producto
			ModeloVenta::mdlActualizarStockProducto($tblProducto, $idProducto, $cantidad);
		}

		/* =========================================
        ACTUALIZANDO EL MONTO DE VENTA DEL PAGO AL CREDITO
        ========================================= */
		$tablaActualizarVenta = "ventas";
		$totalPago = $_POST["pago_cuota_venta"]; 
		if (!empty($totalPago) || $totalPago == null || $pago_total = '') {
			$datosAdelanto = array(
				"id_venta" => $id_venta_ultimo,
				"total_pago" => $totalPago
			);

			ModeloVenta::mdlActualizarPagoPendiente($tablaActualizarVenta, $datosAdelanto);

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
			"monto_pago" => (!empty($_POST["pago_cuota_venta"])) ? $_POST["pago_cuota_venta"] : $_POST["total"],
			"forma_pago" => $_POST["metodos_pago_venta"],
			"numero_serie_pago" => !empty($_POST["serie_de_pago_venta"]) ? $_POST["serie_de_pago_venta"] : null,
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
