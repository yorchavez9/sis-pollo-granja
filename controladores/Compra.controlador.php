<?php

class ControladorCompra
{

	/*=============================================
	MOSTRAR COMPRA
	=============================================*/
	static public function ctrMostrarCompras($item, $valor)
	{
		$tablaE = "egresos";
		$tablaDE = "detalle_egreso";
		$tablaP = "personas";
		$tablaU = "usuarios";
		$respuesta = ModeloCompra::mdlMostrarCompra($tablaE, $tablaDE, $tablaP, $tablaU, $item, $valor);
		return $respuesta;
	}

	/*=============================================
    MOSTRAR REPORTE DE COMPRAS
    =============================================*/
	public static function ctrReporteCompras()
	{
		$tabla_egresos = "egresos";
		$tabla_personas = "personas";
		$tabla_usuarios = "usuarios";

		// Capturamos los filtros
		$filtros = [
			"filtro_usuario_compra" => isset($_POST['filtro_usuario_compra']) ? $_POST['filtro_usuario_compra'] : null,
			"filtro_fecha_desde_compra" => isset($_POST['filtro_fecha_desde_compra']) ? $_POST['filtro_fecha_desde_compra'] : null,
			"filtro_fecha_hasta_compra" => isset($_POST['filtro_fecha_hasta_compra']) ? $_POST['filtro_fecha_hasta_compra'] : null,
			"filtro_tipo_comprobante_compra" => isset($_POST['filtro_tipo_comprobante_compra']) ? $_POST['filtro_tipo_comprobante_compra'] : null,
			"filtro_estado_pago_compra" => isset($_POST['filtro_estado_pago_compra']) ? $_POST['filtro_estado_pago_compra'] : null,
			"filtro_total_compra_min" => isset($_POST['filtro_total_compra_min']) ? $_POST['filtro_total_compra_min'] : null,
			"filtro_total_compra_max" => isset($_POST['filtro_total_compra_max']) ? $_POST['filtro_total_compra_max'] : null
		];

		// Pasamos los filtros al modelo
		$respuesta = ModeloCompra::mdlReporteCompras($tabla_egresos, $tabla_personas, $tabla_usuarios, $filtros);

		return $respuesta;
	}

	/*=============================================
    MOSTRAR REPORTE DE COMPRAS
    =============================================*/
	public static function ctrReporteComprasPDF()
	{
		$tabla_egresos = "egresos";
		$tabla_personas = "personas";
		$tabla_usuarios = "usuarios";

		// Capturamos los filtros
		$filtros = [
			"filtro_usuario_compra" => isset($_GET['filtro_usuario_compra']) ? $_GET['filtro_usuario_compra'] : null,
			"filtro_fecha_desde_compra" => isset($_GET['filtro_fecha_desde_compra']) ? $_GET['filtro_fecha_desde_compra'] : null,
			"filtro_fecha_hasta_compra" => isset($_GET['filtro_fecha_hasta_compra']) ? $_GET['filtro_fecha_hasta_compra'] : null,
			"filtro_tipo_comprobante_compra" => isset($_GET['filtro_tipo_comprobante_compra']) ? $_GET['filtro_tipo_comprobante_compra'] : null,
			"filtro_estado_pago_compra" => isset($_GET['filtro_estado_pago_compra']) ? $_GET['filtro_estado_pago_compra'] : null,
			"filtro_total_compra_min" => isset($_GET['filtro_total_compra_min']) ? $_GET['filtro_total_compra_min'] : null,
			"filtro_total_compra_max" => isset($_GET['filtro_total_compra_max']) ? $_GET['filtro_total_compra_max'] : null
		];

		// Pasamos los filtros al modelo
		$respuesta = ModeloCompra::mdlReporteCompras($tabla_egresos, $tabla_personas, $tabla_usuarios, $filtros);

		return $respuesta;
	}


	/*=============================================
    MOSTRAR DETALLE COMPRA
    =============================================*/
	static public function ctrMostrarDetalleCompra($item, $valor)
	{

		$tablaDE = "detalle_egreso";
		$tablaP = "productos";

		$respuesta = ModeloCompra::mdlMostrarListaDetalleCompra($tablaDE, $tablaP, $item, $valor);

		return $respuesta;
	}


