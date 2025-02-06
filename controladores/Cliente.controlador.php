<?php

class ControladorCliente
{

	/*=============================================
	REGISTRO DE CLIENTE
	=============================================*/
	static public function ctrCrearCliente()
	{

		$tabla = "personas";
		$datos = array(
			"tipo_persona" => $_POST["tipo_persona"],
			"razon_social" => $_POST["razon_social"],
			"id_doc" => $_POST["id_doc"],
			"numero_documento" => $_POST["numero_documento"],
			"direccion" => $_POST["direccion"],
			"ciudad" => $_POST["ciudad"],
			"codigo_postal" => $_POST["codigo_postal"],
			"telefono" => $_POST["telefono"],
			"email" => $_POST["email"],
			"sitio_web" => $_POST["sitio_web"],
			"tipo_banco" => $_POST["tipo_banco"],
			"numero_cuenta" => $_POST["numero_cuenta"]
		);

		$respuesta = ModeloCliente::mdlIngresarCliente($tabla,	$datos);

		if ($respuesta == "ok") {
			echo json_encode("ok");
		} else {
			echo json_encode("error");
		}
	}

	/*=============================================
	MOSTRAR CLIENTE
	=============================================*/

	static public function ctrMostrarCliente($item, $valor)
	{
		$tablaDoc = "tipo_documentos";
		$tablaPer = "personas";
		$respuesta = ModeloCliente::mdlMostrarCliente($tablaDoc, $tablaPer, $item, $valor);
		return $respuesta;
	}
	/*=============================================
	MOSTRAR CLIENTE
	=============================================*/

	static public function ctrMostrarReporteClientesLista()
	{
		$respuesta = ModeloCliente::mdlMostrarClienteVentas();
		return $respuesta;
	}

	/*=============================================
	MOSTRAR REPORTE CLIENTE VENTAS
	=============================================*/
	static public function ctrMostrarReporteClientes()
	{

		// Capturamos los filtros
		$filtros = [
			"id_cliente" => isset($_POST['id_cliente']) ? $_POST['id_cliente'] : null,
			"fecha_desde" => isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null,
			"fecha_hasta" => isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null,
			"tipo_venta" => isset($_POST['tipo_venta']) ? $_POST['tipo_venta'] : null
		];

		// Pasamos los filtros al modelo
		$respuesta = ModeloCliente::mdlReporteClientesVenta($filtros);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR REPORTE CLIENTE VENTAS
	=============================================*/
	static public function ctrMostrarReporteClientesPDF()
	{

		// Capturamos los filtros
		$filtros = [
			"id_cliente" => isset($_GET['id_cliente']) ? $_GET['id_cliente'] : null,
			"fecha_desde" => isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : null,
			"fecha_hasta" => isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : null,
			"tipo_venta" => isset($_GET['tipo_venta']) ? $_GET['tipo_venta'] : null
		];

		// Pasamos los filtros al modelo
		$respuesta = ModeloCliente::mdlReporteClienteVentaPDF($filtros);

		return $respuesta;
	}


	/*=============================================
	MOSTRAR TOTAL DE CLIENTES
	=============================================*/
	static public function ctrMostrarTotalCliente($item, $valor)
	{
		$tablaDoc = "tipo_documentos";
		$tablaPer = "personas";
		$respuesta = ModeloCliente::mdlMostrarTotalClientes($tablaDoc, $tablaPer, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	EDITAR CLIENTE
	=============================================*/
	static public function ctrEditarCliente()
	{
		$tabla = "personas";
		$datos = array(
			"id_persona" => $_POST["edit_id_cliente"],
			"razon_social" => $_POST["edit_razon_social"],
			"id_doc" => $_POST["edit_id_doc"],
			"numero_documento" => $_POST["edit_numero_documento"],
			"direccion" => $_POST["edit_direccion"],
			"ciudad" => $_POST["edit_ciudad"],
			"codigo_postal" => $_POST["edit_codigo_postal"],
			"telefono" => $_POST["edit_telefono"],
			"email" => $_POST["edit_email"],
			"sitio_web" => $_POST["edit_sitio_web"],
			"tipo_banco" => $_POST["edit_tipo_banco"],
			"numero_cuenta" => $_POST["edit_numero_cuenta"]
		);
		$respuesta = ModeloCliente::mdlEditarCliente($tabla, $datos);
		if ($respuesta == "ok") {
			echo json_encode("ok");
		}
	}

	/*=============================================
	BORRAR CLIENTE
	=============================================*/
	static public function ctrBorraCliente()
	{
		if (isset($_POST["deleteIdCliente"])) {
			$tabla = "personas";
			$datos = $_POST["deleteIdCliente"];
			$respuesta = ModeloCliente::mdlBorrarCliente($tabla, $datos);
			if ($respuesta == "ok") {
				echo json_encode("ok");
			}
		}
	}
}
