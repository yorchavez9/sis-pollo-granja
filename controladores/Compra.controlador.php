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
		$respuesta = ModeloCompra::mdlMostrarCompra($tablaE, $tablaDE, $item, $valor);
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
	MOSTRAR SERIE NUMERO COMPRA
	=============================================*/
	static public function ctrMostrarSerieNumero($item, $valor)
	{
		$tabla = "egresos";
		$respuesta = ModeloCompra::mdlMostrarSerieNumero($tabla, $item, $valor);
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

		foreach ($productos as $dato) {
			$nuevo_dato = array(
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

			$datos[] = $nuevo_dato;
			$respuestaDatos = ModeloCompra::mdlIngresarDetalleCompra($tablaDetalleEgreso, $nuevo_dato);

		}

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