	/*=============================================
	MOSTRAR TOTAL DE COMPRAS
	=============================================*/
	static public function ctrMostrarTotalComprasCantidad($item, $valor)
	{
		$tablaE = "egresos";
		$tablaDE = "detalle_egreso";
		$respuesta = ModeloCompra::mdlMostrarCompraTotalCantidad($tablaE, $tablaDE, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	MOSTRAR COMPRA
	=============================================*/
	static public function ctrMostrarTotalCompra($item, $valor)
	{
		$tablaE = "egresos";
		$tablaDE = "detalle_egreso";
		$respuesta = ModeloCompra::mdlMostrarTotalCompras($tablaE, $tablaDE, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	MOSTRAR COMPRA
	=============================================*/
	static public function ctrMostrarEgreso($item, $valor)
	{
		$tabla = "egresos";
		$respuesta = ModeloCompra::mdlMostrarEgreso($tabla, $item, $valor);
		return $respuesta;
	}


	/*=============================================
	REGISTRO DE COMPRA
	=============================================*/
	static public function ctrCrearCompra()
	{
		$tabla = "egresos";
		$pago_total = 0;
		if($_POST["tipo_pago"] == "contado"){
			$pago_total = $_POST["total"];
		}else{
			$pago_total = 0;
		}

		$datos = array(
			"id_persona" => $_POST["id_proveedor_egreso"],
			"id_usuario" => $_POST["id_usuario_egreso"],
			"fecha_egre" => $_POST["fecha_egreso"],
			"hora_egreso" => $_POST["hora_egreso"],
			"tipo_comprobante" => $_POST["tipo_comprobante_egreso"],
			"serie_comprobante" => $_POST["serie_comprobante"],
			"num_comprobante" => $_POST["num_comprobante"],
			"impuesto" => $_POST["impuesto_egreso"],
			"total_compra" => $_POST["total"],
			"total_pago" => $pago_total,
			"subTotal" => $_POST["subtotal"],
			"igv" => $_POST["igv"],
			"tipo_pago" => $_POST["tipo_pago"],
			"estado_pago" => $_POST["estado_pago"],
			"pago_e_y" => $_POST["pago_e_y"]
		);

		ModeloCompra::mdlIngresarCompra($tabla, $datos);

		/* MOSTRANDO EL ULTIMO ID INGRESADO */
		$tabla = "egresos";
		$item = null;
		$valor = null;
		$respuestaDetalleEgreso = ModeloCompra::mdlMostrarEgreso($tabla, $item, $valor);
		foreach ($respuestaDetalleEgreso as $value) {
			$id_egreso_ultimo = $value["id_egreso"];
		}

		/* ==========================================
		INGRESO DE DATOS AL DETALLE EGRESO
		========================================== */
		$tablaDetalleEgreso = "detalle_egreso";
		$productos = json_decode($_POST["productoAddEgreso"], true);
		$datos = array();

		// Recorrer productos y preparar datos
		foreach ($productos as $dato) {
			$datos[] = array(
				'id_egreso' => $id_egreso_ultimo,
				'id_producto' => $dato['idProductoEgreso'],
				'numero_javas' => $dato['numero_javas'],
				'numero_aves' => $dato['numero_aves'],
				'peso_promedio' => $dato['peso_promedio'],
				'peso_bruto' => $dato['peso_bruto'],
				'peso_tara' => $dato['peso_tara'],
				'peso_merma' => $dato['peso_merma'],
				'peso_neto' => $dato['peso_neto'],
				'precio_compra' => $dato['precio_compra'],
				'precio_venta' => $dato['precio_venta']
			);
		}

		// Insertar todos los datos en una sola operación
		$respuestaDatos = ModeloCompra::mdlGuardarDetalleCompra($tablaDetalleEgreso, $datos);



		/* ==========================================
		ACTUALIZANDO EL STOCK DEL PRODUCTO
		========================================== */

		$tblProducto = "productos";
		$stocks = json_decode($_POST["productoAddEgreso"], true);

		foreach ($stocks as $value) {
			$idProducto = $value['idProductoEgreso'];
			$cantidad = $value['numero_aves'];
			$precio = $value['precio_venta'];

			// Actualizar el stock del producto
			ModeloCompra::mdlActualizarStockProducto($tblProducto, $idProducto, $cantidad, $precio);
		}

		if ($respuestaDatos == "ok") {
            $response = array(
                "mensaje" => "Producto guardado correctamente",
                "estado" => "ok",
				"id_egreso" => $id_egreso_ultimo,
				"tipo_documento" => $_POST["tipo_comprobante_egreso"]
            );
            echo json_encode($response);
        } else {
            $response = array(
                "mensaje" => "Error al guardar el producto",
                "estado" => "error"
            );
            echo json_encode($response);
        }
	}


	/*=============================================
	BORRAR COMPRA
	=============================================*/
	static public function ctrBorrarCompra()
	{

		if (isset($_POST["idEgresoDelete"])) {
			$tabla = "egresos";
			$datos = $_POST["idEgresoDelete"];
			$respuesta = ModeloCompra::mdlBorrarCompra($tabla, $datos);
			if ($respuesta == "ok") {
				echo json_encode("ok");
			}
		}
	}
}
